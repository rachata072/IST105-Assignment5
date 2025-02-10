import sys
import math
import re
import codecs

# Force UTF-8 encoding for Windows compatibility
sys.stdout = codecs.getwriter("utf-8")(sys.stdout.buffer)

# Ensure correct arguments
if len(sys.argv) < 2:
    print("Error: Missing arguments! Expected a task.")
    sys.exit(1)

task = sys.argv[1]

# Number & Text Processing
if task == "process":
    if len(sys.argv) < 4:
        print("Error: Missing number and text input!")
        sys.exit(1)

    number = int(sys.argv[2])
    text = sys.argv[3]

    # Number Puzzle
    if number % 2 == 0:
        num_result = f"The number {number} is even. Its square root is {math.sqrt(number):.2f}."
    else:
        num_result = f"The number {number} is odd. Its cube is {number ** 3}."

    # Text to Binary & Vowel Count
    binary_text = ' '.join(format(ord(char), '08b') for char in text)
    vowel_count = len(re.findall(r'[aeiouAEIOU]', text))
    text_result = f"Binary: {binary_text}\nVowel Count: {vowel_count}"

    print(num_result)
    print(text_result)

# Treasure Hunt Game (Using While Loop)
elif task == "treasure":
    if len(sys.argv) < 4:
        print("Error: Missing arguments for treasure hunt!")
        sys.exit(1)

    secret_number = int(sys.argv[2])
    guess = int(sys.argv[3])
    attempts = int(sys.argv[4])

    while attempts <= 5:  # Ensures we process exactly 5 attempts
        if guess == secret_number:
            print(f"Attempt {attempts}: {guess} (Correct!)")
            print(f"You found the treasure in {attempts} attempt{'s' if attempts > 1 else ''}!")
            sys.exit(0)

        elif guess < secret_number:
            print(f"Attempt {attempts}: {guess} (Too low!)")
        else:
            print(f"Attempt {attempts}: {guess} (Too high!)")

        # If this is the last allowed attempt, check if it's still incorrect
        if attempts == 5 and guess != secret_number:
            print(f"Sorry, you lost! The secret number was {secret_number}.")
            sys.exit(0)

        break  # Stop the while loop after one guess is processed
