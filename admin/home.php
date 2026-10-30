<?php include 'includes/session.php'; ?>
<?php include 'includes/slugify.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        ECI কেন্দ্রীয় কন্ট্রোল ড্যাশবোর্ড
        <small>Real-time Election Intelligence Hub</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> হোম</a></li>
        <li class="active">ড্যাশবোর্ড</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <?php
        if(isset($_SESSION['error'])){
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Error!</h4>
              ".$_SESSION['error']."
            </div>
          ";
          unset($_SESSION['error']);
        }
        if(isset($_SESSION['success'])){
          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              ".$_SESSION['success']."
            </div>
          ";
          unset($_SESSION['success']);
        }

        // লাইভ পরিসংখ্যান সংগ্রহ
        $total_voters = $conn->query("SELECT id FROM voters")->num_rows;
        $total_candidates = $conn->query("SELECT id FROM candidates")->num_rows;
        
        // এনক্রিপ্টেড ব্যালট খতিয়ান থেকে গৃহীত মোট ভোট
        $total_encrypted_votes = $conn->query("SELECT id FROM encrypted_ballots")->num_rows;
        
        // টার্নআউট শতকরা হার
        $turnout_pct = ($total_voters > 0) ? round(($total_encrypted_votes / $total_voters) * 100, 2) : 0;
        
        // পেন্ডিং ফর্ম ৬ আবেদন
        $pending_apps = $conn->query("SELECT id FROM voter_applications WHERE status = 'PENDING_BLO'")->num_rows;
      ?>

      <!-- Info Boxes -->
      <div class="row">
        <!-- ১. মোট ভোটার -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-users"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">মোট নিবন্ধিত ভোটার</span>
              <span class="info-box-number" style="font-size:24px;"><?php echo $total_voters; ?></span>
            </div>
          </div>
        </div>

        <!-- ২. মোট এনক্রিপ্টেড ভোট -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-shield"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">এনক্রিপ্টেড ব্যালট ভোট</span>
              <span class="info-box-number" style="font-size:24px;"><?php echo $total_encrypted_votes; ?></span>
            </div>
          </div>
        </div>

        <!-- ৩. ভোটার টার্নআউট (%) -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="fa fa-pie-chart"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">ভোটদানের হার (Turnout)</span>
              <span class="info-box-number" style="font-size:24px;"><?php echo $turnout_pct; ?>%</span>
            </div>
          </div>
        </div>

        <!-- ৪. পেন্ডিং আবেদন -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-file-text-o"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">পেন্ডিং ফর্ম ৬ আবেদন</span>
              <span class="info-box-number" style="font-size:24px;"><?php echo $pending_apps; ?></span>
            </div>
          </div>
        </div>
      </div>

      <!-- অ্যাকশন লিংকস -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-solid" style="background:#f8fafc; border-left:4px solid #0b63c5; padding:15px; border-radius:8px;">
            <h4 style="margin-top:0; font-weight:800; color:#0f172a;"><i class="fa fa-info-circle text-primary"></i> ডাইনামিক নির্বাচনী কমান্ড সেন্টার সক্রিয় রয়েছে</h4>
            <p style="margin:0; color:#475569; font-size:13.5px;">
              নিবন্ধিত ভোটাররা শুধুমাত্র তাদের নির্ধারিত বিধানসভা এলাকাতেই ভোট দিতে পারবেন। ফলাফল গণনা করতে বামদিকের মেনু থেকে <b>Results & Vault &rarr; ভোট গণনা ও ফলাফল</b> অপশনে যান।[cite: 3]
            </p>
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