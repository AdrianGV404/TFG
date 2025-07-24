# Lógica de endpoints
from django.shortcuts import render
from django.http import JsonResponse
from django.contrib.auth.models import User
from core.services.list_gov_datasets import *

# Create your views here.
from django.http import HttpResponse

def home(request):
    return render(request, "core/home.html")

def test_conexion_bd_api(request):
    try:
        user_count = User.objects.count() # ORM -> translates python to sql without using sql language
        return JsonResponse({
            "message": "¡Conexión con Django y PostgreSQL exitosa!",
            "user_count": user_count,
        }, status=200)
    except Exception as e:
        return JsonResponse(
            {"error": f"Error al conectar con la BD: {str(e)}"},
            status=500
        )

def downloadDatasets(request):
    datos = search_datasets()
    if not datos:
        return JsonResponse({"error": "No se pudo obtener datos desde datos.gob.es"}, status=500)
    ruta = store_datasets_as_json(datos)
    if ruta:
        return JsonResponse({"message": "✅ Archivo guardado correctamente", "ruta": ruta})
    else:
        return JsonResponse({"error": "❌ No se pudo guardar el archivo"}, status=500)