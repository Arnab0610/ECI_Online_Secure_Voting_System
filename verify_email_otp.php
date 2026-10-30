<?php
  header("Access-Control-Allow-Origin: *");
  header('Content-Type: application/json; charset=utf-8');

  session_start();

  if(isset($_POST['otp'])){
    $user_otp = trim($_POST['otp']);

    if(!isset($_SESSION['reg_email_otp'])){
      echo json_encode(['status' => 'error', 'message' => 'প্রথমে ওটিপি অনুরোধ করুন।']);
      exit();
    }

    if(time() > $_SESSION['reg_email_otp_expire']){
      echo json_encode(['status' => 'expired', 'message' => 'ওটিপির মেয়াদ শেষ হয়ে গেছে! আবার পাঠান।']);
      exit();
    }

    if($user_otp === $_SESSION['reg_email_otp']){
      $_SESSION['email_verified'] = true;
      echo json_encode(['status' => 'success', 'message' => 'ইমেল সফলভাবে ভেরিফাই হয়েছে!']);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'ভুল ওটিপি! সঠিক ওটিপি দিন।']);
    }
    exit();
  }
?>