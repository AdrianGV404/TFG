import requests
import io
import csv
import json
import re
from dateutil import parser as dateparser
from typing import List, Dict

SPANISH_COMMUNITIES = [
    "andalucía", "andalucia", "aragón", "aragon", "asturias", "islas baleares",
    "baleares", "canarias", "cantabria", "castilla-la mancha", "castilla y león",
    "castilla y leon", "cataluña", "cataluna", "comunidad valenciana", "valenciana",
    "extremadura", "galicia", "la rioja", "madrid", "murcia", "navarra",
    "país vasco", "pais vasco", "paisvasco", "ceuta", "melilla"
]

def safe_text(s):
    return "" if s is None else str(s).strip()

def try_parse_number(v):
    try:
        if not v:
            return None
        v2 = re.sub(r"[^\d\-\.,eE]", "", str(v))
        if v2.count(",") > 0 and v2.count(".") == 0:
            v2 = v2.replace(",", ".")
        return float(v2)
    except Exception:
        return None

def looks_like_date(s):
    if not s:
        return False
    try:
        dateparser.parse(s)
        return True
    except Exception:
        return False

def find_first_list_in_json(obj):
    if isinstance(obj, list):
        return obj
    if isinstance(obj, dict):
        for k in ("data", "rows", "result", "results", "records", "items"):
            if isinstance(obj.get(k), list):
                return obj[k]
        for v in obj.values():
            if isinstance(v, list):
                return v
    return None

def sample_csv_from_url(url, max_rows=100, max_lines=1000, timeout=30):
    try:
        resp = requests.get(url, stream=True, timeout=timeout)
        resp.raise_for_status()
    except requests.RequestException as e:
        raise RuntimeError(f"Error al descargar CSV: {e}")

    lines = []
    for i, line in enumerate(resp.iter_lines(decode_unicode=True)):
        if line:
            lines.append(line)
        if i >= max_lines or len(lines) >= max_rows + 20:
            break

    try:
        reader = csv.DictReader(io.StringIO("\n".join(lines)))
        rows = [{k: safe_text(v) for k, v in r.items()} for r in reader]
        return rows[:max_rows]
    except Exception:
        return []

def sample_json_from_url(url, max_rows=100, timeout=30):
    try:
        resp = requests.get(url, timeout=timeout)
        resp.raise_for_status()
        data = resp.json()
    except requests.RequestException as e:
        raise RuntimeError(f"Error al descargar JSON: {e}")
    except ValueError:
        return []

    arr = find_first_list_in_json(data) or (data if isinstance(data, list) else [])
    rows = []
    for item in arr[:max_rows]:
        if isinstance(item, dict):
            rows.append({k: safe_text(v) if not isinstance(v, (dict, list)) else json.dumps(v) for k, v in item.items()})
        else:
            rows.append({"value": safe_text(item)})
    return rows

def infer_schema_from_rows(rows: List[Dict[str,str]], sample_limit=20):
    schema = []
    if not rows:
        return schema
    for col in rows[0].keys():
        values = [safe_text(r.get(col, "")) for r in rows[:sample_limit]]
        non_empty = [v for v in values if v]
        inferred = "string"
        num_count = sum(1 for v in non_empty if try_parse_number(v) is not None)
        date_count = sum(1 for v in non_empty if looks_like_date(v))
        unique_vals = set(v.lower() for v in non_empty)

        if non_empty:
            if num_count / len(non_empty) >= 0.8:
                inferred = "numeric"
            elif date_count / len(non_empty) >= 0.6:
                inferred = "datetime"

        lowname = col.lower()
        if any(k in lowname for k in ("lat", "latitude", "latitud")):
            inferred = "latitude"
        if any(k in lowname for k in ("lon", "lng", "longitude", "longitud")):
            inferred = "longitude"

        lower_vals = [v.lower() for v in non_empty[:30]]
        matches = sum(1 for v in lower_vals for c in SPANISH_COMMUNITIES if c in v)
        if non_empty and matches / len(lower_vals) >= 0.2:
            inferred = "geo_name"

        schema.append({
            "name": col,
            "inferred_type": inferred,
            "sample_values": non_empty[:5],
            "unique_count_estimate": len(unique_vals)
        })
    return schema

def build_suggestions(schema):
    suggestions = []
    numeric_cols = [c["name"] for c in schema if c["inferred_type"] == "numeric"]
    datetime_cols = [c["name"] for c in schema if c["inferred_type"] == "datetime"]
    lat_cols = [c["name"] for c in schema if c["inferred_type"] == "latitude"]
    lon_cols = [c["name"] for c in schema if c["inferred_type"] == "longitude"]
    geo_name_cols = [c["name"] for c in schema if c["inferred_type"] == "geo_name"]
    categorical_cols = [c["name"] for c in schema if c["inferred_type"] == "string" and c["unique_count_estimate"] < 200]

    for dcol in datetime_cols:
        for ncol in numeric_cols:
            suggestions.append({"type": "timeseries", "title": f"Serie temporal: {ncol} por {dcol}", "x": dcol, "y": ncol})
    for cat in categorical_cols:
        for ncol in numeric_cols:
            suggestions.append({"type": "barchart", "title": f"Bar chart por {cat} ({ncol})", "category": cat, "value": ncol})
            suggestions.append({"type": "piechart", "title": f"Pie chart por {cat} ({ncol})", "category": cat, "value": ncol})
    if lat_cols and lon_cols:
        suggestions.append({"type": "heatmap", "title": f"Heatmap por coordenadas ({lat_cols[0]} / {lon_cols[0]})", "lat": lat_cols[0], "lon": lon_cols[0]})
    if geo_name_cols and numeric_cols:
        for ncol in numeric_cols:
            suggestions.append({"type": "choropleth", "title": f"Valor por unidad geográfica ({geo_name_cols[0]} - {ncol})", "geo_name": geo_name_cols[0], "value": ncol})
    suggestions.append({"type": "table", "title": "Mostrar tabla (vista cruda)"})
    return suggestions

def analyze_distribution_url(url: str, format_override: str = None, sample_rows=80):
    fmt = (format_override or "").lower() or None
    rows = []
    last_format_used = None

    if fmt == "csv":
        rows = sample_csv_from_url(url, max_rows=sample_rows)
        last_format_used = "csv"
    elif fmt == "json":
        rows = sample_json_from_url(url, max_rows=sample_rows)
        last_format_used = "json"
    else:
        try:
            rows = sample_csv_from_url(url, max_rows=sample_rows)
            if rows:
                last_format_used = "csv"
            else:
                rows = sample_json_from_url(url, max_rows=sample_rows)
                last_format_used = "json"
        except Exception:
            rows = sample_json_from_url(url, max_rows=sample_rows)
            last_format_used = "json"

    schema = infer_schema_from_rows(rows, sample_limit=30)
    return {
        "format_detected": last_format_used,
        "schema": schema,
        "sample_rows": rows[:200],
        "suggestions": build_suggestions(schema),
        "sample_rows_count": len(rows)
    }
