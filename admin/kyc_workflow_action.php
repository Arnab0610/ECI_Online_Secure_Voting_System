<?php
  session_start();
  include 'includes/conn.php';
  // admin ফোল্ডার থেকে এক ধাপ পেছনে গিয়ে includes ফোল্ডারের ফাইল পাওয়া যাবে
  include_once '../includes/sms_helper.php';

  if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id = intval($_POST['id']);
    $stage = trim($_POST['stage'] ?? '');
    $action = trim($_POST['action'] ?? '');

    // ভোটারের তথ্য সংগ্রহ
    $app_query = $conn->query("SELECT * FROM voter_applications WHERE id = '$id' LIMIT 1");
    if(!$app_query || $app_query->num_rows === 0){
      $_SESSION['error'] = 'আবেদনটি খুঁজে পাওয়া যায়নি!';
      header('Location: ' . $_SERVER['HTTP_REFERER']);
      exit();
    }
    $app = $app_query->fetch_assoc();
    $mobile = $app['mobile'];
    $ref_no = $app['ref_no'];
    $fname = $app['firstname'];
    $lname = $app['lastname'];

    // ================= BLO স্টেজ =================
    if($stage === 'BLO' && $action === 'approve'){
      $update = $conn->query("UPDATE voter_applications SET status = 'BLO_APPROVED' WHERE id = '$id'");
      
      if($update){
        $blo_msg = "ECI Update: Your Form 6 application (Ref: " . $ref_no . ") has been verified by BLO and forwarded to ERO for final approval.";
        sendLiveSMS($mobile, $blo_msg);

        $_SESSION['success'] = 'BLO ভেরিফিকেশন সফল হয়েছে এবং ভোটারের ফোনে SMS পাঠানো হয়েছে।';
      } else {
        $_SESSION['error'] = 'স্ট্যাটাস আপডেট করতে সমস্যা হয়েছে!';
      }
    }

// ================= ERO স্টেজ (ফাইনাল অ্যাপ্রুভাল) =================
    else if($stage === 'ERO' && $action === 'approve'){
      $epic_no = 'WB/' . rand(10, 99) . '/' . rand(100, 999) . '/' . rand(100000, 999999);
      $default_password = (string)rand(100000, 999999);
      $hashed_password = password_hash($default_password, PASSWORD_DEFAULT);

      $user_photo = !empty($app['photo']) ? $app['photo'] : 'profile.jpg';
      $constituency = $app['constituency'] ?? '';
      $email = $app['email'] ?? ''; // ভোটারের ইমেল ঠিকানা সংগ্রহ

      // voters টেবিলে নতুন ভোটার যুক্ত
      $stmt_voter = $conn->prepare("INSERT INTO voters (voters_id, password, firstname, lastname, photo, phone, constituency, is_verified) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
      $stmt_voter->bind_param("sssssss", $epic_no, $hashed_password, $fname, $lname, $user_photo, $mobile, $constituency);
      $stmt_voter->execute();

      // voter_applications আপডেট
      $update = $conn->query("UPDATE voter_applications SET status = 'ERO_APPROVED', epic_no = '$epic_no' WHERE id = '$id'");

      if($update){
        // ইমেল নোটিফিকেশন পাঠানোর কোড
        include_once '../includes/email_helper.php';
        
        $subject = "Online Voting System - Status Update";
        $html_message = "
          <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #cbd5e1; border-radius: 8px;'>
            <h2 style='color: #0284c7;'>Election Commission Notification</h2>
            <p>Dear $fname $lname,</p>
            <p>Your application has been successfully processed and approved by ERO.</p>
            <hr style='border: none; border-top: 1px solid #e2e8f0;'>
            <p><b>Your Voter ID (EPIC):</b> $epic_no</p>
            <p><b>Your Password:</b> $default_password</p>
            <p>Please use these credentials to log in and cast your vote.</p>
          </div>
        ";

        if(!empty($email)) {
            @sendEmailNotification($email, $subject, $html_message);
        }

        $_SESSION['success'] = 'আবেদন সফলভাবে অ্যাপ্রুভ হয়েছে এবং ভোটারের ইমেইলে EPIC ও পাসওয়ার্ড পাঠানো হয়েছে।';
      } else {
        $_SESSION['error'] = 'ERO অ্যাপ্রুভাল সম্পন্ন করা যায়নি: ' . $conn->error;
      }
    }

    // বাতিল
    else if($action === 'reject'){
      $reject_status = ($stage === 'BLO') ? 'BLO_REJECTED' : 'ERO_REJECTED';
      $conn->query("UPDATE voter_applications SET status = '$reject_status' WHERE id = '$id'");
      $_SESSION['error'] = 'আবেদনটি বাতিল করা হয়েছে।';
    }

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }
?>