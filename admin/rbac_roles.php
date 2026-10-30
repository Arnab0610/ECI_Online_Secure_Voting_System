<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Role-Based Access Control (RBAC) <small>অ্যাডমিন রোল ও পারমিশন ব্যবস্থাপনা</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">RBAC Roles</li>
      </ol>
    </section>

    <section class="content">
      <?php
        if(isset($_SESSION['error'])){
          echo "
            <div class='alert alert-danger alert-dismissible' style='border-radius: 8px;'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Error!</h4>
              ".$_SESSION['error']."
            </div>
          ";
          unset($_SESSION['error']);
        }
        if(isset($_SESSION['success'])){
          echo "
            <div class='alert alert-success alert-dismissible' style='border-radius: 8px;'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              ".$_SESSION['success']."
            </div>
          ";
          unset($_SESSION['success']);
        }
      ?>

      <div class="row">
        <div class="col-xs-12">
          <div class="box box-primary" style="border-radius: 12px; border-top: 3px solid #00c0ef;">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-user-secret"></i> System Administrators & Privilege Matrix</h3>
            </div>
            <div class="box-body table-responsive">
              <table id="example1" class="table table-bordered table-striped table-hover">
                <thead style="background-color: #f8fafc;">
                  <th>Photo</th>
                  <th>Full Name</th>
                  <th>Username</th>
                  <th>Assigned Role</th>
                  <th>Permissions</th>
                  <th>Change Role Action</th>
                </thead>
                <tbody>
                  <?php
                    $sql = "SELECT * FROM admin ORDER BY id ASC";
                    $query = $conn->query($sql);
                    while($row = $query->fetch_assoc()){
                      $image = (!empty($row['photo'])) ? '../images/'.$row['photo'] : '../images/profile.jpg';
                      $role = $row['role'] ?? 'super_admin';

                      if($role == 'super_admin'){
                        $role_badge = "<span class='label label-danger'><i class='fa fa-star'></i> Super Admin</span>";
                        $permissions = "Full System Control, Kill Switch, Vault Decryption";
                      } elseif($role == 'election_officer') {
                        $role_badge = "<span class='label label-success'><i class='fa fa-check'></i> Election Officer</span>";
                        $permissions = "Voter Verification, Candidate Slate Management";
                      } else {
                        $role_badge = "<span class='label label-info'><i class='fa fa-eye'></i> Auditor</span>";
                        $permissions = "View Only: Audit Logs & Live Tally Inspection";
                      }

                      echo "
                        <tr>
                          <td><img src='".$image."' width='35px' height='35px' style='border-radius:50%; object-fit:cover;'></td>
                          <td><b>".htmlspecialchars($row['firstname'].' '.$row['lastname'])."</b></td>
                          <td>".htmlspecialchars($row['username'])."</td>
                          <td>".$role_badge."</td>
                          <td><small class='text-muted'>".$permissions."</small></td>
                          <td>
                            <div class='btn-group'>
                              <button type='button' class='btn btn-default btn-xs dropdown-toggle' data-toggle='dropdown'>
                                Assign Role <span class='caret'></span>
                              </button>
                              <ul class='dropdown-menu dropdown-menu-right'>
                                <li><a href='rbac_action.php?role=super_admin&id=".$row['id']."'>Set as Super Admin</a></li>
                                <li><a href='rbac_action.php?role=election_officer&id=".$row['id']."'>Set as Election Officer</a></li>
                                <li><a href='rbac_action.php?role=auditor&id=".$row['id']."'>Set as Auditor</a></li>
                              </ul>
                            </div>
                          </td>
                        </tr>
                      ";
                    }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>
</body>
</html>