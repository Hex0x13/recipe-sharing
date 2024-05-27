<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('./dbcon.php');
    // Validate name
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    if (empty($name)) {
        $errors['name'] = "Name is required.";
    }

    // Validate email
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if ($email === false) {
        $errors['email'] = "Invalid email format.";
    }

    // Validate password
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    }

    $images = null;
    // Validate file upload
    if (isset($_FILES['profile']) && $_FILES['profile']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $file_name = $_FILES['profile']['name'];
        $file_size = $_FILES['profile']['size'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $max_file_size = 15 * 1024 * 1024; // 15 MB

        if (!in_array($file_ext, $allowed) && ($file_size >= $max_file_size)) {
          $errors['profile'] = "Invalid file type. Allowed types: " . implode(', ', $allowed);
        } else {
          $images = file_get_contents($_FILES['profile']['tmp_name']);
        }
    }

    // Validate role
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_SPECIAL_CHARS);
    $valid_roles = ['admin', 'chef'];
    if (!in_array($role, $valid_roles)) {
        $errors['role'] = "Invalid role selected.";
    }

    // Check if there are any errors
    if (empty($errors)) {
      $db = new Dbcon();
      if ($db->addUser($name, $password, $email, $role, $images)) {
        header('Location: ../users.php');
        unset($db);
      }
    } else {
        // Handle errors
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
}
?>