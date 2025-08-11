# backend/services/sparql_service.py

import requests

SPARQL_ENDPOINT = "http://datos.gob.es/virtuoso/sparql"

def get_total_datasets():
    query = """
    SELECT (COUNT(?dataset) AS ?total) WHERE {
      ?dataset a <http://www.w3.org/ns/dcat#Dataset> .
    }
    """
    params = {
        "query": query,
        "format": "application/sparql-results+json"
    }
    response = requests.get(SPARQL_ENDPOINT, params=params, timeout=10)
    response.raise_for_status()  # Para levantar excepción si no es 200 OK

    # Comprueba si la respuesta está vacía (puede pasar)
    if not response.content:
        raise ValueError("Respuesta vacía del endpoint SPARQL")

    try:
        data = response.json()
    except Exception as e:
        # Muy útil para ver qué está llegando
        raise ValueError(f"Error parseando JSON de respuesta SPARQL: {e}\nContenido: {response.text}")

    # Extraemos el total de la respuesta, protegemos por si estructuras cambian
    try:
        total_str = data["results"]["bindings"][0]["total"]["value"]
        total = int(total_str)
    except (KeyError, IndexError, ValueError) as e:
        raise ValueError(f"No se pudo extraer el total desde la respuesta SPARQL: {e}\nDatos: {data}")

    return total

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
    params = {
        "query": query,
        "format": "application/sparql-results+json"
    }
    resp = requests.get(SPARQL_ENDPOINT, params=params, timeout=30)
    resp.raise_for_status()
    data = resp.json()

    # Transformar a lista [{uri: "...", label: "..."}]
    themes = []
    for b in data["results"]["bindings"]:
        uri = b.get("themeURI", {}).get("value")
        label = b.get("themeLabel", {}).get("value", uri)  # si no hay label, usar uri
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
    params = {
        "query": query,
        "format": "application/sparql-results+json"
    }
    response = requests.get(SPARQL_ENDPOINT, params=params, timeout=30)
    response.raise_for_status()
    data = response.json()

    results = []
    for binding in data["results"]["bindings"]:
        theme_uri = binding["theme"]["value"]
        theme_label = binding["themeLabel"]["value"]
        count = int(binding["datasetCount"]["value"])
        results.append({
            "theme": theme_uri,
            "label": theme_label,
            "count": count,
        })
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

    # Transformar el formato SPARQL a un JSON uniforme
    results = []
    for b in data["results"]["bindings"]:
        results.append({
            "dataset": b.get("dataset", {}).get("value"),
            "title": b.get("title", {}).get("value"),
            "description": b.get("description", {}).get("value"),
            "distribution": b.get("distribution", {}).get("value", None)
        })

    return {
        "items_count": len(results),
        "items": results
    }


def run_sparql_query(query):
    headers = {"Accept": "application/sparql-results+json"}
    response = requests.get(SPARQL_ENDPOINT, params={"query": query}, headers=headers)
    response.raise_for_status()
    return response.json()