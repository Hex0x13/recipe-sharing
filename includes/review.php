<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  die("Invalid Request.");
}

$rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
$comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_SPECIAL_CHARS);
$user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
$recipe_id = filter_input(INPUT_POST, 'recipe_id', FILTER_VALIDATE_INT);


if (empty($rating) || empty($comment) || empty($recipe_id)) {
  die("Invalid Input");
}

include('./dbcon.php');

$db = new Dbcon();
$db->reviewRecipe($rating, $comment, $user_id, $recipe_id);
header("Location: " . $_SERVER['HTTP_REFERER']);