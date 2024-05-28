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
        // Check if the user exists
        if (!$db->userExists($userId)) {
            $_SESSION['error'] = "Invalid user ID.";
            header("Location: error.php");
            exit();
        }

        // Retrieve form data
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
        $ingredients = filter_input(INPUT_POST, 'ingredients', FILTER_SANITIZE_SPECIAL_CHARS);
        $instructions = filter_input(INPUT_POST, 'instructions', FILTER_SANITIZE_SPECIAL_CHARS);
        $specialInstructions = filter_input(INPUT_POST, 'specialInstructions', FILTER_SANITIZE_SPECIAL_CHARS);
        $cookingTime = filter_input(INPUT_POST, 'cooking_time', FILTER_SANITIZE_SPECIAL_CHARS);
        $servingSize = filter_input(INPUT_POST, 'serving_size', FILTER_SANITIZE_SPECIAL_CHARS);
        $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_SPECIAL_CHARS);
        $images = $_FILES['images'] ?? [];

        // Validate the input (simple example)
        if (empty($title) || empty($ingredients) || empty($instructions)) {
            $_SESSION['error'] = "Title, ingredients, and instructions are required.";
        } else {
            $images = array(); // Initialize an empty array to store the file paths

            // Validate file upload
            if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                $allowed = ['jpg', 'jpeg', 'png'];
                $upload_dir = './assets/uploads/';

                foreach ($_FILES['images']['name'] as $key => $file_name) {
                    $file_size = $_FILES['images']['size'][$key];
                    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                    $max_file_size = 15 * 1024 * 1024; // 15 MB

                    if (!in_array($file_ext, $allowed) || $file_size >= $max_file_size) {
                        $errors['images'][] = "Invalid file type or size for file $file_name.";
                    } else {
                        $image_path = $upload_dir . uniqid() . '_' . $file_name;
                        if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $image_path)) {
                            // File uploaded successfully
                            $images[] = $image_path; // Append the image path to the $images array
                            echo "File uploaded successfully: $image_path<br>";
                        } else {
                            $errors['images'][] = "Failed to upload file: $file_name.";
                        }
                    }
                }
            }

            $result = $db->updateRecipe($id, $title, $description, $ingredients, $instructions, $specialInstructions, $cookingTime, $servingSize, $category, $images);

            if ($result) {
                $_SESSION['success'] = "Recipe updated successfully.";
            } else {
                $_SESSION['error'] = "Failed to update recipe.";
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