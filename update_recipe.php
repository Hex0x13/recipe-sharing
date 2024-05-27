<?php
include('./includes/Dbcon.php');

// Start a session
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create an instance of Dbcon
    $db = new Dbcon();

    // Get the recipe ID and updated data from the form
    $recipeId = $_POST['recipe_id'];
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
    $ingredients = filter_input(INPUT_POST, 'ingredients', FILTER_SANITIZE_SPECIAL_CHARS);
    $instructions = filter_input(INPUT_POST, 'instructions', FILTER_SANITIZE_SPECIAL_CHARS);
    $cookingTime = filter_input(INPUT_POST, 'cooking_time', FILTER_SANITIZE_SPECIAL_CHARS);
    $servingSize = filter_input(INPUT_POST, 'serving_size', FILTER_SANITIZE_SPECIAL_CHARS);
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_SPECIAL_CHARS);

    // Update the recipe in the database
    $result = $db->updateRecipe($id, $title, $description, $ingredients, $instructions, $cookingTime, $servingSize, $categoryName);

    if ($result) {
        $_SESSION['success'] = "Recipe updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update recipe.";
    }
}

// Redirect back to the manage-recipe.php page
header("Location: manage-recipe.php");
exit;