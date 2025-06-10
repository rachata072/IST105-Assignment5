"""
URL configuration for assignment5 project.
"""
from django.contrib import admin
from django.urls import path, include

urlpatterns = [
    path('admin/', admin.site.urls),
    path('', include('puzzle.urls')),  # Include puzzle app URLs
]