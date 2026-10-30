<?php
  session_start();
  include 'includes/conn.php';
  header('Content-Type: application/json');

  if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $voter_id_num = trim($_POST['voter_id'] ?? '');
    $live_descriptor_raw = $_POST['live_descriptor'] ?? '';
    $live_image_base64 = $_POST['live_image'] ?? '';

    if(empty($voter_id_num) || empty($live_descriptor_raw)){
      echo json_encode(['status' => 'error', 'message' => 'ফেস ডেটা পাওয়া যায়নি!']);
      exit();
    }

    $live_descriptor = json_decode($live_descriptor_raw, true);

    // ভোটারের তথ্য বের করা
    $stmt = $conn->prepare("SELECT v.id, v.voters_id, v.photo, b.face_descriptor FROM voters v LEFT JOIN voter_biometrics b ON v.id = b.voter_id WHERE v.voters_id = ? LIMIT 1");
    $stmt->bind_param("s", $voter_id_num);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res->num_rows === 0){
      echo json_encode(['status' => 'error', 'message' => 'ভোটার রেকর্ড পাওয়া যায়নি!']);
      exit();
    }

    $voter = $res->fetch_assoc();
    $db_descriptor = !empty($voter['face_descriptor']) ? json_decode($voter['face_descriptor'], true) : null;

    // লাইভ ক্যাপচার করা ছবি ফাইলে সেভ করা
    $captured_filename = 'login_face_' . $voter['id'] . '_' . time() . '.png';
    if(!empty($live_image_base64)){
      $image_parts = explode(";base64,", $live_image_base64);
      if(isset($image_parts[1])){
        $image_base64 = base64_decode($image_parts[1]);
        file_put_contents('images/' . $captured_filename, $image_base64);
      }
    }

    // প্রথমবার হলে বায়োমেট্রিক সেভ
    if(!$db_descriptor){
      $store_stmt = $conn->prepare("INSERT INTO voter_biometrics (voter_id, face_descriptor) VALUES (?, ?) ON DUPLICATE KEY UPDATE face_descriptor = ?");
      $store_stmt->bind_param("iss", $voter['id'], $live_descriptor_raw, $live_descriptor_raw);
      $store_stmt->execute();
    }

    // ইউক্লিডিয়ান ডিস্ট্যান্স হিসেব
    $distance = 0.0;
    if($db_descriptor){
      $sum = 0.0;
      for ($i = 0; $i < 128; $i++) {
        $diff = $db_descriptor[$i] - $live_descriptor[$i];
        $sum += $diff * $diff;
      }
      $distance = sqrt($sum);
    }

    // ০.৪৬-এর নিচে থাকলে ম্যাচ
    if(!$db_descriptor || $distance < 0.46){
      $_SESSION['face_auth_passed'] = true;
      $_SESSION['authenticated_voter_id'] = $voter['id'];

      // লগ টেবিলে অডিট হিস্ট্রি ইনসার্ট
      $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
      $dist_str = round($distance, 4);
      $log_stmt = $conn->prepare("INSERT INTO voter_login_logs (voter_id, voters_id, captured_photo, distance_score, ip_address) VALUES (?, ?, ?, ?, ?)");
      $log_stmt->bind_param("issss", $voter['id'], $voter['voters_id'], $captured_filename, $dist_str, $ip);
      $log_stmt->execute();

      echo json_encode([
        'status' => 'success',
        'message' => 'ফেস ম্যাচ ১০০% সফল হয়েছে!',
        'distance' => round($distance, 4)
      ]);
    } else {
      $_SESSION['face_auth_passed'] = false;
      echo json_encode([
        'status' => 'error',
        'message' => 'সংরক্ষিত ভোটারের সাথে মুখের মিল পাওয়া যায়নি!',
        'distance' => round($distance, 4)
      ]);
    }
  }
?>