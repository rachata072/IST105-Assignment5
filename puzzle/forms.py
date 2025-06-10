"""
Forms for the puzzle app.
"""
from django import forms

class PuzzleForm(forms.Form):
    """Form for collecting user input."""
    number = forms.IntegerField(label='Enter a Number')
    text = forms.CharField(label='Enter Text', max_length=100)
    guess = forms.IntegerField(label='Treasure Hunt Guess (1-100)', min_value=1, max_value=100)