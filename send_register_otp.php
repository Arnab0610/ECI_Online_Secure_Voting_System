<?php
  header("Access-Control-Allow-Origin: *");
  header('Content-Type: application/json; charset=utf-8');

  session_start();
  include 'includes/conn.php';

  if(isset($_POST['mobile'])){
    $mobile = trim($_POST['mobile']);

    if(strlen($mobile) !== 10 || !is_numeric($mobile)){
      echo json_encode(['status' => 'error', 'message' => 'সঠিক ১০ সংখ্যার মোবাইল নম্বর দিন।']);
      exit();
    }

    $otp = (string)rand(100000, 999999);
    $_SESSION['reg_otp'] = $otp;
    $_SESSION['reg_mobile'] = $mobile;
    $_SESSION['reg_otp_expire'] = time() + 60;

    echo json_encode([
      'status' => 'success',
      'message' => 'আপনার মোবাইলে ৬ সংখ্যার ওটিপি পাঠানো হয়েছে।',
      'demo_otp' => $otp,
      'expire_in' => 60
    ]);
    exit();
  } else {
    echo json_encode(['status' => 'error', 'message' => 'মোবাইল নম্বর পাওয়া যায়নি।']);
    exit();
  }
?>