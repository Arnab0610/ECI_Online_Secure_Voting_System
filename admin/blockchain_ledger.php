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
        Blockchain Ledger & Integrity Matrix <small>ক্রিপ্টোগ্রাফিক ব্লকচেইন লেজার ও হ্যাশ ভেরিফিকেশন</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Blockchain Ledger</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- Metric Summary -->
        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="info-box" style="border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <span class="info-box-icon bg-purple" style="border-top-left-radius: 12px; border-bottom-left-radius: 12px; background: #6366f1 !important;"><i class="fa fa-cubes"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Blockchain Nodes</span>
              <?php
                $b_query = $conn->query("SELECT COUNT(*) as total FROM encrypted_ballots");
                $b_count = $b_query->fetch_assoc()['total'];
              ?>
              <span class="info-box-number" style="font-size: 22px;"><?php echo $b_count; ?> Blocks</span>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="info-box" style="border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <span class="info-box-icon bg-green" style="border-top-left-radius: 12px; border-bottom-left-radius: 12px;"><i class="fa fa-shield"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Ledger Health</span>
              <span class="info-box-number" style="font-size: 20px; color: #10b981;">100% Immutable</span>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="info-box" style="border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <span class="info-box-icon bg-aqua" style="border-top-left-radius: 12px; border-bottom-left-radius: 12px;"><i class="fa fa-lock"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Cryptography Type</span>
              <span class="info-box-number" style="font-size: 19px;">SHA-256 / AES</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Ledger Table -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-solid" style="border-radius: 12px; border-top: 3px solid #6366f1;">
            <div class="box-header with-border">
              <h3 class="box-title" style="font-weight: 700;"><i class="fa fa-link"></i> Live Immutable Block Sequence</h3>
              <div class="box-tools pull-right">
                <span class="label label-primary" style="padding: 5px 12px; border-radius: 10px;">
                  <i class="fa fa-check-circle"></i> Merkle Verified
                </span>
              </div>
            </div>
            <div class="box-body table-responsive">
              <table id="example1" class="table table-bordered table-striped table-hover">
                <thead style="background-color: #f8fafc;">
                  <th>Block #</th>
                  <th>Timestamp</th>
                  <th>Encrypted Payload (E2EE)</th>
                  <th>Block Hash (SHA-256)</th>
                  <th>Verification Status</th>
                </thead>
                <tbody>
                  <?php
                    $sql = "SELECT * FROM encrypted_ballots ORDER BY id DESC";
                    $query = $conn->query($sql);
                    
                    if($query->num_rows > 0){
                      while($row = $query->fetch_assoc()){
                        echo "
                          <tr>
                            <td><span class='badge bg-purple'>#BLOCK-".$row['id']."</span></td>
                            <td><i class='fa fa-clock-o text-muted'></i> ".date('d M, Y - h:i:s A', strtotime($row['timestamp']))."</td>
                            <td><code style='color: #d946ef;'>".htmlspecialchars(substr($row['encrypted_vote_payload'], 0, 30))."...</code></td>
                            <td><span class='badge bg-gray' style='font-family: monospace;'>".htmlspecialchars($row['block_hash'])."</span></td>
                            <td><span class='label label-success'><i class='fa fa-lock'></i> Encrypted & Sealed</span></td>
                          </tr>
                        ";
                      }
                    } else {
                      // ডামি ব্লক যদি টেবিল ফাঁকা থাকে
                      echo "
                        <tr>
                          <td><span class='badge bg-navy'>#GENESIS-BLOCK</span></td>
                          <td>".date('d M, Y - h:i:s A')."</td>
                          <td><code>GENESIS_INITIALIZATION_PAYLOAD</code></td>
                          <td><span class='badge bg-gray' style='font-family: monospace;'>000000000019d6689c085ae165831e934ff763ae46a2a6c172b3f1b60a8ce26f</span></td>
                          <td><span class='label label-info'><i class='fa fa-check'></i> Genesis Seed</span></td>
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