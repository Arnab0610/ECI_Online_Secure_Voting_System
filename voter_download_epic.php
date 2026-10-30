<?php include 'includes/session.php'; ?>
<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="utf-8">
  <title>Digital Voter Card (e-EPIC) • National Portal</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Tiro+Bangla&family=Share+Tech+Mono&display=swap" rel="stylesheet">
  <style>
    body { background: #0f172a; font-family: 'Plus Jakarta Sans', sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 20px; color: #fff; }
    
    /* Official PVC Card Design */
    .epic-card-pvc {
      width: 480px;
      height: 290px;
      border-radius: 16px;
      background: linear-gradient(135deg, #fff7ed 0%, #ffffff 50%, #f0fdf4 100%);
      color: #0f172a;
      border: 2px solid #cbd5e1;
      box-shadow: 0 20px 45px rgba(0,0,0,0.5);
      position: relative;
      overflow: hidden;
      padding: 16px 20px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .epic-top-bar { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #0b63c5; padding-bottom: 6px; }
    .epic-grid { display: grid; grid-template-columns: 100px 1fr; gap: 15px; align-items: center; }
    .epic-photo { width: 100px; height: 120px; border-radius: 8px; object-fit: cover; border: 2px solid #0b63c5; background: #e2e8f0; }
    .epic-details { font-size: 11.5px; line-height: 1.6; }
    .epic-no { font-family: 'Share Tech Mono', monospace; font-size: 15px; font-weight: 800; color: #0b63c5; letter-spacing: 1px; }

    .print-btn { background: #0b63c5; color: white; border: none; padding: 12px 28px; border-radius: 8px; font-weight: 700; margin-top: 25px; cursor: pointer; font-size: 14px; }
  </style>
</head>
<body>

  <div class="epic-card-pvc" id="pvcCard">
    <div class="epic-top-bar">
      <div style="display:flex; align-items:center; gap:8px;">
        <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/Emblem_of_India.svg" style="height:26px;">
        <div>
          <div style="font-size:11px; font-weight:800; color:#0b63c5;">ELECTION COMMISSION OF INDIA</div>
          <div style="font-size:9.5px; font-weight:700; color:#64748b;">ভারত নির্বাচন কমিশন • VOTER IDENTITY CARD</div>
        </div>
      </div>
      <div class="epic-no"><?php echo htmlspecialchars($voter['voters_id']); ?></div>
    </div>

    <div class="epic-grid">
      <img src="<?php echo (!empty($voter['photo'])) ? 'images/'.$voter['photo'] : 'images/profile.jpg'; ?>" class="epic-photo">
      <div class="epic-details">
        <div><b>Name:</b> <?php echo htmlspecialchars($voter['firstname'].' '.$voter['lastname']); ?></div>
        <div><b>Gender:</b> Male</div>
        <div><b>Assembly:</b> 162 - Chowrangee, West Bengal</div>
        <div><b>Issue Date:</b> <?php echo date('d-m-Y'); ?></div>
        <div style="color:#16a34a; font-weight:700; font-size:10px; margin-top:3px;">✓ Digitally Certified Security Vault</div>
      </div>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:flex-end; font-size:9px; color:#64748b; border-top:1px dashed #cbd5e1; padding-top:4px;">
      <span>Electoral Registration Officer (ERO) Signature</span>
      <span style="font-family:'Share Tech Mono', monospace;">SEC-CRYPT-256</span>
    </div>
  </div>

  <button class="print-btn" onclick="window.print()">🖨️ Download & Print Official e-EPIC Card</button>
  <a href="home.php" style="color:#94a3b8; margin-top:15px; font-size:13px; text-decoration:none;">⬅ Return to Dashboard</a>

</body>
</html>