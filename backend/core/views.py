# views.py
from django.shortcuts import render
from django.views.decorators.http import require_GET
from django.http import JsonResponse
from django.contrib.auth.models import User
from django.utils.text import slugify
import os

from core.services.list_gov_datasets import *
from core.services.search_datasets import *
from core.utils.file_utils import handle_dataset_file

DATASETS_DIR = os.path.join("core", "data")


def home(request):
    return render(request, "core/home.html")


def test_conexion_bd_api(request):
    try:
        user_count = User.objects.count()
        return JsonResponse({
            "message": "¡Conexión con Django y PostgreSQL exitosa!",
            "user_count": user_count,
        }, status=200)
    except Exception as e:
        return JsonResponse({"error": f"Error al conectar con la BD: {str(e)}"}, status=500)

@require_GET
def search_by_title_view(request):
    titulo = request.GET.get("title")
    page = request.GET.get("_page", "0")
    if not titulo:
        return JsonResponse({"error": "Falta el parámetro 'title'"}, status=400)
    
    try:
        page_int = int(page)
        if page_int < 0:
            page_int = 0
    except ValueError:
        page_int = 0

    filename = f"{titulo}_{page_int}"

    data, message, status = handle_dataset_file(
        directory=DATASETS_DIR,
        filename=filename,
        fetch_function=search_by_title,
        fetch_args=(titulo, page_int)
    )
    
    if status != 200 or not data:
        return JsonResponse({"error": message}, status=status)
    
    return JsonResponse({
        "message": message,
        "result": data.get("result", {}),
        "items_count": len(data.get("result", {}).get("items", [])),
        "file_path": os.path.join(DATASETS_DIR, f"{slugify(filename)}.json")
    }, status=status)


@require_GET
def search_by_keyword_view(request):
    keyword = request.GET.get("keyword")
    page = request.GET.get("_page", "0")
    if not keyword:
        return JsonResponse({"error": "Falta el parámetro 'keyword'"}, status=400)
    
    try:
        page_int = int(page)
        if page_int < 0:
            page_int = 0
    except ValueError:
        page_int = 0

    filename = f"{keyword}_{page_int}"

    data, message, status = handle_dataset_file(
        directory=DATASETS_DIR,
        filename=filename,
        fetch_function=search_by_keyword,
        fetch_args=(keyword, page_int)
    )
    
    if status != 200 or not data:
        return JsonResponse({"error": message}, status=status)
    
    return JsonResponse({
        "message": message,
        "result": data.get("result", {}),
        "items_count": len(data.get("result", {}).get("items", [])),
        "file_path": os.path.join(DATASETS_DIR, f"{slugify(filename)}.json")
    }, status=status)


@require_GET
def search_by_spatial_view(request):
    spatial_type = request.GET.get("spatial_type")
    spatial_value = request.GET.get("spatial_value")
    page = request.GET.get("_page", "0")
    
    if not spatial_type or not spatial_value:
        return JsonResponse({"error": "Faltan los parámetros 'spatial_type' y/o 'spatial_value'"}, status=400)
    try:
        page_int = int(page)
        if page_int < 0:
            page_int = 0
    except ValueError:
        page_int = 0

    filename = f"{spatial_type}_{spatial_value}_{page_int}"

    data, message, status = handle_dataset_file(
        directory=DATASETS_DIR,
        filename=filename,
        fetch_function=search_by_spatial,
        fetch_args=(spatial_type, spatial_value, page_int)
    )

    if status != 200 or not data:
        return JsonResponse({"error": message}, status=status)

    return JsonResponse({
        "message": message,
        "result": data.get("result", {}),
        "items_count": len(data.get("result", {}).get("items", [])),
        "file_path": os.path.join(DATASETS_DIR, f"{slugify(filename)}.json")
    }, status=status)
