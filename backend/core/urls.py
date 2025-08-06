from django.urls import path
from core import views

urlpatterns = [
    path('', views.home, name='home'),
    path('test/', views.test_conexion_bd_api, name='test_conexion_bd_api'),
    path('search/title/', views.search_by_title_view, name='search_by_title_view'),
    path('search/keyword/', views.search_by_keyword_view, name='search_by_keyword_view'),
    path('search/spatial/', views.search_by_spatial_view, name='search_by_spatial_view'),
    path('search/category/', views.search_by_category_view, name='search_by_category_view'),
]