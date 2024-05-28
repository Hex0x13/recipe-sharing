<?php
include('./includes/dbcon.php');

$db = new Dbcon();

$users = $db->getUsers();

foreach ($users as $user) {
    $userId = $user['id']; // Assuming 'id' is unique for each user
?>
<tr id="user-<?php echo $userId; ?>">
    <td class="id text-center"><?php echo $userId; ?></td>
    <td class="name"><?php echo $user['name']; ?></td>
    <td class="email"><?php echo $user['email']; ?></td>
    <td class="text-center">
      <span class="date"><?php echo $user['date']; ?></span>
    </td>
    <td class="role text-center"><?php echo $user['role']; ?></td>
    <td class="text-center">
      <button type="button" class="p-0 btn text-info btn-sm edit_data_btn" data-toggle="modal" data-target="#editDataModal" data-user-id="<?php echo $userId; ?>" title="click for edit">
        <i class="fas fa-pencil-alt text-primary"></i>
      </button>
      <a href="delete-user.php?id=<?php echo $userId; ?>" class="p-0 btn text-danger btn-sm" onclick="return confirm('Do you really want to delete <?php echo $user['email']; ?> ?');">
        <i class="fas fa-trash"></i>
      </a>
    </td>
</tr>
<?php }?>

<div id="editDataModal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit user info</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <div class="modal-body" id="info_update">
        <div class="card-body">
          <form id="editUserForm" action="./update-user.php" method="post" enctype="multipart/form-data">
            <input type="hidden" id="modalUserId" name="id">
            <div class="row">
              <div class="form-group col-md-6">
                <select id="modalUserRole" class="form-control" name="role" required>
                  <option value="">Select Role</option>
                  <option value="chef">Chef</option>
                  <option value="admin">Admin</option>
                </select>
              </div>
              <div class="form-group col-md-6">
                <input type="text" id="modalUserName" class="form-control" name="name" placeholder="Full Name" required>
                <span id="user-availability-status2" style="font-size:12px;"></span>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-6">
                <input type="email" id="modalUserEmail" class="form-control" name="email" placeholder="Email Address" required>
                <span id="user-availability-status" style="font-size:12px;"></span>
              </div>
              <div class="form-group col-md-6">
                <input type="password" id="modalUserPassword" class="form-control" name="password" placeholder="Password">
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-6">
                <div class="input-group">
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" name="profile" id="InputFile">
                    <label class="custom-file-label" for="InputFile">Choose file</label>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group">
              <input type="submit" class="btn btn-info" value="Update">
              <input type="button" class="btn btn-secondary" data-dismiss="modal" value="Close">
            </div>
          </form>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->
</div>



<script>
  document.addEventListener('click', event => {
    const edit = event.target.closest('.edit_data_btn');
    if (edit) {
      const userId = edit.getAttribute('data-user-id');
      const userRow = document.getElementById('user-' + userId);

      // Populate the form fields in the modal
      document.getElementById('modalUserId').value = userId;
      document.getElementById('modalUserName').value = userRow.querySelector('.name').textContent;
      document.getElementById('modalUserEmail').value = userRow.querySelector('.email').textContent;
      document.getElementById('modalUserRole').value = userRow.querySelector('.role').textContent;
    }
  });

</script>