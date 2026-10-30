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
        Cryptographic Vault & Key Activation <small>ক্রিপ্টোগ্রাফিক ব্যালট ভোল্ট ডিক্রিপশন</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Decrypt Vault</li>
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
          <div class="box box-warning" style="border-radius: 16px; border-top: 3px solid #f39c12; padding: 15px;">
            <div class="box-header with-border text-center">
              <div style="font-size: 45px; color: #f39c12; margin-bottom: 10px;">
                <i class="fa fa-key"></i>
              </div>
              <h3 class="box-title" style="font-size: 22px; font-weight: 700; color: #1e293b;">
                Ballot Vault Decryption Desk
              </h3>
              <p class="text-muted" style="margin-top: 8px; font-size: 13.5px;">
                নির্বাচনী প্রক্রিয়া শেষ হওয়ার পর অফিসিয়াল মাস্টার সিকিউরিটি কী (Master Key) প্রদান করে এনক্রিপ্টেড ফলাফল ভোল্ট আনলক করুন।
              </p>
            </div>
            
            <div class="box-body" style="padding: 25px 20px;">
              <form action="decrypt_action.php" method="POST">
                <div class="form-group" style="margin-bottom: 20px;">
                  <label style="font-size: 14px; font-weight: 600; color: #334155;">Select Active Election</label>
                  <select class="form-control" name="election_id" required style="height: 44px; border-radius: 8px;">
                    <option value="" disabled selected>-- নির্বাচন নির্বাচন করুন --</option>
                    <?php
                      $e_query = $conn->query("SELECT * FROM elections ORDER BY id DESC");
                      while($e_row = $e_query->fetch_assoc()){
                        echo "<option value='".$e_row['id']."'>".htmlspecialchars($e_row['title'])." [".strtoupper($e_row['status'])."]</option>";
                      }
                    ?>
                  </select>
                </div>

                <div class="form-group" style="margin-bottom: 25px;">
                  <label style="font-size: 14px; font-weight: 600; color: #334155;">Master Decryption Key (AES-256)</label>
                  <input type="password" class="form-control" name="master_key" placeholder="অফিসিয়াল ক্রিপ্টোগ্রাফিক মাস্টার কী দিন..." required style="height: 44px; border-radius: 8px;">
                  <small class="text-muted"><i class="fa fa-info-circle"></i> ডিফল্ট মাস্টার কী: <code>ELECTION@2026#SECURE</code></small>
                </div>

                <button type="submit" name="decrypt_vault" class="btn btn-warning btn-block btn-lg" style="border-radius: 10px; font-weight: 700; height: 48px; box-shadow: 0 4px 15px rgba(243, 156, 18, 0.4);">
                  <i class="fa fa-unlock-alt"></i> AUTHORIZE & UNLOCK BALLOT VAULT
                </button>
              </form>
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