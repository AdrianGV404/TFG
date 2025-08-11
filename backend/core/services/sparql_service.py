import requests
import logging

SPARQL_ENDPOINT = "http://datos.gob.es/virtuoso/sparql"
logger = logging.getLogger(__name__)

DEFAULT_TIMEOUT = 20  # segundos

def run_sparql_query(query, timeout=DEFAULT_TIMEOUT):
    """
    Ejecuta una consulta SPARQL y devuelve el JSON ya parseado.
    Lanza excepción si la respuesta no es 200 o el JSON es inválido.
    """
    headers = {"Accept": "application/sparql-results+json"}
    try:
        response = requests.get(
            SPARQL_ENDPOINT,
            params={"query": query},
            headers=headers,
            timeout=timeout
        )
        response.raise_for_status()
    except requests.RequestException as e:
        logger.exception("Error de red al consultar SPARQL")
        raise RuntimeError(f"Error al conectar con SPARQL endpoint: {e}")
    
    try:
        return response.json()
    except ValueError as e:
        logger.error("Respuesta SPARQL no es JSON válido")
        raise RuntimeError(f"Respuesta no es JSON válido: {e}")

def get_total_datasets():
    query = """
    SELECT (COUNT(?dataset) AS ?total) WHERE {
      ?dataset a <http://www.w3.org/ns/dcat#Dataset> .
    }
    """
    data = run_sparql_query(query)
    try:
        total_str = data["results"]["bindings"][0]["total"]["value"]
        return int(total_str)
    except (KeyError, IndexError, ValueError) as e:
        raise RuntimeError(f"No se pudo extraer 'total' del resultado SPARQL: {e}")

def get_all_themes():
    query = """
    PREFIX dcat: <http://www.w3.org/ns/dcat#>
    PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
    SELECT DISTINCT ?themeURI ?themeLabel WHERE {
      ?dataset a dcat:Dataset ;
               dcat:theme ?themeURI .
      OPTIONAL {
        ?themeURI skos:prefLabel ?themeLabel .
        FILTER(LANGMATCHES(LANG(?themeLabel), "es") || LANGMATCHES(LANG(?themeLabel), "en"))
      }
    }
    ORDER BY ?themeLabel
    """
    data = run_sparql_query(query, timeout=30)
    themes = []
    for b in data.get("results", {}).get("bindings", []):
        uri = b.get("themeURI", {}).get("value")
        label = b.get("themeLabel", {}).get("value", uri)
        themes.append({"uri": uri, "label": label})
    return themes

def get_dataset_counts_by_theme():
    query = """
    PREFIX dcat: <http://www.w3.org/ns/dcat#>
    PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
    SELECT ?theme ?themeLabel (COUNT(?dataset) AS ?datasetCount)
    WHERE {
      ?dataset a dcat:Dataset ;
               dcat:theme ?theme .
      ?theme skos:prefLabel ?themeLabel .
      FILTER(LANG(?themeLabel) = "es")
    }
    GROUP BY ?theme ?themeLabel
    ORDER BY DESC(?datasetCount)
    """
    data = run_sparql_query(query, timeout=30)
    results = []
    for b in data.get("results", {}).get("bindings", []):
        try:
            results.append({
                "theme": b["theme"]["value"],
                "label": b["themeLabel"]["value"],
                "count": int(b["datasetCount"]["value"]),
            })
        except (KeyError, ValueError):
            continue
    return results

def search_datasets_by_theme_sparql(theme_uri, search_type="", value="", page=0):
    query = f"""
    PREFIX dcat: <http://www.w3.org/ns/dcat#>
    PREFIX dct: <http://purl.org/dc/terms/>
    SELECT ?dataset ?title ?description ?distribution
    WHERE {{
      ?dataset a dcat:Dataset ;
               dct:title ?title ;
               dct:description ?description ;
               dcat:theme <{theme_uri}> .
      OPTIONAL {{ ?dataset dcat:distribution ?distribution . }}
      FILTER(LANG(?title) = "es" || LANG(?title) = "en")
      FILTER(LANG(?description) = "es" || LANG(?description) = "en")
    }}
    LIMIT 10 OFFSET {page * 10}
    """
    data = run_sparql_query(query)
    items = []
    for b in data.get("results", {}).get("bindings", []):
        items.append({
            "dataset": b.get("dataset", {}).get("value"),
            "title": b.get("title", {}).get("value"),
            "description": b.get("description", {}).get("value"),
            "distribution": b.get("distribution", {}).get("value")
        })
    return {
        "items_count": len(items),
        "items": items
    }
