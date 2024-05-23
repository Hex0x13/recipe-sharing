<style>
  #userEmail::after {
    position: relative;
    content: attr(data-user-email);
  }
</style>

<?php
include('./includes/dbcon.php');

$db = new Dbcon();

$users = $db->getUsers();

foreach ($users as $user) {
  echo "
  <tr>
    <td>
      {$user['id']}
    </td>
    <td>{$user['name']}</td>
    <td>{$user['email']}</td>
    <td>{$user['date']}</td>
    <td>{$user['role']}</td>
    <td>
      <a href=\"update-user.php?id={$user['id']}\" class=\"btn btn-success\">Update</a>
      <a href=\"delete-user.php?id={$user['id']}\" class=\"btn btn-danger\">Delete</a>
    </td>
  </tr>
  ";
}
?>