<?php
  session_start();
  include 'includes/conn.php';
  header('Content-Type: application/json');

  if(!isset($_SESSION['voter'])){
    echo json_encode(['status' => 'error', 'message' => 'সেশন অকার্যকর! অনুগ্রহ করে পুনরায় লগইন করুন।']);
    exit();
  }

  $voter_id = (int)$_SESSION['voter'];
  $candidate_id = isset($_POST['candidate_id']) ? (int)$_POST['candidate_id'] : 0;
  $constituency = !empty($_POST['constituency']) ? trim($_POST['constituency']) : 'General';

  // ১. বর্তমান সক্রিয় নির্বাচন চেক
  $elec_q = $conn->query("SELECT id FROM elections WHERE status = 'live' ORDER BY id DESC LIMIT 1");
  if(!$elec_q || $elec_q->num_rows === 0){
    echo json_encode(['status' => 'error', 'message' => 'বর্তমানে কোনো নির্বাচন সক্রিয় নেই!']);
    exit();
  }
  $election_id = (int)$elec_q->fetch_assoc()['id'];

  // ২. ডবল ভোট প্রতিরোধ: ডেটাবেজ লেভেল স্ট্রিক্ট ভ্যালিডেশন
  $check_vote = $conn->query("SELECT id FROM voter_election_history WHERE voter_id = '$voter_id' AND election_id = '$election_id' LIMIT 1");
  if($check_vote && $check_vote->num_rows > 0){
    echo json_encode(['status' => 'error', 'message' => '⚠️ আপনি ইতিমধ্যে এই নির্বাচনে ভোট প্রদান করেছেন! দ্বিতীয়বার ভোটদান সম্ভব নয়।']);
    exit();
  }

  // ৩. ডেটাবেজ ট্রানজ্যাকশন লক (Atomic Rollback Engine)
  $conn->begin_transaction();

  try {
    // ক্রিপ্টোগ্রাফিক পেলোড ও হ্যাশ তৈরি
    $payload = json_encode([
      'election_id' => $election_id,
      'position_id' => 1,
      'candidate_id' => $candidate_id,
      'constituency' => $constituency,
      'nonce' => bin2hex(random_bytes(16)),
      'timestamp' => microtime(true)
    ]);

    $cipher_method = "AES-256-CBC";
    $enc_key = hash('sha256', 'ECI_SECRET_BALLOT_MASTER_KEY_2026', true);
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher_method));
    $encrypted = openssl_encrypt($payload, $cipher_method, $enc_key, 0, $iv);
    $final_payload = base64_encode($iv . $encrypted);
    $block_hash = hash('sha256', $final_payload . $constituency);

    // ব্যালট বক্সে এনক্রিপ্ট ভোট ইনসার্ট
    $stmt1 = $conn->prepare("INSERT INTO encrypted_ballots (constituency, election_id, position_id, candidate_id, encrypted_vote_payload, block_hash) VALUES (?, ?, 1, ?, ?, ?)");
    $stmt1->bind_param("siiss", $constituency, $election_id, $candidate_id, $final_payload, $block_hash);
    $stmt1->execute();

    // ভোটার হিস্ট্রি লক (Unique Constraint দ্বারা ডুপ্লিকেট রোধ)
    $stmt2 = $conn->prepare("INSERT INTO voter_election_history (voter_id, election_id) VALUES (?, ?)");
    $stmt2->bind_param("ii", $voter_id, $election_id);
    $stmt2->execute();

    // ভোটারের গ্লোবাল ফ্ল্যাগ লক
    $stmt3 = $conn->prepare("UPDATE voters SET has_voted = 1, voted_at = NOW() WHERE id = ?");
    $stmt3->bind_param("i", $voter_id);
    $stmt3->execute();

    // সমস্ত কুয়েরি সফল হলে কমিট
    $conn->commit();

    // সেশন নিষ্ক্রিয় করা
    $_SESSION['vote_completed_hash'] = $block_hash;
    unset($_SESSION['voter']);

    echo json_encode([
      'status' => 'success',
      'message' => 'ভোট সফলভাবে লক হয়েছে!',
      'block_hash' => $block_hash
    ]);
  } catch (Exception $e) {
    // সার্ভার ডাউন বা কোনো একটি কুয়েরি ব্যর্থ হলে স্বয়ংক্রিয় রোলব্যাক (কোনো অর্ধেক ডেটা জমা হবে না)
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'সার্ভারে সাময়িক সমস্যা দেখা দিয়েছে! কোনো ভোট জমা পড়েনি। অনুগ্রহ করে পুনরায় চেষ্টা করুন।']);
  }
?>