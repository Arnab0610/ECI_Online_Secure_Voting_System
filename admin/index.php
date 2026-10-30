<?php
  	session_start();
  	if(isset($_SESSION['admin'])){
    	header('location:home.php');
  	}
?>
<?php include 'includes/header.php'; ?>

<style>
    /* পেজ সেন্টার ও ব্যাকগ্রাউন্ড সেটআপ */
    html, body.admin-login-page {
        height: 100vh !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: hidden !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: url('../images/vote-bg2.png') no-repeat center center fixed !important;
        background-size: cover !important;
    }

    .admin-card-container {
        display: flex;
        flex-wrap: wrap;
        width: 100%;
        max-width: 860px;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.35);
        margin: 0 !important;
    }

    /* Left Side: Transparent Glass Panel */
    .admin-left-panel {
        flex: 1 1 380px;
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 30px;
        border-right: 1px solid rgba(255, 255, 255, 0.3);
        position: relative;
    }

    .home-btn {
        width: 42px;
        height: 42px;
        background: #005a36;
        color: #ffffff;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        text-decoration: none;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        transition: 0.3s;
    }
    .home-btn:hover {
        background: #008d4c;
        color: #fff;
    }

    /* Right Side: Solid White Form */
    .admin-right-form {
        flex: 1 1 400px;
        background: #ffffff;
        padding: 40px 35px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
</style>

<body class="hold-transition admin-login-page">

<div class="admin-card-container">
  
  	<!-- Left Panel: Branding & Details -->
  	<div class="admin-left-panel">
    	<div>
      		<a href="../index.php" class="home-btn" title="Back to Voter Login">
        		<i class="fa fa-home"></i>
      		</a>
    	</div>

    	<div style="margin: 40px 0; text-align: center;">
      		<h2 style="color: #005a36; font-weight: 800; margin: 0 0 10px 0; font-size: 26px; text-shadow: 0 1px 2px rgba(255,255,255,0.6);">
        		প্রশাসনিক নিয়ন্ত্রণ কক্ষ
      		</h2>
      		<h4 style="color: #2c3e50; font-weight: 700; margin: 0; font-size: 18px; letter-spacing: 0.5px;">
        		Admin Control Portal
      		</h4>
      		<div style="width: 50px; height: 3px; background: #e65100; margin: 15px auto; border-radius: 2px;"></div>
      		<p style="color: #333333; font-size: 13px; font-weight: 500; line-height: 1.6; margin: 0;">
        		অনলাইন ভোটিং সিস্টেমের সুরক্ষিত ও দায়িত্বশীল প্রশাসনিক কার্যক্রম পরিচালনা করার জন্য লগইন করুন।
      	</div>

    	<div style="text-align: center;">
      		<span style="font-size: 11px; color: #444; font-weight: 600; background: rgba(255,255,255,0.5); padding: 5px 12px; border-radius: 20px;">
        		<i class="fa fa-shield"></i> Authorized Access Only
      		</span>
    	</div>
  	</div>

  	<!-- Right Panel: Login Form -->
  	<div class="admin-right-form">
    	<div style="text-align: center; margin-bottom: 25px;">
      		<h3 style="margin: 0; font-weight: 700; color: #4a148c; font-size: 22px;">
        		Online Voting System
      		</h3>
      		<p style="margin-top: 6px; color: #666; font-size: 14px; font-weight: 600;">
        		Administrator Login
      		</p>
    	</div>

    	<form action="login.php" method="POST">
      		<div class="form-group has-feedback" style="margin-bottom: 20px;">
        		<label style="font-size: 13px; color: #444; font-weight: 600;">Username <span style="color: red;">*</span></label>
        		<input type="text" class="form-control" name="username" placeholder="Enter Admin Username" required style="border-radius: 8px; height: 44px; border: 1px solid #ced4da; box-shadow: none; font-size: 14px;">
        		<span class="glyphicon glyphicon-user form-control-feedback" style="height: 44px; line-height: 44px; color: #888;"></span>
      		</div>

      		<div class="form-group has-feedback" style="margin-bottom: 25px;">
        		<label style="font-size: 13px; color: #444; font-weight: 600;">Password <span style="color: red;">*</span></label>
        		<input type="password" class="form-control" name="password" placeholder="Enter Admin Password" required style="border-radius: 8px; height: 44px; border: 1px solid #ced4da; box-shadow: none; font-size: 14px;">
        		<span class="glyphicon glyphicon-lock form-control-feedback" style="height: 44px; line-height: 44px; color: #888;"></span>
      		</div>

      		<div>
        		<button type="submit" class="btn btn-block" name="login" style="background: #e65100; color: #ffffff; font-weight: 700; height: 44px; border-radius: 8px; font-size: 15px; border: none; transition: 0.3s; box-shadow: 0 4px 12px rgba(230, 81, 0, 0.35);">
          			Sign In &nbsp;<i class="fa fa-sign-in"></i>
        		</button>
      		</div>
    	</form>

    	<?php
      		if(isset($_SESSION['error'])){
        		echo "
        			<div class='callout callout-danger text-center' style='margin-top: 20px; padding: 8px; border-radius: 6px; font-size: 13px;'>
          				<p style='margin: 0;'>".$_SESSION['error']."</p> 
        			</div>
        		";
        		unset($_SESSION['error']);
      		}
    	?>
  	</div>

</div>

<?php include 'includes/scripts.php' ?>
</body>
</html>