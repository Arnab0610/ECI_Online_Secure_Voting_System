<?php
  session_start();
  header('Content-Type: application/json');

  if(isset($_POST['otp'])){
    $user_otp = trim($_POST['otp']);

    if(!isset($_SESSION['reg_otp']) || !isset($_SESSION['reg_otp_expire'])){
      echo json_encode(['status' => 'error', 'message' => 'ওটিপি সেশন পাওয়া যায়নি! নতুন ওটিপি পাঠান।']);
      exit();
    }

    // ১ মিনিটের মেয়াদ শেষ হয়েছে কিনা চেক
    if(time() > $_SESSION['reg_otp_expire']){
      unset($_SESSION['reg_otp']);
      unset($_SESSION['reg_otp_expire']);
      echo json_encode(['status' => 'expired', 'message' => '⚠️ ওটিপির ১ মিনিটের মেয়াদ শেষ হয়ে গেছে! অনুগ্রহ করে পুনরায় ওটিপি পাঠান।']);
      exit();
    }

    // ওটিপি মিলছে কিনা চেক
    if($user_otp === $_SESSION['reg_otp']){
      $_SESSION['reg_mobile_verified'] = true;
      echo json_encode(['status' => 'success', 'message' => '✓ মোবাইল নম্বর সফলভাবে ভেরিফাই হয়েছে!']);
    } else {
      echo json_encode(['status' => 'error', 'message' => '❌ ভুল ওটিপি কোড! আবার চেষ্টা করুন।']);
    }
  } else {
    echo json_encode(['status' => 'error', 'message' => 'ওটিপি পাওয়া যায়নি।']);
  }
?>