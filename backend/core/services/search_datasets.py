import os
import json
import requests

BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
DATA_DIR = os.path.join(BASE_DIR, "core", "data")

def store_datasets_as_json(data, filename="data.json"):
    directorio = os.path.join("core", "data")
    os.makedirs(directorio, exist_ok=True)
    path = os.path.join(directorio, filename)
    try:
        with open(path, "w", encoding="utf-8") as f:
            json.dump(data, f, ensure_ascii=False, indent=4)
            print(f"Datos guardados en {path}")
        return path
    except Exception as e:
        print("Error al guardar archivo:", e)
        return None

def search_by_title(title):
    """Consulta la API de datos.gob.es buscando datasets por título"""
    url = f"https://datos.gob.es/apidata/catalog/dataset/title/{title}?_sort=title&_pageSize=200&_page=0"
    headers = {
        "Accept": "application/json",
        "User-Agent": "TFG Buscador de Datos - Estudiante"
    }
    try:
        response = requests.get(url, headers=headers)
        response.raise_for_status()
        return response.json()
    except Exception as e:
        print("Error en search_by_title:", e)
        return None
