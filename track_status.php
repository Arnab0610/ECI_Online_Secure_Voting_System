<?php
  session_start();
  include 'includes/conn.php';
  include 'includes/header.php';

  $status_data = null;
  $error_msg = '';
  $searched_ref = '';

  if(isset($_POST['track_btn'])){
    $searched_ref = trim($_POST['ref_no']);
    if(!empty($searched_ref)){
      $stmt = $conn->prepare("SELECT * FROM voter_applications WHERE ref_no = ?");
      $stmt->bind_param("s", $searched_ref);
      $stmt->execute();
      $res = $stmt->get_result();
      if($res->num_rows > 0){
        $status_data = $res->fetch_assoc();
      } else {
        $error_msg = "কোনো আবেদন পাওয়া যায়নি। অনুগ্রহ করে সঠিক রেফারেন্স নম্বর দিন।";
      }
    } else {
      $error_msg = "অনুগ্রহ করে অ্যাপ্লিকেশন রেফারেন্স নম্বর প্রদান করুন।";
    }
  }
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Share+Tech+Mono&display=swap" rel="stylesheet">

<style>
  :root {
    --eci-blue: #0b63c5;
    --eci-navy: #0f172a;
    --border-color: #e2e8f0;
  }
  * { box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
  body {
    background: #f0f4f9 url('images/vote-bg.png') no-repeat center center fixed;
    background-size: cover;
    min-height: 100vh;
    margin: 0;
    padding: 30px 15px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }
  .track-container {
    width: 100%;
    max-width: 820px;
    background: #ffffff;
    border-radius: 18px;
    box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.2);
    overflow: hidden;
    border: 1px solid var(--border-color);
  }
  .track-header {
    background: linear-gradient(135deg, #0b63c5, #0369a1);
    color: #ffffff;
    padding: 20px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 3.5px solid #ff9933;
  }
  .track-body { padding: 30px 35px; }

  .search-box {
    display: flex;
    gap: 12px;
    margin-bottom: 25px;
  }
  .search-input {
    flex-grow: 1;
    height: 46px;
    border-radius: 10px;
    border: 1.5px solid #cbd5e1;
    padding: 10px 16px;
    font-size: 15px;
    font-family: 'Share Tech Mono', monospace;
    font-weight: 700;
    outline: none;
  }
  .search-input:focus {
    border-color: var(--eci-blue);
    box-shadow: 0 0 0 3.5px rgba(11, 99, 197, 0.12);
  }
  .btn-track {
    background: var(--eci-blue);
    color: #ffffff;
    border: none;
    padding: 0 24px;
    border-radius: 10px;
    font-weight: 700;
    cursor: pointer;
    font-size: 14px;
    transition: 0.2s;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .btn-track:hover { background: #094d99; }

  /* ================= STEPPER PROGRESS BAR ================= */
  .stepper-wrapper {
    display: flex;
    justify-content: space-between;
    position: relative;
    margin-top: 35px;
    margin-bottom: 30px;
  }
  .stepper-wrapper::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 40px;
    right: 40px;
    height: 4px;
    background: #e2e8f0;
    z-index: 1;
  }
  .step-node {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    width: 25%;
  }
  .step-icon {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: #ffffff;
    border: 3px solid #cbd5e1;
    color: #94a3b8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    font-weight: 800;
    transition: all 0.3s ease;
  }
  .step-label {
    margin-top: 10px;
    font-size: 12.5px;
    font-weight: 700;
    color: #64748b;
  }
  .step-date {
    font-size: 11px;
    color: #94a3b8;
    margin-top: 2px;
  }

  /* Node States */
  .step-node.completed .step-icon {
    background: #10b981;
    border-color: #10b981;
    color: #ffffff;
  }
  .step-node.completed .step-label { color: #0f172a; }

  .step-node.active .step-icon {
    background: #0b63c5;
    border-color: #0b63c5;
    color: #ffffff;
    box-shadow: 0 0 12px rgba(11, 99, 197, 0.4);
  }
  .step-node.active .step-label { color: #0b63c5; }

  .step-node.rejected .step-icon {
    background: #ef4444;
    border-color: #ef4444;
    color: #ffffff;
  }
  .step-node.rejected .step-label { color: #ef4444; }

  .info-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    margin-top: 20px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    font-size: 13.5px;
  }

  @media (max-width: 650px) {
    .info-card { grid-template-columns: 1fr; }
    .step-label { font-size: 11px; }
  }
</style>

<body>

<div class="track-container">
  
  <!-- Header -->
  <div class="track-header">
    <div style="display:flex; align-items:center; gap:12px;">
      <div style="background:#ffffff; width:38px; height:38px; border-radius:8px; display:flex; align-items:center; justify-content:center;">
        <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/Emblem_of_India.svg" style="height:28px;" alt="ECI">
      </div>
      <div>
        <h2 style="margin:0; font-size:17.5px; font-weight:800;">Track Application Status • Form 6</h2>
        <p style="margin:2px 0 0; font-size:11px; opacity:0.9;">নির্বাচন কমিশন আবেদন ট্র্যাকিং পোর্টাল</p>
      </div>
    </div>
    <a href="register.php" style="color:#ffffff; text-decoration:none; font-size:12.5px; font-weight:700; background:rgba(255,255,255,0.2); padding:6px 14px; border-radius:8px; border:1px solid rgba(255,255,255,0.4);">
      <i class="fa fa-pencil-square-o"></i> New Form 6
    </a>
  </div>

  <div class="track-body">
    
    <!-- Search Reference Bar -->
    <form method="POST" action="track_status.php" class="search-box">
      <input type="text" name="ref_no" class="search-input" placeholder="Enter Application Ref No. (e.g. VTR-WB-2026-XXXXXX)" value="<?php echo htmlspecialchars($searched_ref); ?>" required>
      <button type="submit" name="track_btn" class="btn-track">
        <i class="fa fa-search"></i> Track Status
      </button>
    </form>

    <?php if(!empty($error_msg)): ?>
      <div style="background:#fee2e2; border:1px solid #fecaca; color:#991b1b; padding:14px; border-radius:10px; font-size:13.5px; font-weight:600; text-align:center;">
        <i class="fa fa-exclamation-triangle"></i> <?php echo $error_msg; ?>
      </div>
    <?php endif; ?>

    <?php if($status_data): 
      $st = $status_data['status'];
      // স্ট্যাটাস লজিক
      $step1_class = 'completed'; // সাবমিশন সর্বদা সম্পন্ন
      $step2_class = '';
      $step3_class = '';
      $step4_class = '';

      if($st == 'PENDING_BLO'){
        $step2_class = 'active';
      } elseif($st == 'BLO_APPROVED'){
        $step2_class = 'completed';
        $step3_class = 'active';
      } elseif($st == 'ERO_APPROVED'){
        $step2_class = 'completed';
        $step3_class = 'completed';
        $step4_class = 'completed';
      } elseif($st == 'BLO_REJECTED'){
        $step2_class = 'rejected';
      } elseif($st == 'ERO_REJECTED'){
        $step2_class = 'completed';
        $step3_class = 'rejected';
      }
    ?>
      <!-- Stepper Visual Track -->
      <div class="stepper-wrapper">
        <!-- Node 1: Submitted -->
        <div class="step-node <?php echo $step1_class; ?>">
          <div class="step-icon"><i class="fa fa-file-text-o"></i></div>
          <div class="step-label">Submitted</div>
          <div class="step-date"><?php echo date('d M Y', strtotime($status_data['created_at'])); ?></div>
        </div>

        <!-- Node 2: BLO Verification -->
        <div class="step-node <?php echo $step2_class; ?>">
          <div class="step-icon">
            <?php if($st == 'BLO_REJECTED') echo '<i class="fa fa-times"></i>'; else echo '<i class="fa fa-user-check"></i>'; ?>
          </div>
          <div class="step-label">
            <?php echo ($st == 'BLO_REJECTED') ? 'BLO Rejected' : 'BLO Appointed & Verified'; ?>
          </div>
        </div>

        <!-- Node 3: ERO Decision -->
        <div class="step-node <?php echo $step3_class; ?>">
          <div class="step-icon">
            <?php if($st == 'ERO_REJECTED') echo '<i class="fa fa-times"></i>'; else echo '<i class="fa fa-gavel"></i>'; ?>
          </div>
          <div class="step-label">
            <?php echo ($st == 'ERO_REJECTED') ? 'ERO Rejected' : 'ERO Scrutiny'; ?>
          </div>
        </div>

        <!-- Node 4: EPIC Generated -->
        <div class="step-node <?php echo $step4_class; ?>">
          <div class="step-icon"><i class="fa fa-id-card"></i></div>
          <div class="step-label">EPIC Card Issued</div>
        </div>
      </div>

      <!-- Information Summary -->
      <div class="info-card">
        <div>• <b>Applicant Name:</b> <?php echo htmlspecialchars($status_data['firstname'].' '.$status_data['lastname']); ?></div>
        <div>• <b>Assembly:</b> <?php echo htmlspecialchars($status_data['constituency']); ?></div>
        <div>• <b>State / District:</b> <?php echo htmlspecialchars($status_data['district'].', '.$status_data['state']); ?></div>
        <div>• <b>Application Date:</b> <?php echo date('d-M-Y', strtotime($status_data['created_at'])); ?></div>
      </div>

      <?php if($st == 'ERO_APPROVED'): ?>
        <div style="margin-top:20px; text-align:center;">
          <a href="user_login.php" style="display:inline-block; background:#10b981; color:#fff; text-decoration:none; padding:12px 28px; border-radius:10px; font-weight:700; font-size:14px;">
            <i class="fa fa-download"></i> Login & Download e-EPIC Card
          </a>
        </div>
      <?php endif; ?>

    <?php endif; ?>

  </div>

</div>

<?php include 'includes/scripts.php'; ?>
</body>
</html>