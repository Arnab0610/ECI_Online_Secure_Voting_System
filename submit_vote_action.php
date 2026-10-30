<?php
  session_start();
  include 'includes/conn.php';

  if(isset($_POST['cast_vote']) && isset($_SESSION['voter'])){
    $voter_id = $_SESSION['voter'];
    $candidate_id = isset($_POST['candidate_id']) ? (int)$_POST['candidate_id'] : 0;
    $position_id = isset($_POST['position_id']) ? (int)$_POST['position_id'] : 1;
    $constituency = !empty($_POST['constituency']) ? trim($_POST['constituency']) : 'General';

    // সক্রিয় নির্বাচনের আইডি নেওয়া
    $elec_query = $conn->query("SELECT id FROM elections WHERE status = 'live' ORDER BY id DESC LIMIT 1");
    $election_id = ($elec_query && $elec_query->num_rows > 0) ? (int)$elec_query->fetch_assoc()['id'] : 1;

    // ১. চেক: ভোটার এই নির্দিষ্ট নির্বাচনে ইতিমধ্যে ভোট দিয়েছেন কিনা
    $history_check = $conn->query("SELECT id FROM voter_election_history WHERE voter_id = '$voter_id' AND election_id = '$election_id' LIMIT 1");
    if($history_check && $history_check->num_rows > 0){
      session_destroy();
      echo "<script>alert('⚠️ আপনি ইতিমধ্যে এই নির্বাচনে ভোট দিয়ে ফেলেছেন!'); window.location.href='voter_secure_login.php';</script>";
      exit();
    }

    $conn->begin_transaction();

    try {
      // ২. ক্রিপ্টোগ্রাফিক পেলোড ও হ্যাশ তৈরি
      $payload = json_encode([
        'election_id' => $election_id,
        'position_id' => $position_id,
        'candidate_id' => $candidate_id,
        'constituency' => $constituency,
        'nonce' => bin2hex(random_bytes(16)),
        'timestamp' => microtime(true)
      ]);

      $cipher_method = "AES-256-CBC";
      $enc_key = hash('sha256', 'ECI_SECRET_BALLOT_MASTER_KEY_2026', true);
      $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher_method));
      $encrypted = openssl_encrypt($payload, $cipher_method, $enc_key, 0, $iv);
      $final_encrypted_payload = base64_encode($iv . $encrypted);
      $block_hash = hash('sha256', $final_encrypted_payload . $constituency);

      // (A) বেনামী ব্যালট বক্সে ভোট সংরক্ষণ (কোনো voter_id নেই)
      $stmt1 = $conn->prepare("INSERT INTO encrypted_ballots (constituency, election_id, position_id, candidate_id, encrypted_vote_payload, block_hash) VALUES (?, ?, ?, ?, ?, ?)");
      $stmt1->bind_param("siiiss", $constituency, $election_id, $position_id, $candidate_id, $final_encrypted_payload, $block_hash);
      $stmt1->execute();

      // (B) নির্দিষ্ট নির্বাচনের হিস্ট্রিতে ভোটারের আইডি লক করা
      $stmt2 = $conn->prepare("INSERT INTO voter_election_history (voter_id, election_id) VALUES (?, ?)");
      $stmt2->bind_param("ii", $voter_id, $election_id);
      $stmt2->execute();

      // (C) ভোটারের সাধারণ স্ট্যাটাস আপডেট
      $stmt3 = $conn->prepare("UPDATE voters SET has_voted = 1, voted_at = NOW() WHERE id = ?");
      $stmt3->bind_param("i", $voter_id);
      $stmt3->execute();

      $conn->commit();

      session_unset();
      session_destroy();
      ?>
      <!DOCTYPE html>
      <html lang="bn">
      <head>
        <meta charset="utf-8">
        <title>ভোট সফল • Secret Ballot Secured</title>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@700;800&family=Tiro+Bangla&family=Share+Tech+Mono&display=swap" rel="stylesheet">
        <style>
          body { background: #0f172a; color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 15px; }
          .receipt-card { background: #1e293b; max-width: 520px; width: 100%; border-radius: 18px; border: 2px solid #10b981; padding: 35px 25px; text-align: center; box-shadow: 0 20px 45px rgba(0,0,0,0.5); }
          .hash-box { background: #0f172a; border: 1px solid #334155; padding: 12px; border-radius: 8px; font-family: 'Share Tech Mono', monospace; font-size: 12px; color: #38bdf8; word-break: break-all; margin: 18px 0; }
          .btn-exit { background: #0b63c5; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; display: inline-block; font-size: 14px; transition: 0.2s; }
          .btn-exit:hover { background: #0369a1; }
        </style>
      </head>
      <body>
        <div class="receipt-card">
          <div style="font-size: 60px; color: #10b981; margin-bottom: 6px;">✓</div>
          <h2 style="margin:0; font-size:22px; font-weight:800;">আপনার ভোটটি সুরক্ষিতভাবে সম্পন্ন হয়েছে!</h2>
          <p style="color:#94a3b8; font-size:13.5px; margin:8px 0 0;">গোপন ব্যালট প্রোটোকল অনুযায়ী আপনার ভোটটি এনক্রিপ্ট হয়ে সংরক্ষিত হয়েছে।</p>
          
          <div class="hash-box">
            <span style="color:#94a3b8; font-size:11px;">Cryptographic Audit Block Hash:</span><br>
            <?php echo $block_hash; ?>
          </div>

          <a href="voter_secure_login.php" class="btn-exit">পোর্টাল থেকে প্রস্থান করুন</a>
        </div>
      </body>
      </html>
      <?php
      exit();

    } catch (Exception $e) {
      $conn->rollback();
      echo "ভোট প্রক্রিয়াকরণে সমস্যা হয়েছে: " . $e->getMessage();
    }
  } else {
    header('location: voter_secure_login.php');
    exit();
  }
?>