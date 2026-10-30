<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <?php
  // অটোমেটিক নির্বাচন স্ট্যাটাস সিঙ্ক (Auto-activation Engine)
  date_default_timezone_set('Asia/Kolkata');
  $now_dt = date('Y-m-d H:i:s');

  // সময় হলে স্বয়ংক্রিয় লাইভ
  $conn->query("UPDATE elections SET status = 'live' WHERE status = 'scheduled' AND start_time <= '$now_dt' AND end_time > '$now_dt'");
  
  // সময় পার হলে স্বয়ংক্রিয় সমাপ্ত
  $conn->query("UPDATE elections SET status = 'completed' WHERE status = 'live' AND end_time < '$now_dt'");
?>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Election Schedule & Management <small>নির্বাচন সময়সূচী ও নিয়ন্ত্রণ</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Schedule Election</li>
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
          <div class="box box-primary" style="border-radius: 12px; border-top: 3px solid #3c8dbc;">
            <div class="box-header with-border">
              <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat" style="border-radius: 6px;"><i class="fa fa-plus"></i> Create New Election</a>
            </div>
            <div class="box-body table-responsive">
              <table id="example1" class="table table-bordered table-striped table-hover">
                <thead style="background-color: #f8fafc;">
                  <th>Title</th>
                  <th>Start Time</th>
                  <th>End Time</th>
                  <th>Encryption Mode</th>
                  <th>Live Status</th>
                  <th>Actions</th>
                </thead>
                <tbody>
                  <?php
                    $sql = "SELECT * FROM elections ORDER BY id DESC";
                    $query = $conn->query($sql);
                    while($row = $query->fetch_assoc()){
                      $status = $row['status'];
                      if($status == 'live'){
                        $badge = "<span class='label label-success'><i class='fa fa-circle'></i> LIVE</span>";
                      } elseif($status == 'scheduled'){
                        $badge = "<span class='label label-info'><i class='fa fa-clock-o'></i> Scheduled</span>";
                      } elseif($status == 'paused'){
                        $badge = "<span class='label label-warning'><i class='fa fa-pause'></i> Paused</span>";
                      } else {
                        $badge = "<span class='label label-danger'><i class='fa fa-stop'></i> Completed</span>";
                      }

                      $enc_badge = ($row['is_encrypted'] == 1) 
                        ? "<span class='label label-primary'><i class='fa fa-lock'></i> E2EE Active</span>"
                        : "<span class='label label-default'>Standard</span>";

                      echo "
                        <tr>
                          <td><b>".htmlspecialchars($row['title'])."</b></td>
                          <td>".date('d M, Y - h:i A', strtotime($row['start_time']))."</td>
                          <td>".date('d M, Y - h:i A', strtotime($row['end_time']))."</td>
                          <td>".$enc_badge."</td>
                          <td>".$badge."</td>
                          <td>
                            <div class='btn-group'>
                              <button type='button' class='btn btn-default btn-xs dropdown-toggle' data-toggle='dropdown'>
                                Change Status <span class='caret'></span>
                              </button>
                              <ul class='dropdown-menu dropdown-menu-right'>
                                <li><a href='election_action.php?action=status&val=live&id=".$row['id']."'>Set to Live</a></li>
                                <li><a href='election_action.php?action=status&val=paused&id=".$row['id']."'>Pause Election</a></li>
                                <li><a href='election_action.php?action=status&val=completed&id=".$row['id']."'>Mark Completed</a></li>
                              </ul>
                            </div>
                            <a href='election_action.php?action=delete&id=".$row['id']."' class='btn btn-danger btn-xs btn-flat' onclick=\"return confirm('Are you sure you want to delete this election schedule?');\"><i class='fa fa-trash'></i></a>
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

  <!-- Add New Election Modal -->
  <div class="modal fade" id="addnew">
    <div class="modal-dialog">
      <div class="modal-content" style="border-radius: 12px;">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><b>Schedule New Election / নির্বাচন নির্ধারণ</b></h4>
        </div>
        <form class="form-horizontal" method="POST" action="election_action.php?action=add">
          <div class="modal-body">
            <div class="form-group">
              <label for="title" class="col-sm-3 control-label">Election Title</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="title" name="title" placeholder="যেমন: জাতীয় নির্বাচন ২০২৬" required>
              </div>
            </div>
            <div class="form-group">
              <label for="start_time" class="col-sm-3 control-label">Start Date & Time</label>
              <div class="col-sm-9">
                <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
              </div>
            </div>
            <div class="form-group">
              <label for="end_time" class="col-sm-3 control-label">End Date & Time</label>
              <div class="col-sm-9">
                <input type="datetime-local" class="form-control" id="end_time" name="end_time" required>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
            <button type="submit" class="btn btn-primary btn-flat" name="add"><i class="fa fa-save"></i> Save Schedule</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>
</body>
</html>