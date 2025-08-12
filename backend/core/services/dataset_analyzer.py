# core/services/dataset_analyzer.py
import requests
import io
import csv
import json
import re
import pyaxis  # soporte PC-Axis (si lo tienes instalado)
from dateutil import parser as dateparser
from typing import List, Dict, Any, Optional

# Importación del servicio INE (asegúrate de tener el archivo core/services/ine_api_service.py actualizado
# para que get_dataset_from_ine devuelva labels y series — te lo indico más abajo si hace falta).
from core.services.ine_api_service import is_ine_dataset, get_dataset_from_ine

SPANISH_COMMUNITIES = [
    "andalucía", "andalucia", "aragón", "aragon", "asturias", "islas baleares",
    "baleares", "canarias", "cantabria", "castilla-la mancha", "castilla y león",
    "castilla y leon", "cataluña", "cataluna", "comunidad valenciana", "valenciana",
    "extremadura", "galicia", "la rioja", "madrid", "murcia", "navarra",
    "país vasco", "pais vasco", "paisvasco", "ceuta", "melilla"
]

# -----------------------
# Utilidades básicas
# -----------------------
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
    return " ".join(parts)

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
        normalized.append({col: r.get(col, "") for col in all_cols})
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
        rows = [{k: safe_text(v) for k, v in r.items()} for r in reader]
        return rows[:max_rows]
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
        rows.append(item if isinstance(item, dict) else {"value": item})
    return rows

def sample_pcaxis_from_url(url, max_rows=100, timeout=30):
    resp = requests.get(url, timeout=timeout)
    resp.raise_for_status()
    # pyaxis.parse devuelve dataframe; convertir a records
    df = pyaxis.parse(io.BytesIO(resp.content), encoding="utf-8")
    return df.to_dict(orient="records")[:max_rows]

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
        non_empty = [v for v in values if v]
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
        lower_vals = [v.lower() for v in non_empty[:min(30, len(non_empty))]]
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

    # Priorizar timeseries si hay datetime + numeric
    for dcol in datetime_cols:
        for ncol in numeric_cols:
            suggestions.append({"type": "timeseries", "title": f"{ncol} por {dcol}", "x": dcol, "y": ncol})

    # Agrupar numéricos por categorías -> barras / pastel
    for cat in categorical_candidates:
        for ncol in numeric_cols:
            suggestions.append({"type": "barchart", "title": f"{ncol} por {cat}", "category": cat, "value": ncol})
            suggestions.append({"type": "piechart", "title": f"{ncol} por {cat}", "category": cat, "value": ncol})

    # Geoespacial
    if lat_cols and lon_cols:
        suggestions.append({"type": "heatmap", "title": f"Coordenadas ({lat_cols[0]} / {lon_cols[0]})", "lat": lat_cols[0], "lon": lon_cols[0]})
    if geo_name_cols and numeric_cols:
        for ncol in numeric_cols:
            suggestions.append({"type": "choropleth", "title": f"{ncol} por {geo_name_cols[0]}", "geo_name": geo_name_cols[0], "value": ncol})

    # Siempre añadir tabla al final
    suggestions.append({"type": "table", "title": "Mostrar tabla"})
    return suggestions

def choose_primary_suggestion(suggestions):
    # Preferir visualizaciones gráficas antes que table
    order = ["timeseries", "barchart", "piechart", "choropleth", "heatmap", "table"]
    for t in order:
        for s in suggestions:
            if s.get("type") == t:
                return s
    return suggestions[0] if suggestions else {"type": "table", "title": "Mostrar tabla"}

