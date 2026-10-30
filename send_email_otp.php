<?php
  header("Access-Control-Allow-Origin: *");
  header('Content-Type: application/json; charset=utf-8');

  session_start();
  include 'includes/conn.php';
  include_once 'includes/email_helper.php';

  if(isset($_POST['email'])){
    $email = trim($_POST['email']);

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
      echo json_encode(['status' => 'error', 'message' => 'সঠিক ইমেল ঠিকানা দিন।']);
      exit();
    }

    $otp = (string)rand(100000, 999999);
    $_SESSION['reg_email_otp'] = $otp;
    $_SESSION['reg_email'] = $email;
    $_SESSION['reg_email_otp_expire'] = time() + 60;

    $subject = "Email Verification OTP - Online Voting System";
    $message = "
      <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #cbd5e1; border-radius: 8px;'>
        <h3 style='color: #0284c7;'>Election Commission of India</h3>
        <p>আপনার রেজিস্ট্রেশন ইমেল ভেরিফিকেশনের জন্য ওটিপি কোড নিচে দেওয়া হলো:</p>
        <h2 style='color: #0b63c5; letter-spacing: 4px;'>$otp</h2>
        <p>এই ওটিপি কোডটি ১ মিনিটের জন্য বৈধ।</p>
      </div>
    ";

    $mail_sent = sendEmailNotification($email, $subject, $message);

    if($mail_sent){
      echo json_encode([
        'status' => 'success',
        'message' => 'আপনার ইমেইলে ৬ ডিজিটের ওটিপি পাঠানো হয়েছে।',
        'demo_otp' => $otp,
        'expire_in' => 60
      ]);
    } else {
      echo json_encode([
        'status' => 'error',
        'message' => 'ইমেল পাঠানো সম্ভব হয়নি। আবার চেষ্টা করুন।'
      ]);
    }
    exit();
  } else {
    echo json_encode(['status' => 'error', 'message' => 'ইমেল ঠিকানা পাওয়া যায়নি।']);
    exit();
  }
?>