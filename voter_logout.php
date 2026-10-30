<?php
  session_start();
  // সমস্ত ভোটার সেশন ডাটা সম্পূর্ণ মুছে ফেলা
  $_SESSION = array();
  if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
      $params["path"], $params["domain"],
      $params["secure"], $params["httponly"]
    );
  }
  session_destroy();

  // সরাসরি লগইন পেজে পাঠিয়ে দেওয়া
  header('location: voter_secure_login.php?session_expired=1');
  exit();
?>