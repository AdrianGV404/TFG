# Lógica de endpoints
from django.shortcuts import render
from django.http import JsonResponse
from django.contrib.auth.models import User

# Create your views here.
from django.http import HttpResponse

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