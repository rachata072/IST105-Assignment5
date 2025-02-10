<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Process Number & Text Input
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_number_text"])) {
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

    header("Location: form.php");
    exit();
}

// Process Guess for Treasure Hunt
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_guess"])) {
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

    header("Location: form.php");
    exit();
}

// Reset Game
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["reset"])) {
    session_destroy();
    header("Location: form.php");
    exit();
}
?>
