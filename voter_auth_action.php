<?php
  session_start();
  include 'includes/conn.php';

  if(isset($_POST['secure_login'])){
    $voter_epic = trim($_POST['voter_identity']);
    $mobile = trim($_POST['mobile']);
    $face_verified = $_POST['face_verified'] ?? '0';

    // ফেস ও ওটিপি ফ্লাগ যাচাই
    if($face_verified !== '1'){
      echo "<script>
        alert('❌ ফেস ভেরিফিকেশন সফল হয়নি! অনুগ্রহ করে সঠিকভাবে স্ক্যান সম্পন্ন করুন।');
        window.location.href = 'voter_secure_login.php';
      </script>";
      exit();
    }

    // ভোটার তথ্য খোঁজা
    $stmt = $conn->prepare("SELECT * FROM voters WHERE voters_id = ? AND phone = ? LIMIT 1");
    $stmt->bind_param("ss", $voter_epic, $mobile);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res->num_rows === 0){
      echo "<script>
        alert('❌ ভোটার বিবরণ পাওয়া যায়নি!');
        window.location.href = 'voter_secure_login.php';
      </script>";
      exit();
    }

    $voter = $res->fetch_assoc();
    $voter_id = $voter['id'];

    // চলমান সক্রিয় নির্বাচন চেক (Time-based ও Status-based)
    date_default_timezone_set('Asia/Kolkata');
    $now = date('Y-m-d H:i:s');

    $elec_q = $conn->query("
      SELECT * FROM elections 
      WHERE (status = 'live' OR (status = 'scheduled' AND '$now' BETWEEN start_time AND end_time))
      ORDER BY id DESC LIMIT 1
    ");

    if(!$elec_q || $elec_q->num_rows === 0){
      echo "<script>
        alert('⚠️ বর্তমানে কোনো নির্বাচন সক্রিয় নেই!');
        window.location.href = 'voter_secure_login.php';
      </script>";
      exit();
    }

    $election = $elec_q->fetch_assoc();
    $election_id = $election['id'];

    // ভোটার এই নির্দিষ্ট নির্বাচনে আগে ভোট দিয়েছেন কিনা তা হিস্ট্রি টেবিল থেকে চেক
    $history_check = $conn->query("SELECT id FROM voter_election_history WHERE voter_id = '$voter_id' AND election_id = '$election_id' LIMIT 1");

    if($history_check && $history_check->num_rows > 0){
      echo "<script>
        alert('⚠️ আপনি ইতিমধ্যে এই নির্বাচনে ({$election['title']}) আপনার ভোট প্রদান করেছেন!');
        window.location.href = 'voter_secure_login.php';
      </script>";
      exit();
    }

    // সফল ভ্যালিডেশন: ব্যালট সেশন শুরু
    $_SESSION['voter'] = $voter['id'];
    $_SESSION['active_election_id'] = $election_id;

    header('location: ballot_paper.php');
    exit();

  } else {
    header('location: voter_secure_login.php');
    exit();
  }
?>