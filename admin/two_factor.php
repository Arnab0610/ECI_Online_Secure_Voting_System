<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Two-Factor Authentication (2FA) <small>দ্বি-স্তরীয় নিরাপত্তা সেটআপ</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">2FA Security</li>
      </ol>
    </section>

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
          <div class="box box-success" style="border-radius: 16px; border-top: 3px solid #00a65a; padding: 15px;">
            <div class="box-header with-border text-center">
              <div style="font-size: 45px; color: #00a65a; margin-bottom: 10px;">
                <i class="fa fa-mobile"></i>
              </div>
              <h3 class="box-title" style="font-size: 22px; font-weight: 700;">
                Two-Factor Security Layer
              </h3>
              <p class="text-muted" style="margin-top: 8px; font-size: 13.5px;">
                পাসওয়ার্ডের পাশাপাশি অতিরিক্ত OTP বা সিকিউরিটি টোকেন যাচাইকরণ সক্রিয় করুন।
              </p>
            </div>

            <div class="box-body" style="padding: 20px 25px;">
              <div class="well text-center" style="background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
                <h4 style="font-weight: 700; margin-top: 0;">2FA Status for <code><?php echo htmlspecialchars($user['username']); ?></code></h4>
                <p>
                  Current Protection: 
                  <?php
                    if(!empty($user['two_factor_secret'])){
                      echo "<span class='label label-success' style='font-size: 13px; padding: 4px 12px;'><i class='fa fa-shield'></i> 2FA / OTP ENABLED</span>";
                    } else {
                      echo "<span class='label label-default' style='font-size: 13px; padding: 4px 12px;'>DISABLED</span>";
                    }
                  ?>
                </p>

                <div style="margin-top: 25px;">
                  <?php if(empty($user['two_factor_secret'])): ?>
                    <a href="two_factor_action.php?action=enable" class="btn btn-success btn-lg" style="border-radius: 10px; font-weight: 700; box-shadow: 0 4px 15px rgba(0, 166, 90, 0.35);">
                      <i class="fa fa-key"></i> ENABLE TWO-FACTOR (OTP)
                    </a>
                  <?php else: ?>
                    <a href="two_factor_action.php?action=disable" class="btn btn-danger btn-lg" style="border-radius: 10px; font-weight: 700; box-shadow: 0 4px 15px rgba(221, 75, 57, 0.35);" onclick="return confirm('আপনি কি টু-ফ্যাক্টর নিরাপত্তা বন্ধ করতে চান?');">
                      <i class="fa fa-ban"></i> DISABLE 2FA
                    </a>
                  <?php endif; ?>
                </div>
              </div>
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