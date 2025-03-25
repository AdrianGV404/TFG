from django.shortcuts import render

# Create your views here.
from django.http import HttpResponse

def home_view(request):
    return HttpResponse("""
    <h1>Backend Django funcionando!</h1>
    <p>El frontend React se conectará aquí.</p>
    """)