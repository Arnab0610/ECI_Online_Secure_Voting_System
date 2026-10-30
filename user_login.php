<?php
  	session_start();

    // ব্রাউজার ক্যাশ বন্ধ রাখা
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    // শুধুমাত্র যদি ভোটার লগইন থাকে, তখনই ভোটার হোমে পাঠাবে
    if(isset($_SESSION['voter']) && trim($_SESSION['voter']) != ''){
      header('location: home.php');
      exit();
    }
?>
<?php include 'includes/header.php'; ?>

<style>
  html, body {
    height: 100vh !important;
    margin: 0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
  }
  .custom-login-container {
    margin: 0 !important;
  }
</style>
<body class="hold-transition login-page" style="background: url('images/vote-bg.png') no-repeat center center fixed !important; background-size: cover !important; min-height: 100vh !important; height: 100vh !important; display: flex !important; align-items: center !important; justify-content: center !important; margin: 0 !important; padding: 20px !important;">

<div class="custom-login-container" style="display: flex; flex-wrap: wrap; width: 100%; max-width: 820px; border-radius: 16px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.35); border: 1px solid rgba(255, 255, 255, 0.4); margin: auto;">
  
  	<!-- Left Side: Login Form -->
  	<div class="login-left" style="flex: 1 1 380px; background: #ffffff; padding: 35px 30px; display: flex; flex-direction: column; justify-content: center;">
    	<div style="text-align: center; margin-bottom: 25px;">
      		<div style="width: 50px; height: 50px; background: #e8f0fe; color: #1a73e8; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 10px;">
        		<i class="fa fa-id-card-o"></i>
      		</div>
      		<h4 style="margin: 0; font-weight: 700; color: #333333; font-size: 20px;">Voter Login</h4>
      		<p style="margin-top: 5px; color: #777; font-size: 13px;">Sign in to start your session</p>
    	</div>

    	<form action="login.php" method="POST">
      		<div class="form-group has-feedback" style="margin-bottom: 18px;">
        		<label style="font-size: 13px; color: #444; font-weight: 600;">Voter's ID <span style="color: red;">*</span></label>
        		<input type="text" class="form-control" name="voter" placeholder="Enter Voter ID" required style="border-radius: 6px; height: 42px; border: 1px solid #ced4da; box-shadow: none;">
        		<span class="glyphicon glyphicon-user form-control-feedback" style="height: 42px; line-height: 42px; color: #888;"></span>
      		</div>

      		<div class="form-group has-feedback" style="margin-bottom: 25px;">
        		<label style="font-size: 13px; color: #444; font-weight: 600;">Password <span style="color: red;">*</span></label>
        		<input type="password" class="form-control" name="password" placeholder="Enter Password" required style="border-radius: 6px; height: 42px; border: 1px solid #ced4da; box-shadow: none;">
        		<span class="glyphicon glyphicon-lock form-control-feedback" style="height: 42px; line-height: 42px; color: #888;"></span>
      		</div>

      		<div>
        		<button type="submit" class="btn btn-block" name="login" style="background: #1a73e8; color: #ffffff; font-weight: 600; height: 42px; border-radius: 6px; font-size: 15px; border: none; transition: 0.3s; box-shadow: 0 4px 10px rgba(26, 115, 232, 0.3);">
          			Sign In &nbsp;<i class="fa fa-arrow-right"></i>
        		</button>
      		</div>

			<div style="text-align:center; margin-top:14px; font-size:13px;">
				নতুন ভোটার? <a href="register.php" style="color:#1a73e8; font-weight:700;">এখানে নিবন্ধন করুন</a>
			</div>

			<!-- Track Application Status Section -->
			<div style="margin-top: 20px; padding-top: 15px; border-top: 1px dashed #cbd5e1; text-align: center;">
			<p style="font-size: 12.5px; color: #64748b; margin-bottom: 8px;">নতুন ফর্ম ৬ আবেদন জমা দিয়েছেন?</p>
			<a href="track_status.php" class="btn-track-status" style="display: inline-flex; align-items: center; gap: 8px; background: #f0f9ff; color: #0284c7; border: 1.5px solid #bae6fd; padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none; transition: 0.2s;">
				<i class="fa fa-search"></i> আবেদনের স্থিতি ট্র্যাক করুন (Track Status)
			</a>
			</div>
    	</form>

    	<?php
      		if(isset($_SESSION['error'])){
        		echo "
        			<div class='callout callout-danger text-center' style='margin-top: 15px; padding: 8px; border-radius: 4px; font-size: 13px;'>
          				<p style='margin: 0;'>".$_SESSION['error']."</p> 
        			</div>
        		";
        		unset($_SESSION['error']);
      		}
    	?>
  	</div>

  	<!-- Right Side: Frosted Glass Panel -->
  	<div class="login-right" style="flex: 1 1 340px; background: rgba(255, 255, 255, 0.18); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px 25px; text-align: center; border-left: 1px solid rgba(255, 255, 255, 0.3);">
    	<h2 style="color: #2c3e50; font-weight: 800; margin: 0 0 8px 0; font-size: 28px; text-shadow: 0 1px 2px rgba(255,255,255,0.6);">
      		অনলাইন ভোটিং সিস্টেম
    	</h2>
    	<h4 style="color: #34495e; font-weight: 600; margin: 0; font-size: 16px; letter-spacing: 0.5px;">
      		Online Voting System
    	</h4>
    	<div style="width: 50px; height: 3px; background: #1a73e8; margin: 15px 0; border-radius: 2px;"></div>
    	<p style="color: #2c3e50; font-size: 13px; font-weight: 500; line-height: 1.5; margin: 0;">
      		গণতন্ত্র রক্ষা করুন, সুরক্ষিত থাকুন এবং ঘরে বসে আপনার ভোটাধিকার প্রয়োগ করুন।
    	</p>
  	</div>

</div>

<?php include 'includes/scripts.php' ?>
</body>
</html>