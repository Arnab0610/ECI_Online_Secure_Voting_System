<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>নির্বাচন পোর্টাল • Form 6 যাচাই ও অনুমোদন বোর্ড (BLO / ERO)</h1>
    </section>

    <section class="content">
      <?php
        if(isset($_SESSION['success'])){
          echo "<div class='alert alert-success alert-dismissible'><button type='button' class='close' data-dismiss='alert'>&times;</button>".$_SESSION['success']."</div>";
          unset($_SESSION['success']);
        }
        if(isset($_SESSION['error'])){
          echo "<div class='alert alert-danger alert-dismissible'><button type='button' class='close' data-dismiss='alert'>&times;</button>".$_SESSION['error']."</div>";
          unset($_SESSION['error']);
        }
      ?>

      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-tasks"></i> সকল আবেদন তালিকা ও স্ট্যাটাস</h3>
        </div>
        <div class="box-body table-responsive">
          <table class="table table-bordered table-striped" id="example1">
            <thead>
              <th>Ref ID</th>
              <th>আবেদনকারীর নাম</th>
              <th>মোবাইল</th>
              <th>বিধানসভা কেন্দ্র</th>
              <th>ডকুমেন্টস</th>
              <th>বর্তমান স্ট্যাটাস</th>
              <th>অ্যাকশন (BLO / ERO)</th>
            </thead>
            <tbody>
              <?php
                $query = $conn->query("SELECT * FROM voter_applications ORDER BY id DESC");
                while($row = $query->fetch_assoc()){
                  $photo = (!empty($row['photo'])) ? '../images/'.$row['photo'] : '../images/profile.jpg';
                  $dob_doc = (!empty($row['dob_doc_file'])) ? '../images/'.$row['dob_doc_file'] : '#';
                  $addr_doc = (!empty($row['address_doc_file'])) ? '../images/'.$row['address_doc_file'] : '#';

                  echo "<tr>
                    <td><b>{$row['ref_no']}</b></td>
                    <td>{$row['firstname']} {$row['lastname']}<br><small style='color:#64748b;'>{$row['firstname_bn']} {$row['lastname_bn']}</small></td>
                    <td>{$row['mobile']}</td>
                    <td>{$row['constituency']}</td>
                    <td>
                      <a href='{$photo}' target='_blank' class='btn btn-default btn-xs'><i class='fa fa-user'></i> ছবি</a>
                      <a href='{$dob_doc}' target='_blank' class='btn btn-default btn-xs'><i class='fa fa-calendar'></i> জন্ম প্রমাণপত্র</a>
                      <a href='{$addr_doc}' target='_blank' class='btn btn-default btn-xs'><i class='fa fa-file-text'></i> ঠিকানা প্রমাণপত্র</a>
                    </td>
                    <td>";
                      if($row['status'] == 'PENDING_BLO') echo "<span class='label label-warning'>Stage 1: BLO Pending</span>";
                      elseif($row['status'] == 'BLO_APPROVED') echo "<span class='label label-info'>Stage 2: ERO Approval Pending</span>";
                      elseif($row['status'] == 'ERO_APPROVED') echo "<span class='label label-success'>Approved & EPIC Generated</span>";
                      elseif($row['status'] == 'BLO_REJECTED') echo "<span class='label label-danger'>BLO Rejected</span>";
                      elseif($row['status'] == 'ERO_REJECTED') echo "<span class='label label-danger'>ERO Rejected</span>";
                  echo "</td>
                    <td>";
                      // Stage 1: BLO Action
                      if($row['status'] == 'PENDING_BLO'){
                        echo "
                          <form method='POST' action='kyc_workflow_action.php' style='display:inline;'>
                            <input type='hidden' name='id' value='{$row['id']}'>
                            <input type='hidden' name='stage' value='BLO'>
                            <input type='hidden' name='action' value='approve'>
                            <button type='submit' class='btn btn-sm btn-success btn-flat'><i class='fa fa-check'></i> BLO ভেরিফাই</button>
                          </form>
                          <form method='POST' action='kyc_workflow_action.php' style='display:inline;'>
                            <input type='hidden' name='id' value='{$row['id']}'>
                            <input type='hidden' name='stage' value='BLO'>
                            <input type='hidden' name='action' value='reject'>
                            <button type='submit' class='btn btn-sm btn-danger btn-flat'><i class='fa fa-times'></i> বাতিল</button>
                          </form>
                        ";
                      }
                      // Stage 2: ERO Final Approval & EPIC Generation
                      elseif($row['status'] == 'BLO_APPROVED'){
                        echo "
                          <form method='POST' action='kyc_workflow_action.php' style='display:inline;'>
                            <input type='hidden' name='id' value='{$row['id']}'>
                            <input type='hidden' name='stage' value='ERO'>
                            <input type='hidden' name='action' value='approve'>
                            <button type='submit' class='btn btn-sm btn-primary btn-flat' style='background:#0b63c5;'><i class='fa fa-id-card'></i> ERO অনুমোদন ও EPIC জেনারেট</button>
                          </form>
                          <form method='POST' action='kyc_workflow_action.php' style='display:inline;'>
                            <input type='hidden' name='id' value='{$row['id']}'>
                            <input type='hidden' name='stage' value='ERO'>
                            <input type='hidden' name='action' value='reject'>
                            <button type='submit' class='btn btn-sm btn-danger btn-flat'><i class='fa fa-times'></i> বাতিল</button>
                          </form>
                        ";
                      } else {
                        echo "<span style='color:#10b981; font-weight:700;'><i class='fa fa-check-circle'></i> সম্পন্ন</span>";
                      }
                  echo "</td>
                  </tr>";
                }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </div>
  <?php include 'includes/footer.php'; ?>
</div>
<?php include 'includes/scripts.php'; ?>
</body>
</html>