# Rutas de la app
from django.urls import path
from core import views

urlpatterns = [
    path('', views.home, name='home'),
    path('test/', views.test_conexion_bd_api, name='test_conexion_bd_api'),
    path('search/', views.store_datasets_as_json_view, name='store_datasets_as_json_view'), 
    path('search/title/', views.search_by_title_view, name='search_by_title_view'), 

]