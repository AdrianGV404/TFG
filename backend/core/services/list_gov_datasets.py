import requests
import json
import os

def search_datasets():
    """Busca datasets en datos.gob.es que contienen 'empleo' en su título."""
    url = "http://datos.gob.es/apidata/catalog/dataset/title/empleo"
    headers = {
        "User-Agent": "Mozilla/5.0"
    }
    try:
        respuesta = requests.get(url, headers=headers)
        respuesta.raise_for_status()
        datos = respuesta.json()
        return datos
    except requests.exceptions.RequestException as e:
        print(f"Error al realizar la consulta: {e}")
        return None