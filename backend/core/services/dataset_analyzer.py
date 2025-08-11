import requests
import io
import csv
import json
import re
import pyaxis  # <-- añadido para soporte pc-axis
from dateutil import parser as dateparser  # pip install python-dateutil
from typing import List, Dict, Any


# (Lista de comunidades usada para detección geo)
SPANISH_COMMUNITIES = [
    "andalucía", "andalucia", "aragón", "aragon", "asturias", "islas baleares",
    "baleares", "canarias", "cantabria", "castilla-la mancha", "castilla y león",
    "castilla y leon", "cataluña", "cataluna", "comunidad valenciana", "valenciana",
    "extremadura", "galicia", "la rioja", "madrid", "murcia", "navarra", "navarra",
    "país vasco", "pais vasco", "paisvasco", "ceuta", "melilla"
]


def safe_text(s):
    if s is None:
        return ""
    return str(s).strip()


def try_parse_number(v):
    try:
        if v is None or v == "":
            return None
        v2 = re.sub(r"[^\d\-\.,eE]", "", str(v))
        if v2.count(",") > 0 and v2.count(".") == 0:
            v2 = v2.replace(",", ".")
        return float(v2)
    except Exception:
        return None


def looks_like_date(s):
    if s is None or s == "":
        return False
    try:
        dateparser.parse(str(s))
        return True
    except Exception:
        return False


def find_first_list_in_json(obj):
    if isinstance(obj, list):
        return obj
    if isinstance(obj, dict):
        for k in ("data", "rows", "result", "results", "records", "items"):
            if k in obj and isinstance(obj[k], list):
                return obj[k]
        for v in obj.values():
            if isinstance(v, list):
                return v
    return None


# -----------------------
# Normalización / flatten
# -----------------------

def auto_normalize_key(key: str) -> str:
    if key is None:
        return ""
    s = str(key)
    s = re.sub(r'\bT\d+[_\-]?', ' ', s, flags=re.IGNORECASE)
    s = re.sub(r'[_\-]+', ' ', s)
    s = re.sub(r'([a-z])([A-Z])', r'\1 \2', s)
    s = re.sub(r'[^0-9A-Za-z\u00C0-\u017F ]+', ' ', s)
    s = re.sub(r'\s+', ' ', s).strip()
    parts = [p.capitalize() for p in s.split()]
    normalized = " ".join(parts)
    return normalized


def _flatten(obj: Any, prefix: str = "", out: Dict[str, Any] = None, max_list_items: int = 3):
    if out is None:
        out = {}
    if isinstance(obj, dict):
        for k, v in obj.items():
            nk = auto_normalize_key(k)
            new_prefix = f"{prefix} {nk}".strip()
            _flatten(v, new_prefix, out, max_list_items)
    elif isinstance(obj, list):
        if all(not isinstance(x, (dict, list)) for x in obj):
            joined = ", ".join([safe_text(x) for x in obj[:max_list_items]])
            if len(obj) > max_list_items:
                joined += ", ..."
            out[prefix or "value"] = joined
        else:
            for idx, elem in enumerate(obj[:max_list_items]):
                new_prefix = f"{prefix} [{idx}]".strip()
                _flatten(elem, new_prefix, out, max_list_items)
            if len(obj) > max_list_items:
                out[f"{prefix} (more)"] = f"{len(obj)} items"
    else:
        out[prefix or "value"] = obj
    return out


def flatten_row(row: Dict[str, Any]) -> Dict[str, str]:
    if not isinstance(row, dict):
        return {"Value": safe_text(row)}
    flat = {}
    for k, v in row.items():
        nk = auto_normalize_key(k)
        _flatten(v, nk, flat)
    return {k: safe_text(v) for k, v in flat.items()}


def normalize_rows(rows: List[Dict[str, Any]]) -> List[Dict[str, str]]:
    flat_rows = [flatten_row(r) for r in rows]
    all_cols = []
    for r in flat_rows:
        for k in r.keys():
            if k not in all_cols:
                all_cols.append(k)
    normalized = []
    for r in flat_rows:
        newr = {col: r.get(col, "") for col in all_cols}
        normalized.append(newr)
    return normalized


