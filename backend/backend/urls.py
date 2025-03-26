"""
URL configuration for backend project.

The `urlpatterns` list routes URLs to views. For more information please see:
    https://docs.djangoproject.com/en/5.1/topics/http/urls/
Examples:
Function views
    1. Add an import:  from my_app import views
    2. Add a URL to urlpatterns:  path('', views.home, name='home')
Class-based views
    1. Add an import:  from other_app.views import Home
    2. Add a URL to urlpatterns:  path('', Home.as_view(), name='home')
Including another URLconf
    1. Import the include() function: from django.urls import include, path
    2. Add a URL to urlpatterns:  path('blog/', include('blog.urls'))
"""
from django.contrib import admin
from django.urls import include, path
from django.http import JsonResponse
from django.contrib.auth.models import User

def test_api(request):
    try:
        # Consulta a la BD: Cuenta cuántos usuarios hay
        user_count = User.objects.count()
        
        return JsonResponse({
            "message": "¡Conexión con Django y PostgreSQL exitosa!",
            "user_count": user_count,  # Datos desde la BD
        }, status=200)
    
    except Exception as e:
        return JsonResponse(
            {"error": f"Error al conectar con la BD: {str(e)}"},
            status=500
        )

urlpatterns = [
    path('admin/', admin.site.urls),
    path('', include('core.urls')),
    path('api/test/', test_api),
]