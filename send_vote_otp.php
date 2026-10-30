<?php
  session_start();
  include 'includes/conn.php';
  header('Content-Type: application/json');

  date_default_timezone_set('Asia/Kolkata');
  $current_time = date('Y-m-d H:i:s');
  $current_ts = strtotime($current_time);

  if(isset($_POST['voter_id']) && isset($_POST['mobile'])){
    $voter_id_num = trim($_POST['voter_id']);
    $mobile = trim($_POST['mobile']);

    // ১. ভোটার ও মোবাইল নম্বর যাচাই
    $stmt = $conn->prepare("SELECT * FROM voters WHERE voters_id = ? AND phone = ? LIMIT 1");
    $stmt->bind_param("ss", $voter_id_num, $mobile);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res->num_rows === 0){
      echo json_encode(['status' => 'error', 'message' => 'ভোটার আইডি অথবা লিঙ্কড মোবাইল নম্বর মিলেনি!']);
      exit();
    }

    $voter = $res->fetch_assoc();
    $voter_db_id = $voter['id'];

    // ২. সর্বশেষ সক্রিয় / নির্ধারিত নির্বাচন খোঁজা
    $elec_query = $conn->query("SELECT * FROM elections ORDER BY id DESC LIMIT 1");

    if(!$elec_query || $elec_query->num_rows === 0){
      echo json_encode([
        'status' => 'error',
        'message' => '⚠️ বর্তমানে কোনো নির্বাচন নির্ধারিত নেই!'
      ]);
      exit();
    }

    $election = $elec_query->fetch_assoc();
    $active_election_id = $election['id'];
    $start_ts = strtotime($election['start_time']);
    $end_ts = strtotime($election['end_time']);
    $elec_status = $election['status'];

    // ৩. নির্দিষ্ট এই নির্বাচনে ভোটার ইতিমধ্যে ভোট দিয়েছেন কিনা তা হিস্ট্রি টেবিল থেকে চেক
    $history_check = $conn->query("SELECT id FROM voter_election_history WHERE voter_id = '$voter_db_id' AND election_id = '$active_election_id' LIMIT 1");
    $has_voted_in_this_election = ($history_check && $history_check->num_rows > 0);

    // ৪. কন্ডিশনাল চেকিং

    // (A) ভোট যদি এখনও শুরু না হয়ে থাকে (ভবিষ্যতের তারিখ বা সময়ের কাউন্টডাউন)
    if($current_ts < $start_ts && $elec_status !== 'completed'){
      $diff = $start_ts - $current_ts;
      if($diff <= 86400){
        $hours = floor($diff / 3600);
        $minutes = floor(($diff % 3600) / 60);
        $seconds = $diff % 60;
        $countdown_str = "";
        if($hours > 0) $countdown_str .= $hours . " ঘণ্টা ";
        $countdown_str .= $minutes . " মিনিট " . $seconds . " সেকেন্ড";

        echo json_encode([
          'status' => 'error',
          'message' => "⏳ আজকের ভোটগ্রহণ এখনও শুরু হয়নি!\nভোট শুরু হতে বাকি: " . $countdown_str
        ]);
      } else {
        echo json_encode([
          'status' => 'error',
          'message' => "🕒 '{$election['title']}' এখনও শুরু হয়নি!\nভোটগ্রহণের তারিখ: " . date('d M Y, h:i A', $start_ts) . " (Coming Soon)"
        ]);
      }
      exit();
    }

    // (B) নির্বাচনের সময়সীমা শেষ হয়ে গেলে
    if($current_ts > $end_ts || $elec_status === 'completed'){
      if($has_voted_in_this_election){
        echo json_encode([
          'status' => 'error',
          'message' => "🔒 এই নির্বাচন সমাপ্ত হয়েছে!\nআপনার ভোটটি সফলভাবে ব্যালট বক্সে সংরক্ষিত রয়েছে।"
        ]);
      } else {
        echo json_encode([
          'status' => 'error',
          'message' => "⛔ '{$election['title']}' এর নির্ধারিত সময়সীমা সমাপ্ত হয়েছে!\nভোটগ্রহণের পোর্টাল এখন বন্ধ।"
        ]);
      }
      exit();
    }

    // (C) নির্বাচন সাময়িকভাবে স্থগিত থাকলে
    if($elec_status === 'paused'){
      echo json_encode([
        'status' => 'error',
        'message' => '⏸️ নির্বাচন কমিশনের নির্দেশে ভোটগ্রহণ সাময়িকভাবে স্থগিত রয়েছে!'
      ]);
      exit();
    }

    // (D) নির্বাচন লাইভ, কিন্তু এই নির্দিষ্ট নির্বাচনে তিনি ইতিমধ্যে ভোট দিয়ে ফেলেছেন
    if($has_voted_in_this_election){
      echo json_encode([
        'status' => 'error',
        'message' => "⚠️ আপনি ইতিমধ্যে এই নির্বাচনে ({$election['title']}) আপনার মূল্যবান ভোট প্রদান করেছেন!"
      ]);
      exit();
    }

    include 'includes/sms_helper.php';

    // ১. র‍্যান্ডম ৬ সংখ্যার ওটিপি তৈরি
    $otp = (string)rand(100000, 999999);
    $_SESSION['auth_otp'] = $otp;
    $_SESSION['auth_voter_id'] = $voter_db_id;
    $_SESSION['active_election_id'] = $active_election_id;

    // ২. ভোটারের নম্বরে সরাসরি এসএমএস পাঠানো
    $sms_sent = sendLiveSMS($mobile, $otp);

    if($sms_sent){
      echo json_encode([
        'status' => 'success',
        'message' => 'আপনার মোবাইলে ৬ সংখ্যার ওটিপি পাঠানো হয়েছে।'
      ]);
    } else {
      // গেটওয়ে রেসপন্স না করলে ব্যাকআপ ওটিপি
      echo json_encode([
        'status' => 'success',
        'message' => 'এসএমএস গেটওয়ে সংযুক্ত হয়নি, টেস্ট ওটিপি প্রদান করা হলো।',
        'demo_otp' => $otp
      ]);
    }
  } else {
    echo json_encode(['status' => 'error', 'message' => 'তথ্য অসম্পূর্ণ।']);
  }
?>