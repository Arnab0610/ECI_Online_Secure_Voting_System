<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Voter KYC & Verification <small>নাগরিক পরিচয় যাচাইকরণ</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Voter Approval</li>
      </ol>
    </section>

    <!-- Main content -->
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
              <h3 class="box-title">Pending Voter Registrations for Approval</h3>
            </div>
            <div class="box-body table-responsive">
              <table id="example1" class="table table-bordered table-striped table-hover">
                <thead style="background-color: #f8fafc;">
                  <th>Photo</th>
                  <th>Voter ID</th>
                  <th>Full Name</th>
                  <th>Phone Number</th>
                  <th>NID / Document No</th>
                  <th>KYC Status</th>
                  <th>Actions</th>
                </thead>
                <tbody>
                  <?php
                    $sql = "SELECT * FROM voter_applications ORDER BY id DESC";
                    $query = $conn->query($sql);
                    while($row = $query->fetch_assoc()){
                      $image = (!empty($row['photo'])) ? '../images/'.$row['photo'] : '../images/profile.jpg';
                      $status_badge = ($row['is_verified'] == 1) 
                        ? "<span class='label label-success'><i class='fa fa-check'></i> Verified</span>" 
                        : "<span class='label label-warning'><i class='fa fa-clock-o'></i> Pending</span>";
                      
                      echo "
                        <tr>
                          <td>
                            <img src='".$image."' width='35px' height='35px' style='border-radius:50%; object-fit:cover;'>
                          </td>
                          <td><b>".htmlspecialchars($row['voters_id'])."</b></td>
                          <td>".htmlspecialchars($row['firstname'].' '.$row['lastname'])."</td>
                          <td>".htmlspecialchars($row['phone'] ?? 'N/A')."</td>
                          <td>".htmlspecialchars($row['nid_number'] ?? 'N/A')."</td>
                          <td>".$status_badge."</td>
                          <td>";
                          
                      if($row['is_verified'] == 0){
                        echo "<a href='voter_verify_action.php?action=approve&id=".$row['id']."' class='btn btn-success btn-xs btn-flat'><i class='fa fa-check'></i> Approve</a> ";
                      } else {
                        echo "<a href='voter_verify_action.php?action=revoke&id=".$row['id']."' class='btn btn-warning btn-xs btn-flat'><i class='fa fa-ban'></i> Revoke</a> ";
                      }
                      echo "<a href='voter_verify_action.php?action=delete&id=".$row['id']."' class='btn btn-danger btn-xs btn-flat' onclick=\"return confirm('Are you sure you want to delete this voter application?');\"><i class='fa fa-trash'></i></a>
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