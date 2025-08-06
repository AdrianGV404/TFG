import os
import json
import requests


BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
DATA_DIR = os.path.join(BASE_DIR, "core", "data")

def search_by_title(title, page=0, page_size=200):
    """Consulta la API de datos.gob.es buscando datasets por título con paginación"""
    url = f"https://datos.gob.es/apidata/catalog/dataset/title/{title}?_sort=title&_pageSize={page_size}&_page={page}"
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


def search_by_keyword(keyword, page=0, page_size=200):
    """Consulta la API de datos.gob.es buscando datasets por keyword con paginación"""
    url = f"https://datos.gob.es/apidata/catalog/dataset/keyword/{keyword}?_sort=title&_pageSize={page_size}&_page={page}"
    headers = {
        "Accept": "application/json",
        "User-Agent": "TFG Buscador de Datos - Estudiante"
    }
    try:
        response = requests.get(url, headers=headers)
        response.raise_for_status()
        return response.json()
    except Exception as e:
        print("Error en search_by_keyword:", e)
        return None


def search_by_spatial(spatial_type, spatial_value, page=0, page_size=200):
    """Consulta la API de datos.gob.es buscando datasets por tipo y valor espacial con paginación"""
    url = f"https://datos.gob.es/apidata/catalog/dataset/spatial/{spatial_type}/{spatial_value}?_sort=title&_pageSize={page_size}&_page={page}"
    headers = {
        "Accept": "application/json",
        "User-Agent": "TFG Buscador de Datos - Estudiante"
    }
    try:
        response = requests.get(url, headers=headers)
        response.raise_for_status()
        return response.json()
    except Exception as e:
        print("Error en search_by_spatial:", e)
        return None

def search_by_category(category, page=0, page_size=200):
    """Consulta la API de datos.gob.es buscando datasets por categoría con paginación"""
    url = f"https://datos.gob.es/apidata/catalog/dataset/theme/{category}?_sort=title&_pageSize={page_size}&_page={page}"
    headers = {
        "Accept": "application/json",
        "User-Agent": "TFG Buscador de Datos - Estudiante"
    }
    try:
        response = requests.get(url, headers=headers)
        response.raise_for_status()
        return response.json()
    except Exception as e:
        print("Error en search_by_category:", e)
        return None
