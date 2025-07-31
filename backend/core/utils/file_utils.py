import os
import json
from django.utils.text import slugify

def handle_dataset_file(directory, filename, fetch_function, fetch_args=None, force_download=False):
    """
    Maneja la lógica de verificación y descarga de archivos de datasets
    
    Args:
        directory (str): Directorio donde se guardarán los archivos (ej. 'core/data')
        filename (str): Nombre base del archivo (sin extensión)
        fetch_function (function): Función que obtiene los datos si es necesario descargarlos
        fetch_args (tuple): Argumentos para la función fetch_function
        force_download (bool): Si True, ignora archivos existentes y fuerza la descarga
    
    Returns:
        tuple: (datos, mensaje, status_code)
    """
    # Crear el directorio si no existe
    os.makedirs(directory, exist_ok=True)
    
    # Generar nombre de archivo seguro
    safe_filename = f"{slugify(filename)}.json"
    file_path = os.path.join(directory, safe_filename)
    
    # Si el archivo existe y no forzamos descarga, cargarlo
    if not force_download and os.path.exists(file_path):
        try:
            with open(file_path, encoding="utf-8") as f:
                data = json.load(f)
            return data, f"Archivo '{safe_filename}' cargado desde disco", 200
        except Exception as e:
            # Si hay error al leer, proceder a descargar
            pass
    
    # Obtener datos de la función proporcionada
    try:
        data = fetch_function(*(fetch_args or ()))
        if not data:
            return None, "No se pudieron obtener datos", 500
    except Exception as e:
        return None, f"Error al obtener datos: {str(e)}", 500
    
    # Guardar los datos
    try:
        with open(file_path, 'w', encoding="utf-8") as f:
            json.dump(data, f, ensure_ascii=False, indent=2)
        return data, f"Datos guardados en '{safe_filename}'", 200
    except Exception as e:
        return data, f"Datos obtenidos pero no se pudieron guardar: {str(e)}", 500