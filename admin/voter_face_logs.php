<?php
  include 'includes/session.php';
  include 'includes/header.php';
?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        📸 ভোটার ফেস অথেন্টিকেশন অডিট লগ
        <small>Live Biometric Login History</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> হোম</a></li>
        <li class="active">Face Logs</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">লগইন প্রচেষ্টা ও ক্যাপচার করা ছবির খতিয়ান</h3>
            </div>
            <div class="box-body table-responsive">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr style="background:#0f172a; color:#fff;">
                    <th>SL</th>
                    <th>ভোটার আইডি</th>
                    <th>ভোটারের নাম</th>
                    <th>ক্যাপচার করা ফেস</th>
                    <th>ডিস্ট্যান্স স্কোর</th>
                    <th>আইপি অ্যাড্রেস</th>
                    <th>লগইন সময়</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $sql = "SELECT l.*, v.firstname, v.lastname 
                            FROM voter_login_logs l 
                            LEFT JOIN voters v ON l.voter_id = v.id 
                            ORDER BY l.login_time DESC";
                    $query = $conn->query($sql);
                    $sl = 1;
                    while($row = $query->fetch_assoc()){
                      $photo_path = '../images/' . $row['captured_photo'];
                      $img_src = (!empty($row['captured_photo']) && file_exists($photo_path)) ? $photo_path : '../images/profile.jpg';
                      echo "
                        <tr>
                          <td>".$sl++."</td>
                          <td><span class='label label-primary' style='font-family:monospace; font-size:13px;'>".$row['voters_id']."</span></td>
                          <td><b>".$row['firstname']." ".$row['lastname']."</b></td>
                          <td style='text-align:center;'>
                            <img src='".$img_src."' style='width:50px; height:50px; border-radius:6px; object-fit:cover; border:1.5px solid #0b63c5;'>
                          </td>
                          <td><span class='badge bg-green'>".$row['distance_score']."</span></td>
                          <td><code>".$row['ip_address']."</code></td>
                          <td>".date('d M Y, h:i A', strtotime($row['login_time']))."</td>
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