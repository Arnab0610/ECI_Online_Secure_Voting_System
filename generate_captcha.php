<?php
  session_start();
  header('Content-Type: application/json');

  // কনফিউজিং ক্যারেক্টার বাদ দিয়ে ৫ ডিজিটের আলফানিউমেরিক ক্যাপচা
  $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
  $captcha = '';
  for ($i = 0; $i < 5; $i++) {
      $captcha .= $characters[rand(0, strlen($characters) - 1)];
  }

  // ভেরিফিকেশনের জন্য সেশনে সেভ রাখা
  $_SESSION['reg_captcha'] = $captcha;

  echo json_encode(['captcha' => $captcha]);
?>