# core/services/ine_api_service.py
import re
import requests
from urllib.parse import urlparse, parse_qs
from typing import List, Dict, Any, Optional
from dateutil import parser as dateparser

BASE_WSTEMPUS = "https://servicios.ine.es/wstempus/js/es"

class INEApiError(Exception):
    pass

def is_ine_dataset(url_or_id: str) -> bool:
    if not url_or_id:
        return False
    s = str(url_or_id).lower()
    if "ine.es" in s or "servicios.ine.es" in s:
        return True
    if s.isdigit() and len(s) >= 4:
        return True
    if s.endswith(".px") or ".px" in s:
        return True
    return False

def extract_ine_idtable(url_or_id: str) -> Optional[str]:
    if not url_or_id:
        return None
    s = str(url_or_id).strip()

    # Si sólo es número
    if s.isdigit():
        return s

    # Query params típicos
    try:
        parsed = urlparse(s)
        qs = parse_qs(parsed.query)
        if "t" in qs and qs["t"]:
            return qs["t"][0]
        if "tpx" in qs and qs["tpx"]:
            return qs["tpx"][0]
        if "file" in qs:
            path = qs.get("path", [""])[0].strip("/")
            file = qs["file"][0]
            return f"{path}/{file}" if path else file
    except Exception:
        pass

    # Regex fallback
    m = re.search(r'[?&]t=([^&]+)', s)
    if m:
        return m.group(1)
    m = re.search(r'[?&]tpx=([^&]+)', s)
    if m:
        return m.group(1)

    # Si contiene .px devolver segmento final
    if ".px" in s:
        return s.split("/")[-1]

    # último segmento
    if "/" in s:
        return s.split("/")[-1]

    return None

def fetch_table_data(table_id: str, sample_rows: Optional[int] = None) -> List[Dict[str, Any]]:
    if not table_id:
        raise INEApiError("table_id vacío para consultar INE")

    url = f"{BASE_WSTEMPUS}/DATOS_TABLA/{table_id}"
    resp = requests.get(url, timeout=30)
    resp.raise_for_status()
    j = resp.json()

    if not isinstance(j, list):
        raise INEApiError("Respuesta inesperada de la API del INE")

    if sample_rows:
        for serie in j:
            if "Data" in serie and isinstance(serie["Data"], list):
                serie["Data"] = serie["Data"][-sample_rows:]

    return j

def _infer_type(val: Any) -> str:
    """Detecta si un valor parece numérico o fecha."""
    if val is None or val == "":
        return "string"
    # Intentar número
    try:
        float(str(val).replace(",", "."))
        return "numeric"
    except Exception:
        pass
    # Intentar fecha
    try:
        dateparser.parse(str(val))
        return "datetime"
    except Exception:
        pass
    return "string"

def normalize_ine_data(raw_series: List[Dict[str, Any]]) -> Dict[str, Any]:
    if not raw_series:
        return {
            "schema": [],
            "sample_rows": [],
            "items_count": 0,
            "labels": [],
            "series": []
        }

    # === Construir labels ===
    labels = []
    for serie in raw_series:
        for punto in serie.get("Data", []):
            fecha = punto.get("Fecha")
            if fecha not in labels:
                labels.append(fecha)

    # Ordenar labels cronológicamente si parecen fechas
    try:
        labels.sort(key=lambda x: dateparser.parse(str(x)))
    except Exception:
        labels.sort()

    # === Construir series ===
    series_data = []
    for serie in raw_series:
        nombre = serie.get("Nombre", "Sin nombre")
        valores_map = {}
        for p in serie.get("Data", []):
            val = p.get("Valor")
            try:
                val = float(str(val).replace(",", ".")) if val not in (None, "") else None
            except Exception:
                val = None
            valores_map[p.get("Fecha")] = val
        valores_ordenados = [valores_map.get(f, None) for f in labels]
        series_data.append({"name": nombre, "data": valores_ordenados})

    # === Construir tabla ===
    cols: List[str] = []
    for serie in raw_series:
        for p in serie.get("Data", []):
            if isinstance(p, dict):
                for k in p.keys():
                    if k not in cols:
                        cols.append(k)

    # Inferir tipos de columna
    schema = []
    for c in cols:
        muestras = []
        for serie in raw_series:
            for p in serie.get("Data", []):
                muestras.append(p.get(c, ""))
        # quitar vacíos
        muestras_no_vacias = [m for m in muestras if m not in (None, "")]
        tipo = "string"
        if muestras_no_vacias:
            tipos_detectados = [_infer_type(v) for v in muestras_no_vacias[:20]]
            if tipos_detectados.count("numeric") / len(tipos_detectados) > 0.8:
                tipo = "numeric"
            elif tipos_detectados.count("datetime") / len(tipos_detectados) > 0.8:
                tipo = "datetime"
        schema.append({"name": c, "inferred_type": tipo})

    rows: List[Dict[str, Any]] = []
    for serie in raw_series:
        for p in serie.get("Data", []):
            row = {}
            for c in cols:
                v = p.get(c, "")
                if isinstance(v, list):
                    v = ", ".join(map(str, v))
                row[c] = v
            rows.append(row)

    return {
        "schema": schema,
        "sample_rows": rows,
        "items_count": len(rows),
        "labels": labels,
        "series": series_data
    }

def get_dataset_from_ine(url_or_id: str, sample_rows: Optional[int] = None) -> Dict[str, Any]:
    table_id = extract_ine_idtable(url_or_id)
    if not table_id:
        raise INEApiError("No se pudo extraer idTable de la URL/ID proporcionada")

    raw = fetch_table_data(table_id, sample_rows=sample_rows)
    return normalize_ine_data(raw)
