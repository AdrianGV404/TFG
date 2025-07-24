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

def store_datasets_as_json(datos, nombre_archivo="datasets_empleo.json"):
    """Guarda los datos en una carpeta organizada."""
    directorio = os.path.join("core", "data")
    os.makedirs(directorio, exist_ok=True)  # crea la carpeta si no existe
    ruta = os.path.join(directorio, nombre_archivo)

    try:
        with open(ruta, "w", encoding="utf-8") as archivo:
            json.dump(datos, archivo, indent=4, ensure_ascii=False)
        print(f"Datos guardados en {ruta}")
        return ruta
    except Exception as e:
        print(f"Error al guardar el archivo JSON: {e}")
        return None