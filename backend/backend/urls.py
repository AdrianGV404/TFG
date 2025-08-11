from django.contrib import admin
from django.urls import include, path
from django.http import JsonResponse
from django.contrib.auth.models import User
from core import views


urlpatterns = [
    path('admin/', admin.site.urls),
    path('api/', include('core.urls')),
    
]