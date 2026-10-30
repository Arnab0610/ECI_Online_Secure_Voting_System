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
        Security Audit Logs <small>নিরাপত্তা অডিট ও অ্যাক্টিভিটি ট্র্যাকিং</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Audit Logs</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-danger" style="border-radius: 12px; border-top: 3px solid #dd4b39;">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-shield"></i> Immutable Activity Ledger</h3>
              <div class="box-tools pull-right">
                <span class="label label-danger" style="font-size: 11px; padding: 5px 10px; border-radius: 10px;">
                  <i class="fa fa-lock"></i> SHA-256 Audit Trail
                </span>
              </div>
            </div>
            <div class="box-body table-responsive">
              <table id="example1" class="table table-bordered table-striped table-hover">
                <thead style="background-color: #f8fafc;">
                  <th>Timestamp</th>
                  <th>User / Role</th>
                  <th>Action Triggered</th>
                  <th>IP Address</th>
                  <th>User Agent</th>
                </thead>
                <tbody>
                  <?php
                    $sql = "SELECT a.*, adm.username, adm.firstname, adm.lastname 
                            FROM audit_logs a 
                            LEFT JOIN admin adm ON a.user_id = adm.id 
                            ORDER BY a.id DESC";
                    $query = $conn->query($sql);
                    while($row = $query->fetch_assoc()){
                      $name = !empty($row['firstname']) ? htmlspecialchars($row['firstname'].' '.$row['lastname']) : 'Admin ID: #'.$row['user_id'];
                      $role_badge = ($row['user_role'] == 'super_admin') 
                        ? "<span class='label label-danger'>Super Admin</span>" 
                        : "<span class='label label-info'>".htmlspecialchars(ucfirst($row['user_role']))."</span>";

                      echo "
                        <tr>
                          <td style='white-space: nowrap;'><i class='fa fa-clock-o text-muted'></i> ".date('d M, Y - h:i:s A', strtotime($row['log_time']))."</td>
                          <td>
                            <b>".$name."</b><br>
                            ".$role_badge."
                          </td>
                          <td><code>".htmlspecialchars($row['action'])."</code></td>
                          <td><span class='badge bg-gray'>".htmlspecialchars($row['ip_address'])."</span></td>
                          <td><small class='text-muted'>".htmlspecialchars(substr($row['user_agent'], 0, 70))."...</small></td>
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