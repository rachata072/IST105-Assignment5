<?php
session_start();  // Start session to store game data
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Step 1: If user submits number & text, process them first
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["number"]) && isset($_POST["text"])) {
    $number = escapeshellarg($_POST["number"]);
    $text = escapeshellarg($_POST["text"]);

    // Call Python script for number & text processing
    $command = "C:\\Python313\\python.exe process.py process $number $text 2>&1";
    $output = shell_exec($command);

    if ($output !== null) {
        $_SESSION["messages"][] = trim($output);
    }

    // Start new treasure hunt session
    $_SESSION["secret_number"] = rand(1, 100);
    $_SESSION["attempts"] = 0;
    $_SESSION["game_over"] = false;
    $_SESSION["messages"][] = "Treasure Hunt Game Started! Try guessing the number (1-100).";
}

// Step 2: If user submits a guess, process it
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["guess"])) {
    if (isset($_SESSION["secret_number"]) && $_SESSION["game_over"] === false) {
        $guess = escapeshellarg($_POST["guess"]);
        $_SESSION["attempts"]++;

        // Call Python script for the treasure hunt
        $command = "C:\\Python313\\python.exe process.py treasure {$_SESSION['secret_number']} $guess {$_SESSION['attempts']} 2>&1";
        $output = shell_exec($command);

        if ($output !== null) {
            $_SESSION["messages"][] = trim($output);
        }

        // If user wins or uses all 5 attempts, mark game as over
        if ($_SESSION["attempts"] >= 5 || strpos($output, "Correct!") !== false) {
            $_SESSION["game_over"] = true;
        }
    }
}

// Reset game when "Try Again" is clicked
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["reset"])) {
    session_destroy();
    header("Location: form.php");  // Reload page
    exit();
}
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
    <form method="post">
        <h3>Step 1: Enter a Number & Text</h3>
        <label>Enter a Number (e.g., birth year):</label>
        <input type="number" name="number" required><br>

        <label>Enter Text (e.g., a secret word):</label>
        <input type="text" name="text" required><br>

        <button type="submit">Submit & Process</button>
    </form>

    <?php if (isset($_SESSION["secret_number"])): ?>
        <!-- Step 2: Play the Treasure Hunt (Only appears after Step 1) -->
        <?php if ($_SESSION["game_over"] === false): ?>
            <form method="post">
                <h3>Step 2: Play the Treasure Hunt</h3>
                <label>Enter Your Guess (1-100):</label>
                <input type="number" name="guess" min="1" max="100" required>
                <button type="submit">Submit Guess</button>
            </form>
        <?php else: ?>
            <!-- Show Try Again Button when game is over -->
            <form method="post">
                <h3>Game Over! Try Again?</h3>
                <button type="submit" name="reset">Start New Game</button>
            </form>
        <?php endif; ?>
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
