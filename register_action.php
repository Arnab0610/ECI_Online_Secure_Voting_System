<?php
  session_start();
  include 'includes/conn.php';
  include 'includes/sms_helper.php'; // SMS হেল্পার ফাইল
  include_once 'includes/email_helper.php'; // ইমেল হেল্পার ফাইল

  if(isset($_POST['register'])){

    // ================= CAPTCHA VERIFICATION =================
    $user_captcha = strtoupper(trim($_POST['captcha'] ?? ''));
    $session_captcha = isset($_SESSION['reg_captcha']) ? $_SESSION['reg_captcha'] : '';

    if (empty($user_captcha) || $user_captcha !== $session_captcha) {
        echo "<script>
                alert('❌ ভুল সিকিউরিটি ক্যাপচা কোড! পুনরায় চেষ্টা করুন।');
                window.history.back();
              </script>";
        exit();
    }
    
    $mobile = trim($_POST['mobile']);
    $email = trim($_POST['email'] ?? ''); // ভোটারের ইমেল ঠিকানা
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $relative_name = trim($_POST['relative_name'] ?? '');
    $has_aadhaar = $_POST['has_aadhaar'] ?? 'no';
    $aadhaar_number = ($has_aadhaar === 'yes') ? trim($_POST['aadhaar_number'] ?? '') : NULL;

    // ================= DUPLICATE CHECK LOGIC =================
    if(!empty($aadhaar_number)){
      $check_query = $conn->prepare("SELECT ref_no, status, created_at FROM voter_applications WHERE (mobile = ? OR aadhaar_number = ?) AND status NOT IN ('BLO_REJECTED', 'ERO_REJECTED') LIMIT 1");
      $check_query->bind_param("ss", $mobile, $aadhaar_number);
    } else {
      $check_query = $conn->prepare("SELECT ref_no, status, created_at FROM voter_applications WHERE mobile = ? AND status NOT IN ('BLO_REJECTED', 'ERO_REJECTED') LIMIT 1");
      $check_query->bind_param("s", $mobile);
    }

    $check_query->execute();
    $existing = $check_query->get_result()->fetch_assoc();

    if($existing){
      ?>
      <!DOCTYPE html>
      <html lang="bn">
      <head>
        <meta charset="utf-8">
        <title>Application Already Exists • Form 6</title>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&family=Share+Tech+Mono&display=swap" rel="stylesheet">
        <style>
          body { background: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 20px; }
          .error-box { background: #ffffff; max-width: 620px; width: 100%; border-radius: 16px; box-shadow: 0 20px 45px rgba(0,0,0,0.08); padding: 35px; text-align: center; border: 1.5px solid #fecaca; }
          .error-icon { width: 60px; height: 60px; background: #fee2e2; color: #ef4444; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 15px; }
          .details-card { background: #fff1f2; border: 1px solid #fecdd3; border-radius: 10px; padding: 18px 24px; text-align: left; margin: 20px 0; font-size: 13.5px; line-height: 2; color: #9f1239; }
          .btn-action { background: #0b63c5; color: white; padding: 11px 22px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 13.5px; display: inline-block; margin: 0 6px; border: none; cursor: pointer; }
        </style>
      </head>
      <body>
        <div class="error-box">
          <div class="error-icon">⚠️</div>
          <h2 style="margin:0; color:#991b1b; font-weight:800; font-size:20px;">আবেদনটি ইতিমধ্যে নিবন্ধিত রয়েছে!</h2>
          <p style="color:#64748b; font-size:13px; margin:6px 0 0;">এই মোবাইল নম্বর বা তথ্যের বিপরীতে পূর্বে একটি আবেদন জমা রয়েছে।</p>
          <div class="details-card">
            <div>• <b>বিদ্যমান রেফারেন্স নম্বর:</b> <span style="font-family:'Share Tech Mono', monospace; font-weight:800; color:#0b63c5;"><?php echo htmlspecialchars($existing['ref_no']); ?></span></div>
            <div>• <b>স্ট্যাটাস:</b> <span style="font-weight:700; color:#d97706;"><?php echo htmlspecialchars($existing['status']); ?></span></div>
          </div>
          <a href="register.php" class="btn-action" style="background:#64748b;">⬅ ফর্মে ফিরে যান</a>
        </div>
      </body>
      </html>
      <?php
      exit();
    }

    // ================= NEW INSERT LOGIC =================
    $ref_no = 'VTR-WB-' . date('Y') . '-' . rand(100000, 999999);
    $state = $_POST['state'] ?? '';
    $district = $_POST['district'] ?? '';
    $constituency = $_POST['constituency'] ?? '';
    $firstname_bn = trim($_POST['firstname_bn'] ?? '');
    $lastname_bn = trim($_POST['lastname_bn'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $relative_type = $_POST['relative_type'] ?? '';
    $relative_name_bn = trim($_POST['relative_name_bn'] ?? '');
    $house_no = trim($_POST['house_no'] ?? '');
    $street = trim($_POST['street'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $post_office = trim($_POST['post_office'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');
    $tehsil = trim($_POST['tehsil'] ?? '');
    $dob_doc_type = $_POST['dob_doc_type'] ?? '';
    $address_doc_type = $_POST['address_doc_type'] ?? '';
    $face_descriptor = trim($_POST['face_descriptor'] ?? '');
    $declaration_date = trim($_POST['declaration_date'] ?? date('d-m-Y'));

    // ফাইল আপলোড
    $photo_filename = '';
    if(!empty($_FILES['photo']['name'])){
      $photo_filename = 'photo_' . time() . '_' . rand(100, 999) . '.' . pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
      move_uploaded_file($_FILES['photo']['tmp_name'], 'images/' . $photo_filename);
    }

    $dob_filename = '';
    if(!empty($_FILES['dob_doc_file']['name'])){
      $dob_filename = 'dob_' . time() . '_' . rand(100, 999) . '.' . pathinfo($_FILES['dob_doc_file']['name'], PATHINFO_EXTENSION);
      move_uploaded_file($_FILES['dob_doc_file']['tmp_name'], 'images/' . $dob_filename);
    }

    $address_filename = '';
    if(!empty($_FILES['nid_photo']['name'])){
      $address_filename = 'addr_' . time() . '_' . rand(100, 999) . '.' . pathinfo($_FILES['nid_photo']['name'], PATHINFO_EXTENSION);
      move_uploaded_file($_FILES['nid_photo']['tmp_name'], 'images/' . $address_filename);
    }

    // voter_applications টেবিলে email সহ ইনসার্ট কুয়েরি
    $stmt = $conn->prepare("INSERT INTO voter_applications 
      (ref_no, state, district, constituency, firstname, firstname_bn, lastname, lastname_bn, gender, email, relative_type, relative_name, relative_name_bn, mobile, has_aadhaar, aadhaar_number, house_no, street, city, post_office, pincode, tehsil, photo, dob_doc_type, dob_doc_file, address_doc_type, address_doc_file, face_descriptor, status) 
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'PENDING_BLO')");
    
    $stmt->bind_param("ssssssssssssssssssssssssssss", 
      $ref_no, $state, $district, $constituency, $firstname, $firstname_bn, $lastname, $lastname_bn, $gender, $email, 
      $relative_type, $relative_name, $relative_name_bn, $mobile, $has_aadhaar, $aadhaar_number, $house_no, 
      $street, $city, $post_office, $pincode, $tehsil, $photo_filename, $dob_doc_type, $dob_filename, 
      $address_doc_type, $address_filename, $face_descriptor
    );

    if($stmt->execute()){
      unset($_SESSION['reg_captcha']);

      // ১. সফল SMS পাঠানো
      $reg_msg = "Application Submitted Successfully! Your Form 6 Reference ID is: " . $ref_no . ". Sent for BLO field verification. - ECI";
      @sendLiveSMS($mobile, $reg_msg);

      // ২. সফল ইমেল এবং অ্যাকনলেজমেন্ট নম্বর পাঠানো
      if(!empty($email)) {
          $email_subject = "Form 6 Application Submitted Successfully - Online Voting System";
          $email_body = "
            <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #cbd5e1; border-radius: 8px;'>
              <h2 style='color: #0284c7;'>Election Commission of India</h2>
              <p>Dear $firstname $lastname,</p>
              <p>Your Form 6 application has been successfully submitted and sent for BLO field verification.</p>
              <hr style='border: none; border-top: 1px solid #e2e8f0;'>
              <p><b>Acknowledgment / Reference ID:</b> <span style='color: #0b63c5; font-size: 16px; font-weight: bold;'>$ref_no</span></p>
              <p><b>Assembly Constituency:</b> $constituency</p>
              <p><b>Submission Date:</b> $declaration_date</p>
              <p>Please keep this Reference ID safe to track your application status.</p>
            </div>
          ";
          @sendEmailNotification($email, $email_subject, $email_body);
      }
      ?>
      <!DOCTYPE html>
      <html lang="bn">
      <head>
        <meta charset="utf-8">
        <title>Acknowledgement Slip • Form 6</title>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&family=Share+Tech+Mono&display=swap" rel="stylesheet">
        <style>
          body { background: #f1f5f9; font-family: 'Plus Jakarta Sans', sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 20px; }
          .receipt-box { background: #ffffff; max-width: 680px; width: 100%; border-radius: 16px; box-shadow: 0 20px 45px rgba(0,0,0,0.12); padding: 35px; text-align: center; border: 1px solid #e2e8f0; }
          .details-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px 24px; text-align: left; margin: 20px 0; font-size: 13.5px; line-height: 2.2; }
          .btn-action { background: #0b63c5; color: white; padding: 11px 22px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 13.5px; display: inline-block; margin: 0 6px; border: none; cursor: pointer; }
        </style>
      </head>
      <body>
        <div class="receipt-box">
          <div style="font-size: 40px; color: #16a34a; margin-bottom: 10px;">✅</div>
          <h2 style="margin:0; color:#0f172a; font-weight:800; font-size:21px;">Application Submitted Successfully!</h2>
          <p style="color:#64748b; font-size:13px; margin:6px 0 0;">আপনার আবেদনটি সফলভাবে জমা হয়েছে এবং ইমেইলে অ্যাকনলেজমেন্ট নম্বর পাঠানো হয়েছে।</p>

          <div class="details-card">
            <div>• <b>Reference ID / Application No:</b> <span style="font-family:'Share Tech Mono', monospace; font-weight:800; color:#0b63c5;"><?php echo $ref_no; ?></span></div>
            <div>• <b>Applicant Name:</b> <?php echo htmlspecialchars($firstname . ' ' . $lastname); ?></div>
            <div>• <b>Assembly Constituency:</b> <?php echo htmlspecialchars($constituency); ?></div>
            <div>• <b>Submission Date:</b> <?php echo htmlspecialchars($declaration_date); ?></div>
            <div>• <b>Current Status:</b> <span style="color: #f59e0b; font-weight: 700;">Under BLO Field Verification</span></div>
          </div>

          <button onclick="window.print()" class="btn-action" style="background:#10b981;">📥 Download Slip</button>
          <a href="user_login.php" class="btn-action">🏠 Login Portal</a>
        </div>
      </body>
      </html>
    <?php
      exit();
    } else {
      echo "Error: " . $conn->error;
    }
  }
?>