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
        Emergency Freeze / Kill Switch <small>জরুরী নির্বাচন নিয়ন্ত্রণ কেন্দ্র</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Kill Switch</li>
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
        <div class="col-md-8 col-md-offset-2">
          <div class="box box-danger" style="border-radius: 16px; border-top: 3px solid #dd4b39; padding: 15px;">
            <div class="box-header with-border text-center">
              <h3 class="box-title" style="font-size: 22px; font-weight: 700; color: #b91c1c;">
                <i class="fa fa-power-off"></i> Central Election Freeze Command
              </h3>
              <p class="text-muted" style="margin-top: 8px; font-size: 13.5px;">
                যেকোনো সাইবার আক্রমণ, ডেটাবেস থ্রেট বা অননুমোদিত অ্যাক্সেসের ক্ষেত্রে এক ক্লিকে লাইভ নির্বাচন সাময়িকভাবে বন্ধ বা পুনরায় চালু করুন।
              </p>
            </div>
            
            <div class="box-body" style="padding: 25px 15px;">
              <?php
                // লাইভ বা শিডিউলড নির্বাচন চেক করা
                $sql = "SELECT * FROM elections ORDER BY id DESC LIMIT 1";
                $query = $conn->query($sql);
                
                if($query->num_rows > 0){
                  $election = $query->fetch_assoc();
                  $is_frozen = ($election['status'] == 'paused');
              ?>
                <div class="well text-center" style="background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
                  <h4 style="margin-top: 0; font-weight: 700;"><?php echo htmlspecialchars($election['title']); ?></h4>
                  <p style="margin-bottom: 15px;">
                    Current Status: 
                    <?php 
                      if($is_frozen){
                        echo "<span class='label label-danger' style='font-size: 13px; padding: 4px 12px;'><i class='fa fa-snowflake-o'></i> SYSTEM FROZEN / PAUSED</span>";
                      } elseif($election['status'] == 'live') {
                        echo "<span class='label label-success' style='font-size: 13px; padding: 4px 12px;'><i class='fa fa-circle'></i> LIVE & ACCEPTING VOTES</span>";
                      } else {
                        echo "<span class='label label-info' style='font-size: 13px; padding: 4px 12px;'>".strtoupper($election['status'])."</span>";
                      }
                    ?>
                  </p>

                  <div style="margin-top: 25px;">
                    <?php if(!$is_frozen): ?>
                      <a href="kill_switch_action.php?action=freeze&id=<?php echo $election['id']; ?>" 
                         class="btn btn-danger btn-lg" 
                         style="border-radius: 10px; padding: 12px 30px; font-weight: 700; box-shadow: 0 4px 15px rgba(221, 75, 57, 0.4);"
                         onclick="return confirm('WARNING: আপনি কি নিশ্চিত যে লাইভ নির্বাচনটি সাময়িকভাবে বন্ধ (Freeze) করতে চান?');">
                        <i class="fa fa-lock"></i> FREEZE ALL VOTING NOW
                      </a>
                    <?php else: ?>
                      <a href="kill_switch_action.php?action=resume&id=<?php echo $election['id']; ?>" 
                         class="btn btn-success btn-lg" 
                         style="border-radius: 10px; padding: 12px 30px; font-weight: 700; box-shadow: 0 4px 15px rgba(0, 166, 90, 0.4);"
                         onclick="return confirm('আপনি কি নির্বাচন পুনরায় লাইভ (Resume) করতে চান?');">
                        <i class="fa fa-unlock"></i> RESUME VOTING
                      </a>
                    <?php endif; ?>
                  </div>
                </div>
              <?php 
                } else {
                  echo "<div class='alert alert-warning text-center'>কোনো নির্বাচন শিডিউল পাওয়া যায়নি। আগে একটি নির্বাচন শিডিউল তৈরি করুন।</div>";
                }
              ?>
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