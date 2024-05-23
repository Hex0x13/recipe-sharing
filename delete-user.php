<?php
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    include('./includes/dbcon.php');
    $db = new Dbcon();
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $error = false;
    if ($id === false) {
      $error = true;
    }
    if (!$db->deleteUser($id)) {
      $error = true;
    }

    if ($error) {
      echo "<p style='color:red;'>Deletion Failed</p>";
    } else {
      header('Location: users.php');
    }
} else {
    header('Location: users.php');
}