# -----------------------
# Sampling CSV / JSON / PC-AXIS
# -----------------------

def sample_csv_from_url(url, max_rows=100, max_lines=1000, timeout=30):
    resp = requests.get(url, stream=True, timeout=timeout)
    resp.raise_for_status()
    lines = []
    it = resp.iter_lines(decode_unicode=True)
    count_lines = 0
    try:
        while count_lines < max_lines:
            line = next(it)
            if line is None:
                continue
            lines.append(line)
            count_lines += 1
            if len(lines) >= max_rows + 20 and count_lines >= 50:
                break
    except StopIteration:
        pass
    text = "\n".join(lines)
    f = io.StringIO(text)
    try:
        reader = csv.DictReader(f)
        rows = []
        for r in reader:
            rows.append({k: safe_text(v) for k, v in r.items()})
            if len(rows) >= max_rows:
                break
        return rows
    except Exception:
        return []


def sample_json_from_url(url, max_rows=100, timeout=30):
    resp = requests.get(url, timeout=timeout)
    resp.raise_for_status()
    data = resp.json()
    arr = find_first_list_in_json(data)
    if arr is None:
        if isinstance(data, list):
            arr = data
        else:
            return []
    rows = []
    for item in arr[:max_rows]:
        if isinstance(item, dict):
            rows.append(item)
        else:
            rows.append({"value": item})
    return rows


def sample_pcaxis_from_url(url, max_rows=100, timeout=30):
    """
    Lee un archivo PC-Axis (.px) y lo convierte a lista de dicts.
    Requiere: pip install pyaxis
    """
    resp = requests.get(url, timeout=timeout)
    resp.raise_for_status()
    # pyaxis.parse devuelve un pandas DataFrame
    df = pyaxis.parse(io.BytesIO(resp.content), encoding="utf-8")
    rows = df.to_dict(orient="records")
    return rows[:max_rows]


# -----------------------
# Schema inference
# -----------------------

def infer_schema_from_rows(rows: List[Dict[str, str]], sample_limit=20):
    schema = []
    if not rows:
        return schema
    cols = list(rows[0].keys())
    for col in cols:
        values = [safe_text(r.get(col, "")) for r in rows[:sample_limit]]
        non_empty = [v for v in values if v != "" and v is not None]
        inferred = "string"
        num_count = 0
        date_count = 0
        lat_count = 0
        lon_count = 0
        unique_vals = set()
        for v in non_empty:
            unique_vals.add(v.lower())
            if try_parse_number(v) is not None:
                num_count += 1
                try:
                    num = try_parse_number(v)
                    if -90 <= num <= 90:
                        lat_count += 1
                    if -180 <= num <= 180:
                        lon_count += 1
                except Exception:
                    pass
            if looks_like_date(v):
                date_count += 1
        if len(non_empty) > 0:
            if num_count / len(non_empty) >= 0.8:
                inferred = "numeric"
            elif date_count / len(non_empty) >= 0.6:
                inferred = "datetime"
            else:
                inferred = "string"
        lowname = col.lower()
        if any(k in lowname for k in ("lat", "latitude", "latitud")):
            inferred = "latitude"
        if any(k in lowname for k in ("lon", "lng", "longitude", "longitud")):
            inferred = "longitude"
        lower_vals = [v.lower() for v in non_empty[:min(30, len(non_empty))]]
        matches = sum(1 for v in lower_vals for c in SPANISH_COMMUNITIES if c in v)
        if len(non_empty) > 0 and matches / len(lower_vals) >= 0.2:
            inferred = "geo_name"
        schema.append({
            "name": col,
            "inferred_type": inferred,
            "sample_values": non_empty[:5],
            "unique_count_estimate": len(unique_vals)
        })
    return schema


# -----------------------
# Suggestions builder
# -----------------------

