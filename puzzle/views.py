"""
Views for the puzzle app containing all the treasure hunt logic.
"""
import math
import random
from django.shortcuts import render, redirect
from .forms import PuzzleForm

def puzzle_form(request):
    """
    Display the puzzle form for user input.
    """
    # Initialize session for treasure hunt if not present
    if 'treasure_target' not in request.session:
        request.session['treasure_target'] = random.randint(1, 100)
        request.session['treasure_attempts'] = []
        request.session['treasure_done'] = False
        request.session['treasure_feedback'] = ''

    feedback = ''
    treasure_done = request.session.get('treasure_done', False)
    attempts = request.session.get('treasure_attempts', [])
    target = request.session.get('treasure_target')

    if request.method == 'POST':
        form = PuzzleForm(request.POST)
        if form.is_valid():
            number = form.cleaned_data['number']
            text = form.cleaned_data['text']
            guess = form.cleaned_data['guess']

            # Number puzzle
            number_result = process_number_puzzle(number)
            # Text puzzle
            text_result = process_text_puzzle(text)

            # Treasure hunt logic
            if not treasure_done:
                attempts.append(guess)
                request.session['treasure_attempts'] = attempts
                if guess == target:
                    feedback = f"🎉 Correct! You found the treasure in {len(attempts)} tries!"
                    request.session['treasure_done'] = True
                elif len(attempts) >= 5:
                    feedback = f"❌ Out of tries! The treasure was {target}."
                    request.session['treasure_done'] = True
                elif guess < target:
                    feedback = f"Too low! Try again. (Your guess: {guess} is less than the treasure)"
                else:
                    feedback = f"Too high! Try again. (Your guess: {guess} is greater than the treasure)"
                request.session['treasure_feedback'] = feedback
            else:
                feedback = request.session.get('treasure_feedback', '')

            # If game is done, reset for next round on next GET
            if request.session['treasure_done']:
                reset_on_next = True
            else:
                reset_on_next = False

            return render(request, 'puzzle/result.html', {
                'number_result': number_result,
                'text_result': text_result,
                'treasure_feedback': feedback,
                'treasure_attempts': attempts,
                'treasure_done': request.session['treasure_done'],
                'treasure_target': target,
                'reset_on_next': reset_on_next,
                'form': form,
            })
    else:
        form = PuzzleForm()
        # If last game was done, reset session for new game
        if request.session.get('treasure_done', False):
            request.session['treasure_target'] = random.randint(1, 100)
            request.session['treasure_attempts'] = []
            request.session['treasure_done'] = False
            request.session['treasure_feedback'] = ''
            feedback = ''
            attempts = []
            target = request.session['treasure_target']

    return render(request, 'puzzle/form.html', {'form': form})

def process_number_puzzle(number):
    """
    Process the number puzzle:
    - If even, calculate square root
    - If odd, calculate cube
    """
    is_even = number % 2 == 0
    return {
        'number': number,
        'result': math.sqrt(number) if is_even else number ** 3,
        'operation': 'square root' if is_even else 'cube'
    }

def process_text_puzzle(text):
    """
    Process the text puzzle:
    - Convert text to binary
    - Count vowels
    """
    vowels = 'aeiouAEIOU'
    vowel_count = sum(1 for char in text if char in vowels)
    binary = ' '.join(bin(ord(char))[2:] for char in text)
    return {
        'text': text,
        'binary': binary,
        'vowel_count': vowel_count
    }

def process_treasure_hunt():
    """
    Simulate a treasure hunt by guessing a random number (1-100).
    User wins if the simulation succeeds in 5 tries or less.
    """
    target = random.randint(1, 100)
    max_tries = 5
    attempts = []
    found = False
    for attempt in range(1, max_tries + 1):
        guess = random.randint(1, 100)
        attempts.append({'attempt': attempt, 'guess': guess, 'target': target, 'correct': guess == target})
        if guess == target:
            found = True
            break
    message = (
        f"🎉 Found the treasure in {len(attempts)} tries! (Target: {target})"
        if found else
        f"❌ Missed! The treasure was {target}."
    )
    return {
        'target': target,
        'attempts': attempts,
        'success': found,
        'message': message
    }