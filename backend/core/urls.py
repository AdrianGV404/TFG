# Rutas de la app
from django.urls import path
from . import views

urlpatterns = [
    path('', views.home, name='home'),
    path('test/', views.test_conexion_bd_api, name='test_conexion_bd_api'),  # si ya tienes esta función
]