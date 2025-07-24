# Rutas de la app
from django.urls import path
from core import views

urlpatterns = [
    path('', views.home, name='home'),
    path('test/', views.test_conexion_bd_api, name='test_conexion_bd_api'),
    path('datasets/', views.downloadDatasets, name='downloadDatasets'),
]