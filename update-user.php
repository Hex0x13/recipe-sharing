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
          $profile = null;
        } else {
          $profile = file_get_contents($_FILES['profile']['tmp_name']);
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
    if ($db->updateUser($id, $name, $password, $email, $role, $profile)) {
      if (!empty($profile)) {
        $_SESSION['user_profile'] = 'data:image/png;base64,' . base64_encode($profile);
      } else {
        $_SESSION['user_profile'] = 'assets/dist/img/user2-160x160.jpg';
      }
      header('Location: ./users.php');

    }
}