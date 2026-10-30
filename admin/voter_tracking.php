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
        Voter Turnout Tracking <small>রিয়েল-টাইম ভোটার উপস্থিতি ও ট্র্যাকিং</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Turnout Tracking</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <?php
        // মোট অনুমোদিত ভোটার সংখ্যা
        // ২৬ নম্বর লাইনের পুরনো কুয়েরিটি এর বদলে এটি বসিয়ে দিন:
        $sql_total = "SELECT COUNT(*) as total FROM voters";
        $q_total = $conn->query($sql_total);
        $total_voters = $q_total->fetch_assoc()['total'];

        // কতজন ভোট প্রদান করেছেন
        $sql_voted = "SELECT COUNT(DISTINCT voters_id) as total FROM votes";
        $q_voted = $conn->query($sql_voted);
        $voted_count = $q_voted->fetch_assoc()['total'];

        // ভোট বাকি রয়েছে
        $pending_votes = ($total_voters > $voted_count) ? ($total_voters - $voted_count) : 0;
        
        // শতকরা হার (Turnout Percentage)
        $turnout_percentage = ($total_voters > 0) ? round(($voted_count / $total_voters) * 100, 2) : 0;
      ?>

      <!-- Top Metric Cards -->
      <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="info-box" style="border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <span class="info-box-icon bg-aqua" style="border-top-left-radius: 12px; border-bottom-left-radius: 12px;"><i class="fa fa-users"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Verified Voters</span>
              <span class="info-box-number" style="font-size: 22px;"><?php echo $total_voters; ?></span>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="info-box" style="border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <span class="info-box-icon bg-green" style="border-top-left-radius: 12px; border-bottom-left-radius: 12px;"><i class="fa fa-check-square-o"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Ballots Casted</span>
              <span class="info-box-number" style="font-size: 22px;"><?php echo $voted_count; ?></span>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="info-box" style="border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <span class="info-box-icon bg-yellow" style="border-top-left-radius: 12px; border-bottom-left-radius: 12px;"><i class="fa fa-hourglass-half"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Remaining Voters</span>
              <span class="info-box-number" style="font-size: 22px;"><?php echo $pending_votes; ?></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Turnout Progress Card -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-solid" style="border-radius: 12px; padding: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <div class="box-header with-border">
              <h3 class="box-title" style="font-weight: 700;"><i class="fa fa-line-chart"></i> Live Voter Turnout Rate</h3>
              <span class="pull-right" style="font-weight: 700; font-size: 16px; color: #00a65a;"><?php echo $turnout_percentage; ?>% Casted</span>
            </div>
            <div class="box-body">
              <div class="progress progress-lg active" style="height: 25px; border-radius: 12px; background: #e2e8f0;">
                <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="<?php echo $turnout_percentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $turnout_percentage; ?>%; line-height: 25px; font-weight: 700;">
                  <?php echo $turnout_percentage; ?>%
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Individual Voter Status List -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-primary" style="border-radius: 12px; border-top: 3px solid #00c0ef;">
            <div class="box-header with-border">
              <h3 class="box-title">Voter Participation Registry (Secret Ballot Protected)</h3>
            </div>
            <div class="box-body table-responsive">
              <table id="example1" class="table table-bordered table-striped table-hover">
                <thead style="background-color: #f8fafc;">
                  <th>Photo</th>
                  <th>Voter ID</th>
                  <th>Name</th>
                  <th>Contact</th>
                  <th>KYC Status</th>
                  <th>Ballot Status</th>
                </thead>
                <tbody>
                  <?php
                    $sql = "SELECT v.*, 
                            (SELECT COUNT(*) FROM votes WHERE votes.voters_id = v.id) as voted_check 
                            FROM voters v 
                            ORDER BY voted_check DESC, v.id DESC";
                    $query = $conn->query($sql);
                    while($row = $query->fetch_assoc()){
                      $image = (!empty($row['photo'])) ? '../images/'.$row['photo'] : '../images/profile.jpg';
                      
                      $kyc_badge = ($row['is_verified'] == 1) 
                        ? "<span class='label label-success'><i class='fa fa-check'></i> Verified</span>" 
                        : "<span class='label label-warning'><i class='fa fa-clock-o'></i> Unverified</span>";

                      $voted_badge = ($row['voted_check'] > 0 || $row['has_voted'] == 1)
                        ? "<span class='label label-success' style='font-size: 11.5px; padding: 4px 8px;'><i class='fa fa-check-circle'></i> Voted</span>"
                        : "<span class='label label-danger' style='font-size: 11.5px; padding: 4px 8px;'><i class='fa fa-times-circle'></i> Not Voted Yet</span>";

                      echo "
                        <tr>
                          <td>
                            <img src='".$image."' width='35px' height='35px' style='border-radius:50%; object-fit:cover;'>
                          </td>
                          <td><b>".htmlspecialchars($row['voters_id'])."</b></td>
                          <td>".htmlspecialchars($row['firstname'].' '.$row['lastname'])."</td>
                          <td>".htmlspecialchars($row['phone'] ?? 'N/A')."</td>
                          <td>".$kyc_badge."</td>
                          <td>".$voted_badge."</td>
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