from django.urls import path
from core import views

urlpatterns = [
    path('', views.home, name='home'),
    path('test/', views.test_conexion_bd_api, name='test_conexion_bd_api'),

    # Búsqueda
    path('search/title/', views.search_by_title_view, name='search_by_title_view'),
    path('search/keyword/', views.search_by_keyword_view, name='search_by_keyword_view'),
    path('search/spatial/', views.search_by_spatial_view, name='search_by_spatial_view'),
    path('search/category/', views.search_by_category_view, name='search_by_category_view'),

    # Estadísticas
    path("stats/total-datasets/", views.total_datasets_view, name="total_datasets_view"),
    path("stats/themes/", views.all_themes_view, name="all_themes_view"),
    path("stats/dataset-counts-by-theme/", views.dataset_counts_by_theme_view, name="dataset_counts_by_theme_view"),

    # Procesar dataset (ahora GET en lugar de POST)
    path("dataset/analyze/", views.analyze_dataset_view, name="analyze_dataset_view"),
]
