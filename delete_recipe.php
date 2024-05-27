<?php
include('./includes/Dbcon.php');

// Start a session
session_start();

// Check if the recipe ID is provided
if (isset($_GET['recipe_id'])) {
    $recipeId = $_GET['recipe_id'];

    // Create an instance of Dbcon
    $db = new Dbcon();

    // Delete the recipe from the database
    $result = $db->deleteRecipe($recipeId);

    if ($result) {
        $_SESSION['success'] = "Recipe deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete recipe.";
    }
} else {
    $_SESSION['error'] = "Invalid request.";
}

// Redirect back to the manage-recipe.php page or any other desired page
header("Location: manage-recipe.php");
exit;