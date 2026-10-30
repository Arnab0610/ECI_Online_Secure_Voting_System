<?php
  session_start();
  include 'includes/conn.php';
  header('Content-Type: application/json');

  if(isset($_POST['otp']) && isset($_POST['voter_id'])){
    $entered_otp = trim($_POST['otp']);
    $voter_id = trim($_POST['voter_id']);

    if(isset($_SESSION['auth_otp']) && $_SESSION['auth_otp'] === $entered_otp){
      $_SESSION['otp_verified'] = true;
      echo json_encode([
        'status' => 'success', 
        'message' => 'ওটিপি সফলভাবে যাচাই হয়েছে!'
      ]);
    } else {
      echo json_encode([
        'status' => 'error', 
        'message' => 'ভুল ওটিপি কোড!'
      ]);
    }
  } else {
    echo json_encode(['status' => 'error', 'message' => 'তথ্য অসম্পূর্ণ।']);
  }
?>