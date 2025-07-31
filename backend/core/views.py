from django.shortcuts import render
from django.views.decorators.http import require_GET
from django.http import JsonResponse
from django.contrib.auth.models import User
from core.services.list_gov_datasets import *
from core.services.search_datasets import *
from django.utils.text import slugify
from core.utils.file_utils import handle_dataset_file
import os

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
        return JsonResponse(
            {"error": f"Error al conectar con la BD: {str(e)}"},
            status=500
        )

def store_datasets_as_json_view(request):
    data, message, status = handle_dataset_file(
        directory=DATASETS_DIR,
        filename="data",
        fetch_function=search_datasets
    )
    
    if status != 200 or not data:
        return JsonResponse({"error": message}, status=status)
    
    return JsonResponse({
        "message": message,
        "ruta": os.path.join(DATASETS_DIR, f"{slugify('all_datasets')}.json")
    })

@require_GET
def search_by_title_view(request):
    titulo = request.GET.get("title")
    if not titulo:
        return JsonResponse({"error": "Falta el parámetro 'title'"}, status=400)
    
    data, message, status = handle_dataset_file(
        directory=DATASETS_DIR,
        filename=titulo,
        fetch_function=search_by_title,
        fetch_args=(titulo,)
    )
    
    if status != 200 or not data:
        return JsonResponse({"error": message}, status=status)
    
    return JsonResponse({
        "message": message,
        "result": data.get("result", {}),
        "items_count": len(data.get("result", {}).get("items", [])),
        "file_path": os.path.join(DATASETS_DIR, f"{slugify(titulo)}.json")
    }, status=status)