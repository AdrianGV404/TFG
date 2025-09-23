from django.contrib import admin
from django.urls import include, path
from django.http import HttpResponse

def root_view(request):
    return HttpResponse("Backend OK", content_type="text/plain")

urlpatterns = [
    path("", root_view),  # raíz
    path("admin/", admin.site.urls),
    path("api/", include("core.urls")),
]
