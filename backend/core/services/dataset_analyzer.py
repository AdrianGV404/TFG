# core/services/dataset_analyzer.py

import requests
import io
import csv
import json
import re
from dateutil import parser as dateparser  # pip install python-dateutil
from typing import List, Dict

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
        # Remove grouping commas
        v2 = re.sub(r"[^\d\-\.,eE]", "", str(v))
        # Normalize comma as decimal when appropriate
        if v2.count(",") > 0 and v2.count(".") == 0:
            v2 = v2.replace(",", ".")
        return float(v2)
    except Exception:
        return None

def looks_like_date(s):
    if s is None or s == "":
        return False
    try:
        dateparser.parse(s)
        return True
    except Exception:
        return False

def find_first_list_in_json(obj):
    # If it's a list, return the list
    if isinstance(obj, list):
        return obj
    if isinstance(obj, dict):
        # Try common keys
        for k in ("data", "rows", "result", "results", "records", "items"):
            if k in obj and isinstance(obj[k], list):
                return obj[k]
        # Otherwise search values for lists
        for v in obj.values():
            if isinstance(v, list):
                return v
    return None

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
            # break early if got enough data lines (we'll parse later)
            if len(lines) >= max_rows + 20 and count_lines >= 50:
                break
    except StopIteration:
        pass
    text = "\n".join(lines)
    # Try to parse CSV
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
        # maybe it's a list-like string, try to coerce
        if isinstance(data, list):
            arr = data
        else:
            # fallback: not a list
            return []
    # ensure dict-like rows
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
    cols = list(rows[0].keys())
    for col in cols:
        values = [safe_text(r.get(col, "")) for r in rows[:sample_limit]]
        non_empty = [v for v in values if v != "" and v is not None]
        inferred = "string"
        # Try numeric
        num_count = 0
        date_count = 0
        lat_count = 0
        lon_count = 0
        unique_vals = set()
        for v in non_empty:
            unique_vals.add(v.lower())
            if try_parse_number(v) is not None:
                num_count += 1
                # check lat/lon ranges
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
        # heuristics
        if len(non_empty) > 0:
            if num_count / len(non_empty) >= 0.8:
                inferred = "numeric"
            elif date_count / len(non_empty) >= 0.6:
                inferred = "datetime"
            else:
                inferred = "string"
        # check name hints
        lowname = col.lower()
        if any(k in lowname for k in ("lat", "latitude", "latitud")):
            inferred = "latitude"
        if any(k in lowname for k in ("lon", "lng", "longitude", "longitud")):
            inferred = "longitude"
        # check if values include spanish community names -> geo_name
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

def build_suggestions(schema):
    # schema: list of column dicts with inferred_type
    suggestions = []
    # find columns by type
    numeric_cols = [c["name"] for c in schema if c["inferred_type"] == "numeric"]
    datetime_cols = [c["name"] for c in schema if c["inferred_type"] == "datetime"]
    lat_cols = [c["name"] for c in schema if c["inferred_type"] == "latitude"]
    lon_cols = [c["name"] for c in schema if c["inferred_type"] == "longitude"]
    geo_name_cols = [c["name"] for c in schema if c["inferred_type"] == "geo_name"]
    categorical_candidates = [c["name"] for c in schema if c["inferred_type"] == "string" and c["unique_count_estimate"] < 200]

    # timeseries suggestions
    for dcol in datetime_cols:
        for ncol in numeric_cols:
            suggestions.append({
                "type": "timeseries",
                "title": f"Serie temporal: {ncol} por {dcol}",
                "x": dcol,
                "y": ncol
            })
    # bar / pie suggestions (categorical + numeric)
    for cat in categorical_candidates:
        for ncol in numeric_cols:
            suggestions.append({
                "type": "barchart",
                "title": f"Barrs por {cat} ({ncol})",
                "category": cat,
                "value": ncol
            })
            suggestions.append({
                "type": "piechart",
                "title": f"Pie por {cat} ({ncol})",
                "category": cat,
                "value": ncol
            })
    # geo suggestions
    if lat_cols and lon_cols:
        suggestions.append({
            "type": "heatmap",
            "title": f"Heatmap por coordenadas ({lat_cols[0]} / {lon_cols[0]})",
            "lat": lat_cols[0],
            "lon": lon_cols[0]
        })
    if geo_name_cols and numeric_cols:
        for ncol in numeric_cols:
            suggestions.append({
                "type": "choropleth",
                "title": f"Valor por unidad geográfica ({geo_name_cols[0]} - {ncol})",
                "geo_name": geo_name_cols[0],
                "value": ncol
            })
    # fallback table
    suggestions.append({"type": "table", "title": "Mostrar tabla (vista cruda)"})
    return suggestions

def analyze_distribution_url(url: str, format_override: str = None, sample_rows=80):
    """
    Descarga una muestra del recurso (CSV o JSON) y devuelve:
    {
      "schema": [...],
      "sample_rows": [...],
      "suggestions": [...]
    }
    """
    # Decide format heuristically por extension o override
    fmt = None
    if format_override:
        fmt = format_override.lower()
    else:
        if url.lower().endswith(".csv") or "rows.csv" in url or "format=csv" in url:
            fmt = "csv"
        elif url.lower().endswith(".json") or "rows.json" in url or "format=json" in url:
            fmt = "json"
        elif "rdf" in url or "xml" in url:
            fmt = "rdf"
        else:
            # default try CSV first, if fails try JSON
            fmt = None

    rows = []
    last_format_used = None
    # Try CSV if assumed
    if fmt == "csv":
        rows = sample_csv_from_url(url, max_rows=sample_rows)
        last_format_used = "csv"
    elif fmt == "json":
        rows = sample_json_from_url(url, max_rows=sample_rows)
        last_format_used = "json"
    else:
        # Try CSV then JSON
        try:
            rows = sample_csv_from_url(url, max_rows=sample_rows)
            if rows:
                last_format_used = "csv"
            else:
                rows = sample_json_from_url(url, max_rows=sample_rows)
                last_format_used = "json"
        except Exception:
            # fallback to json try
            try:
                rows = sample_json_from_url(url, max_rows=sample_rows)
                last_format_used = "json"
            except Exception as e:
                raise RuntimeError(f"No se pudo obtener muestra (intentos CSV/JSON fallaron): {e}")

    schema = infer_schema_from_rows(rows, sample_limit=30)
    suggestions = build_suggestions(schema)

    return {
        "format_detected": last_format_used,
        "schema": schema,
        "sample_rows": rows[:min(len(rows), 200)],
        "suggestions": suggestions,
        # not returning full items_count (SPARQL needed) - give sample size
        "sample_rows_count": len(rows)
    }
