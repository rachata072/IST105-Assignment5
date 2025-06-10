"""
URL configuration for puzzle app.
"""
from django.urls import path
from . import views

urlpatterns = [
    path('', views.puzzle_form, name='puzzle_form'),  # Main form page
    #path('result/', views.puzzle_result, name='puzzle_result'),  # Results page
]