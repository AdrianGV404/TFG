import requests
import json

def buscar_datasets_empleo():
    """Busca datasets en datos.gob.es que contienen 'empleo' en su título."""
    url = "http://datos.gob.es/apidata/catalog/dataset/title/empleo"
    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36"
    }
    try:
        respuesta = requests.get(url, headers=headers)
        respuesta.raise_for_status()  # Lanza una excepción si la respuesta no es exitosa
        datos = respuesta.json()
        return datos
    except requests.exceptions.RequestException as e:
        print(f"Error al realizar la consulta: {e}")
        return None

def guardar_en_json(datos, nombre_archivo="datasets_empleo.json"):
    """Guarda los datos en un archivo JSON con formato legible."""
    try:
        with open(nombre_archivo, "w", encoding="utf-8") as archivo:
            # Usamos indent=4 para un formato legible y ensure_ascii=False para mantener caracteres especiales
            json.dump(datos, archivo, indent=4, ensure_ascii=False)
        print(f"Los datos se han guardado correctamente en '{nombre_archivo}'.")
    except Exception as e:
        print(f"Error al guardar el archivo JSON: {e}")

# Buscar datasets de empleo
resultados = buscar_datasets_empleo()

if resultados:
    # Guardar los resultados en un archivo JSON
    guardar_en_json(resultados)
else:
    print("No se encontraron resultados para guardar.")