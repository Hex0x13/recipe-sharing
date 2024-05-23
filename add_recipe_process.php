<?php
include('./includes/Dbcon.php');

// Start a session to use session variables
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create an instance of Dbcon
    $db = new Dbcon();

    // Get the user ID from the session or any other method you use to authenticate users
    $userId = $_SESSION['user_id'] ?? null;

    // Validate user ID
    if (!$userId) {
        $_SESSION['error'] = "User not authenticated.";
    } else {
        // Retrieve form data
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
        $ingredients = filter_input(INPUT_POST, 'ingredients', FILTER_SANITIZE_SPECIAL_CHARS);
        $instructions = filter_input(INPUT_POST, 'instructions', FILTER_SANITIZE_SPECIAL_CHARS);
        $cookingTime = filter_input(INPUT_POST, 'cooking_time', FILTER_SANITIZE_SPECIAL_CHARS);
        $servingSize = filter_input(INPUT_POST, 'serving_size', FILTER_SANITIZE_SPECIAL_CHARS);
        $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_SPECIAL_CHARS);

        // Validate the input (simple example)
        if (empty($title) || empty($ingredients) || empty($instructions)) {
            $_SESSION['error'] = "Title, ingredients, and instructions are required.";
        } else {
            // Add the recipe to the database
            $result = $db->addRecipe($title, $description, $ingredients, $instructions, $cookingTime, $servingSize, $userId, $category);

            if ($result) {
                $_SESSION['success'] = "Recipe added successfully.";
            } else {
                $_SESSION['error'] = "Failed to add recipe.";
            }
        }
    }

    // Redirect to appropriate page
    header("Location: manage-recipe.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: error.php");
    exit();
}
