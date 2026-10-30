<?php
  session_start();
  $hash = $_GET['hash'] ?? 'N/A';
?>
<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="utf-8">
  <title>ভোট সফল • Secret Ballot Secured</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@700;800&family=Share+Tech+Mono&display=swap" rel="stylesheet">
  <style>
    body { background: #0f172a; color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 15px; }
    .receipt-card { background: #1e293b; max-width: 520px; width: 100%; border-radius: 18px; border: 2px solid #10b981; padding: 35px 25px; text-align: center; box-shadow: 0 20px 45px rgba(0,0,0,0.5); }
    .hash-box { background: #0f172a; border: 1px solid #334155; padding: 12px; border-radius: 8px; font-family: 'Share Tech Mono', monospace; font-size: 12px; color: #38bdf8; word-break: break-all; margin: 18px 0; }
    .btn-exit { background: #0b63c5; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; display: inline-block; font-size: 14px; }
  </style>
</head>
<body>
  <div class="receipt-card">
    <div style="font-size: 60px; color: #10b981; margin-bottom: 6px;">✓</div>
    <h2 style="margin:0; font-size:22px; font-weight:800;">আপনার ভোটটি সুরক্ষিতভাবে সম্পন্ন হয়েছে!</h2>
    <p style="color:#94a3b8; font-size:13.5px; margin:8px 0 0;">ইভিএম দীর্ঘ সংকেতের মাধ্যমে ভোটটি নিরাপদে এনক্রিপ্ট হয়ে ডেটাবেজে জমা পড়েছে।</p>
    <div class="hash-box">
      <span style="color:#94a3b8; font-size:11px;">Cryptographic Audit Block Hash:</span><br>
      <?php echo htmlspecialchars($hash); ?>
    </div>
    <a href="voter_secure_login.php" class="btn-exit">পোর্টাল থেকে প্রস্থান করুন</a>
  </div>
</body>
</html>