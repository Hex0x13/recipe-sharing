<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  include('./includes/dbcon.php');
  $error = false;
  // Validate email
  $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
  if ($email === false) {
      $error = true;
  }

  // Validate password
  $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
  if (empty($password)) {
      $error = true;
  }

  $db = new Dbcon();
  $user = $db->getUserByAuth($email, $password);
  if (isset($user) && !$error) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    if (!empty($user['profile_img'])) {
      $_SESSION['user_profile'] = $user['profile_img'];
    } else {
      $_SESSION['user_profile'] = './assets/dist/img/no-profile.svg';
    }
    header('Location: index.php');
  } else {
    $_SESSION['error'] = "Invalid email or password";
    header("Location: login.php");
  }
}