# -----------------------
# Analyze distribution url (con soporte INE)
# -----------------------
def analyze_distribution_url(url: str, format_override: str = None, sample_rows=80):
    """
    Descarga una muestra del recurso (PC-Axis prioritario, luego CSV, luego JSON)
    y devuelve esquema, muestra y sugerencias.
    En caso de detectar recurso INE (ine-api), usa get_dataset_from_ine para obtener labels/series.
    """
    # Si es INE -> usar su API (prioritario)
    try:
        if is_ine_dataset(url):
            # get_dataset_from_ine debe devolver al menos: schema, sample_rows, labels, series
            # sample_rows argument pasa el tamaño máximo de sample (o -1 para todo)
            try:
                ine_data = get_dataset_from_ine(url, sample_rows=sample_rows)
            except Exception as e:
                raise RuntimeError(f"Error al obtener datos INE: {e}")

            # Normalizar nombres de keys por si acaso
            schema = ine_data.get("schema", [])
            normalized_rows = ine_data.get("sample_rows", [])
            labels = ine_data.get("labels", [])    # ejes X (fechas, periodos)
            series = ine_data.get("series", [])    # lista de {name, data}

            # Si la función get_dataset_from_ine no devolviera schema/samplerows (compatibilidad),
            # intentar normalizar a partir de 'raw' si está presente.
            if not schema:
                schema = infer_schema_from_rows(normalized_rows, sample_limit=30)

            suggestions = build_suggestions(schema)
            primary = choose_primary_suggestion(suggestions)

            # Forzar que la sugerencia principal sea timeseries si tenemos labels+series
            if labels and series and primary.get("type") == "table":
                primary = {"type": "timeseries", "title": primary.get("title", "Series temporales (INE)"), "x": None, "y": None}

            result = {
                "format_detected": "ine-api",
                "schema": schema,
                "sample_rows": normalized_rows,
                "suggestions": suggestions,
                "suggestion": primary,
                "sample_rows_count": len(normalized_rows),
            }
            # añadir labels/series para que el frontend pinte directamente
            if labels:
                result["labels"] = labels
            if series:
                result["series"] = series

            return result

    except Exception as e:
        # Si falla la ruta INE, se sigue a la ruta genérica más abajo (no romper todo)
        # Loguear/ignorar y continuar
        print(f"[analyze_distribution_url] fallo INE: {e}")

    # ---------------------------------------------------------
    # Código no-INE: intentar detectar formato y samplear
    # ---------------------------------------------------------
    fmt = None
    if format_override:
        fmt = format_override.lower()
    else:
        if ".px" in url.lower() or "pc-axis" in url.lower() or "format=pc-axis" in url.lower():
            fmt = "pc-axis"
        elif url.lower().endswith(".csv") or "rows.csv" in url.lower() or "format=csv" in url.lower():
            fmt = "csv"
        elif url.lower().endswith(".json") or "rows.json" in url.lower() or "format=json" in url.lower():
            fmt = "json"
        elif "rdf" in url.lower() or "xml" in url.lower():
            fmt = "rdf"
        else:
            fmt = None

    rows = []
    last_format_used = None

    try:
        if fmt == "pc-axis":
            rows = sample_pcaxis_from_url(url, max_rows=sample_rows)
            last_format_used = "pc-axis"

        elif fmt == "csv":
            rows = sample_csv_from_url(url, max_rows=sample_rows)
            last_format_used = "csv"

        elif fmt == "json":
            rows = sample_json_from_url(url, max_rows=sample_rows)
            last_format_used = "json"

        else:
            # fallback: pc-axis -> csv -> json
            try:
                rows = sample_pcaxis_from_url(url, max_rows=sample_rows)
                if rows:
                    last_format_used = "pc-axis"
                else:
                    rows = sample_csv_from_url(url, max_rows=sample_rows)
                    if rows:
                        last_format_used = "csv"
                    else:
                        rows = sample_json_from_url(url, max_rows=sample_rows)
                        last_format_used = "json"
            except Exception:
                try:
                    rows = sample_csv_from_url(url, max_rows=sample_rows)
                    if rows:
                        last_format_used = "csv"
                    else:
                        rows = sample_json_from_url(url, max_rows=sample_rows)
                        last_format_used = "json"
                except Exception as e:
                    raise RuntimeError(f"No se pudo obtener muestra en ningún formato: {e}")

    except Exception as e:
        raise RuntimeError(f"Error al procesar la URL {url}: {e}")

    normalized = normalize_rows(rows) if rows else []
    schema = infer_schema_from_rows(normalized, sample_limit=30)
    suggestions = build_suggestions(schema)
    primary = choose_primary_suggestion(suggestions)

    return {
        "format_detected": last_format_used,
        "schema": schema,
        "sample_rows": normalized[:min(len(normalized), 200)],
        "suggestions": suggestions,
        "suggestion": primary,
        "sample_rows_count": len(normalized)
    }