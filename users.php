<?php
include('./includes/check-login.php');
include('./includes/header.php');

if ($_SESSION['user_role'] !== 'admin') {
  header('Location: index.php');
  exit;
}

include('./includes/topbar.php');
include('./includes/sidebar.php');
?>
<div class="content-wrapper p-5">
<section class="content">
  <div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
        <div class="modal-header">
          <h5 class="modal-title" style="float: left;">Register user</h5>
          <div class="card-tools" style="float: right;">
            <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#registeruser"><i
                class="fas fa-plus"></i> Register User
            </button>
          </div>
        </div>
        <!-- /.card-header -->
        <div class="modal fade" id="registeruser">
          <div class="modal-dialog ">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">Register user</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="card-body">
                  <form action="./includes/add-user.php" method="post" enctype="multipart/form-data">
                    <div class="row ">
                      <div class="form-group col-md-6">
                        <select class="form-control" name="role" required="">
                          <option value="">Select Role</option>
                          <option value="chef">Chef</option>
                          <option value="admin">Admin</option>
                        </select>
                      </div>
                      <div class="form-group col-md-6">
                        <input type="text" class="form-control" name="name" placeholder="Full Name"
                          required="required">
                        <span id="user-availability-status2" style="font-size:12px;"></span>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group col-md-6">
                        <input type="email" class="form-control" name="email"
                          placeholder="Email Address" required="required">
                        <span id="user-availability-status" style="font-size:12px;"></span>
                      </div>
                      <div class="form-group col-md-6">
                        <input type="password" class="form-control" name="password" placeholder="Password"
                          required="required">
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group col-md-6">
                        <div class="input-group">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" name="profile" id="exampleInputFile">
                            <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <input type="submit" value="Register" class="btn btn-info">
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>

        <!--  start  modal -->

        <!--   end modal -->
        <div class="card-body table-responsive p-3">
          <table class="table align-items-center table-flush table-hover" id="dataTableHover">
            <thead>
              <tr>
                <th class="text-center">#</th>
                <th class="">Name</th>
                <th class="">Email</th>
                <th class=" text-center">Date registered</th>
                <th class="text-center">Role</th>
                <th class="text-center" style="width: 15%;">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              include('./includes/display-user.php');
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>
</div>

<?php
include('./includes/footer.php');
?>