def build_suggestions(schema):
    suggestions = []
    numeric_cols = [c["name"] for c in schema if c["inferred_type"] == "numeric"]
    datetime_cols = [c["name"] for c in schema if c["inferred_type"] == "datetime"]
    lat_cols = [c["name"] for c in schema if c["inferred_type"] == "latitude"]
    lon_cols = [c["name"] for c in schema if c["inferred_type"] == "longitude"]
    geo_name_cols = [c["name"] for c in schema if c["inferred_type"] == "geo_name"]
    categorical_candidates = [c["name"] for c in schema if c["inferred_type"] == "string" and c["unique_count_estimate"] < 200]

    for dcol in datetime_cols:
        for ncol in numeric_cols:
            suggestions.append({
                "type": "timeseries",
                "title": f"Time series: {ncol} by {dcol}",
                "x": dcol,
                "y": ncol
            })
    for cat in categorical_candidates:
        for ncol in numeric_cols:
            suggestions.append({
                "type": "barchart",
                "title": f"Bar chart by {cat} ({ncol})",
                "category": cat,
                "value": ncol
            })
            suggestions.append({
                "type": "piechart",
                "title": f"Pie chart by {cat} ({ncol})",
                "category": cat,
                "value": ncol
            })
    if lat_cols and lon_cols:
        suggestions.append({
            "type": "heatmap",
            "title": f"Heatmap by coords ({lat_cols[0]} / {lon_cols[0]})",
            "lat": lat_cols[0],
            "lon": lon_cols[0]
        })
    if geo_name_cols and numeric_cols:
        for ncol in numeric_cols:
            suggestions.append({
                "type": "choropleth",
                "title": f"Geo values ({geo_name_cols[0]} - {ncol})",
                "geo_name": geo_name_cols[0],
                "value": ncol
            })
    suggestions.append({"type": "table", "title": "Show table (raw view)"})
    return suggestions


# -----------------------
# Analyze distribution url
# -----------------------

def analyze_distribution_url(url: str, format_override: str = None, sample_rows=80):
    fmt = None
    if format_override:
        fmt = format_override.lower()
    else:
        if url.lower().endswith(".csv") or "rows.csv" in url or "format=csv" in url:
            fmt = "csv"
        elif url.lower().endswith(".json") or "rows.json" in url or "format=json" in url:
            fmt = "json"
        elif url.lower().endswith(".px") or "pc-axis" in url.lower() or "format=pc-axis" in url.lower():
            fmt = "pc-axis"  # <--- soporte pc-axis
        elif "rdf" in url or "xml" in url:
            fmt = "rdf"
        else:
            fmt = None

    rows = []
    last_format_used = None

    if fmt == "csv":
        rows = sample_csv_from_url(url, max_rows=sample_rows)
        last_format_used = "csv"

    elif fmt == "json":
        rows = sample_json_from_url(url, max_rows=sample_rows)
        last_format_used = "json"

    elif fmt == "pc-axis":  # <--- nuevo bloque
        rows = sample_pcaxis_from_url(url, max_rows=sample_rows)
        last_format_used = "pc-axis"

    else:
        # fallback: probar csv luego json
        try:
            rows = sample_csv_from_url(url, max_rows=sample_rows)
            if rows:
                last_format_used = "csv"
            else:
                rows = sample_json_from_url(url, max_rows=sample_rows)
                last_format_used = "json"
        except Exception:
            try:
                rows = sample_json_from_url(url, max_rows=sample_rows)
                last_format_used = "json"
            except Exception as e:
                raise RuntimeError(f"Could not sample CSV/JSON/PC-AXIS from url: {e}")

    normalized = normalize_rows(rows) if rows else []
    schema = infer_schema_from_rows(normalized, sample_limit=30)
    suggestions = build_suggestions(schema)

    return {
        "format_detected": last_format_used,
        "schema": schema,
        "sample_rows": normalized[:min(len(normalized), 200)],
        "suggestions": suggestions,
        "sample_rows_count": len(normalized)
    }
