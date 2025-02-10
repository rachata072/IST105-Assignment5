<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Interactive Treasure Hunt</title>
</head>
<body>
    <h2>Welcome to the Interactive Treasure Hunt!</h2>

    <!-- Step 1: Input Number & Text -->
    <form action="process.php" method="post">
        <h3>Step 1: Enter a Number & Text</h3>
        <label>Enter a Number (e.g., birth year):</label>
        <input type="number" name="number" required><br>

        <label>Enter Text (e.g., a secret word):</label>
        <input type="text" name="text" required><br>

        <button type="submit" name="submit_number_text">Submit & Process</button>
    </form>

    <?php if (isset($_SESSION["secret_number"])): ?>
        <!-- Step 2: Play the Treasure Hunt (Only appears after Step 1) -->
        <form action="process.php" method="post">
            <h3>Step 2: Play the Treasure Hunt</h3>
            <label>Enter Your Guess (1-100):</label>
            <input type="number" name="guess" min="1" max="100" required>
            <button type="submit" name="submit_guess">Submit Guess</button>
        </form>
    <?php endif; ?>

    <?php if (isset($_SESSION["game_over"]) && $_SESSION["game_over"] === true): ?>
        <form action="process.php" method="post">
            <h3>Game Over! Try Again?</h3>
            <button type="submit" name="reset">Start New Game</button>
        </form>
    <?php endif; ?>

    <h3>Game Status:</h3>
    <p>
        <?php
        if (isset($_SESSION["messages"])) {
            foreach ($_SESSION["messages"] as $message) {
                echo "<pre>$message</pre>";
            }
        }
        ?>
    </p>
</body>
</html>
