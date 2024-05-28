<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('./includes/dbcon.php');
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($password)) {
      $password = null;
    }

    $profile = null;
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
          $src = $_FILES['profile']['tmp_name'];
          $profile = './assets/profile/' . uniqid() . $file_name;
          move_uploaded_file($src, $profile);
        }
    }

    // Validate role
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_SPECIAL_CHARS);
    $valid_roles = ['admin', 'chef'];
    if (!in_array($role, $valid_roles)) {
      $role = null;
    }

    // Check if there are any errors
    $db = new Dbcon();
    if ($user = $db->updateUser($id, $name, $password, $email, $role, $profile)) {
      $user = $db->getUserById($id);
      if (!empty($user['profile_img']) && (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $id)) {
        $_SESSION['user_profile'] = $user['profile_img'];
      } else {
        $_SESSION['user_profile'] = './assets/dist/img/no-profile.svg';
      }
      header('Location: ./users.php');

    }
}