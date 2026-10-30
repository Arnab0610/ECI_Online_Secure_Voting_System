<?php
  session_start();
  include 'includes/conn.php';

  // স্বয়ংক্রিয় নির্বাচন সময়সূচী অ্যাক্টিভেশন ও ক্লোজিং ইঞ্জিন
  date_default_timezone_set('Asia/Kolkata');
  $curr_time = date('Y-m-d H:i:s');
  $conn->query("UPDATE elections SET status = 'live' WHERE status = 'scheduled' AND start_time <= '$curr_time' AND end_time > '$curr_time'");
  $conn->query("UPDATE elections SET status = 'completed' WHERE status = 'live' AND end_time < '$curr_time'");

  if(isset($_SESSION['voter'])){
    header('location: ballot_paper.php');
    exit();
  }
?>
<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="utf-8">
  <title>অনলাইন ভোটদান পোর্টাল • Election Commission of India</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Tiro+Bangla&family=Share+Tech+Mono&display=swap" rel="stylesheet">
  
  <!-- Face API AI Model Engine -->
  <script defer src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.min.js"></script>

  <style>
    :root {
      --eci-blue: #0b63c5;
      --eci-navy: #0f172a;
      --eci-orange: #ff9933;
    }
    * { box-sizing: border-box; font-family: 'Plus Jakarta Sans', 'Tiro Bangla', sans-serif; }
    body {
      background: #f0f4f9 url('images/vote-bg.png') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
      margin: 0;
      padding: 16px 12px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-card {
      width: 100%;
      max-width: 520px;
      background: #ffffff;
      border-radius: 18px;
      box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.2);
      border: 1.5px solid #e2e8f0;
      overflow: hidden;
    }
    .card-header {
      background: linear-gradient(135deg, #0b63c5, #0369a1);
      color: #ffffff;
      padding: 16px 18px;
      text-align: center;
      border-bottom: 3.5px solid var(--eci-orange);
    }
    .card-body { padding: 20px 18px; }
    .form-group { margin-bottom: 14px; }
    .form-group label { display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px; }
    .form-input {
      width: 100%;
      height: 44px;
      border-radius: 8px;
      border: 1.5px solid #cbd5e1;
      padding: 8px 12px;
      font-size: 14px;
      outline: none;
    }
    .form-input:focus { border-color: var(--eci-blue); box-shadow: 0 0 0 3px rgba(11,99,197,0.15); }

    /* রেসপন্সিভ ওটিপি ইনপুট গ্রিড */
    .otp-container {
      display: grid;
      grid-template-columns: repeat(6, 1fr);
      gap: 6px;
      width: 100%;
    }
    .otp-digit {
      width: 100%;
      height: 46px;
      text-align: center;
      font-size: 18px;
      font-weight: 800;
      border-radius: 8px;
      border: 1.5px solid #cbd5e1;
      outline: none;
      font-family: 'Share Tech Mono', monospace;
    }

    #faceVerificationSection {
      display: none;
      background: #f8fafc;
      border: 1.5px dashed #0284c7;
      border-radius: 12px;
      padding: 14px;
      text-align: center;
      margin: 16px 0;
    }

    /* রেসপন্সিভ ক্যামেরা ফ্রেম */
    .video-wrapper {
      position: relative;
      width: 100%;
      max-width: 300px;
      aspect-ratio: 4 / 3;
      height: auto;
      margin: 10px auto;
      border-radius: 8px;
      overflow: hidden;
      display: none;
      background: #000;
      border: 2px solid #0284c7;
    }
    #webcam, #capturedPhotoPreview { width: 100%; height: 100%; object-fit: cover; }
    
    .btn-main {
      width: 100%;
      min-height: 46px;
      background: var(--eci-blue);
      color: #ffffff;
      border: none;
      border-radius: 10px;
      font-size: 14.5px;
      font-weight: 800;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: 0.2s;
    }

    @media (max-width: 480px) {
      body { padding: 10px 8px; }
      .card-body { padding: 16px 14px; }
      .otp-digit { height: 42px; font-size: 16px; }
      .btn-main { font-size: 13.5px; }
    }
  </style>
</head>
<body>

<div class="login-card">
  <div class="card-header">
    <div style="display:flex; justify-content:center; align-items:center; gap:10px; margin-bottom:4px;">
      <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/Emblem_of_India.svg" style="height:28px;" alt="ECI">
      <h3 style="margin:0; font-size:17px; font-weight:800;">ভারতের নির্বাচন কমিশন • ECI</h3>
    </div>
    <div style="font-size:12px; opacity:0.9;">[ অনলাইন ভোটদান পোর্টাল - Secure EVM Gateway ]</div>
  </div>

  <?php if(isset($_GET['session_expired'])): ?>
  <div style="background:#fee2e2; border:1px solid #fecaca; color:#991b1b; padding:10px 14px; border-radius:8px; font-size:12.5px; font-weight:700; margin-bottom:15px; text-align:center;">
    ⚠️ নিরাপত্তার স্বার্থে আপনার ৫ মিনিটের সেশনের সময়সীমা শেষ হয়ে গেছে! অনুগ্রহ করে পুনরায় ওটিপি ও ফেস ভেরিফাই করে লগইন করুন।
  </div>
<?php endif; ?>

  <div class="card-body">
    <?php
      date_default_timezone_set('Asia/Kolkata');
      $c_now = date('Y-m-d H:i:s');
      $c_ts = strtotime($c_now);
      $e_check = $conn->query("SELECT * FROM elections ORDER BY id DESC LIMIT 1");
      
      $status_banner = "";
      if(!$e_check || $e_check->num_rows === 0){
        $status_banner = "<div style='background:#fef3c7; color:#92400e; border:1px solid #fde68a; padding:8px 12px; border-radius:8px; font-size:12px; font-weight:700; text-align:center; margin-bottom:14px;'>📢 বর্তমানে কোনো নির্বাচন নির্ধারিত নেই।</div>";
      } else {
        $e_data = $e_check->fetch_assoc();
        $s_ts = strtotime($e_data['start_time']);
        $e_ts = strtotime($e_data['end_time']);

        if($c_ts < $s_ts){
          $status_banner = "<div style='background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd; padding:8px 12px; border-radius:8px; font-size:12px; font-weight:700; text-align:center; margin-bottom:14px;'>🕒 আগামী নির্বাচন: ".htmlspecialchars($e_data['title'])."<br>ভোটগ্রহণ শুরু: ".date('d M Y, h:i A', $s_ts)."</div>";
        } elseif($c_ts >= $s_ts && $c_ts <= $e_ts && $e_data['status'] === 'live'){
          $status_banner = "<div style='background:#dcfce7; color:#166534; border:1px solid #bbf7d0; padding:8px 12px; border-radius:8px; font-size:12px; font-weight:700; text-align:center; margin-bottom:14px;'>🟢 লাইভ ভোটগ্রহণ চলছে: ".htmlspecialchars($e_data['title'])."<br>শেষ সময়: ".date('h:i A', $e_ts)." পর্যন্ত</div>";
        } else {
          $status_banner = "<div style='background:#fee2e2; color:#991b1b; border:1px solid #fecaca; padding:8px 12px; border-radius:8px; font-size:12px; font-weight:700; text-align:center; margin-bottom:14px;'>⛔ ভোটগ্রহণ সমাপ্ত হয়েছে: ".htmlspecialchars($e_data['title'])."</div>";
        }
      }
      echo $status_banner;
    ?>
    <form action="voter_auth_action.php" method="POST" id="voteLoginForm" onsubmit="return validateFinalAuth()">
      
      <div class="form-group">
        <label>* ভোটার আইডি (EPIC Number):</label>
        <input type="text" name="voter_identity" id="voterIdInput" class="form-input" placeholder="WB/144/XXXXXX" required>
      </div>

      <div class="form-group">
        <label>* রেজিস্টার্ড লিঙ্কড মোবাইল নম্বর:</label>
        <div style="display:flex; gap:8px;">
          <input type="tel" name="mobile" id="userMobile" class="form-input" placeholder="9876543210" maxlength="10" required>
          <button type="button" class="btn-main" style="width:auto; padding:0 14px; font-size:13px;" onclick="requestLinkedOTP()">ওটিপি পাঠান</button>
        </div>
      </div>

      <div class="form-group" id="otpInputArea" style="display:none;">
        <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
          <label style="margin:0;">* ওটিপি দিন (Enter 6-Digit OTP):</label>
          <span style="font-size:11.5px; color:#d97706; font-weight:700;" id="otpTimerText">⏱️ মেয়াদ: 02:00</span>
        </div>
        <div class="otp-container">
          <input type="text" class="otp-digit" maxlength="1" oninput="moveOtp(this, 1)">
          <input type="text" class="otp-digit" maxlength="1" oninput="moveOtp(this, 2)">
          <input type="text" class="otp-digit" maxlength="1" oninput="moveOtp(this, 3)">
          <input type="text" class="otp-digit" maxlength="1" oninput="moveOtp(this, 4)">
          <input type="text" class="otp-digit" maxlength="1" oninput="moveOtp(this, 5)">
          <input type="text" class="otp-digit" maxlength="1" oninput="moveOtp(this, 6)">
        </div>
        <input type="hidden" name="full_otp" id="fullOtpInput">
        <button type="button" class="btn-main" style="height:38px; font-size:13px; margin-top:8px; background:#0284c7;" onclick="verifyOtpCode()">
          ✓ ওটিপি যাচাই করুন (Verify OTP)
        </button>
      </div>

      <div id="faceVerificationSection">
        <label style="font-size:13px; font-weight:800; color:#0f172a; display:block;">
          📸 লাইভ ফেস ম্যাচিং (1:1 AI Face Match with Database)
        </label>
        
        <div class="video-wrapper" id="videoWrapper">
          <video id="webcam" autoplay playsinline></video>
          <img id="capturedPhotoPreview" alt="Verified Face">
        </div>

        <div id="cameraStatus" style="font-size:12px; color:#64748b; margin-top:8px;">ওটিপি সফল হয়েছে! এবার ক্যামেরা চালু করুন।</div>
        
        <button type="button" id="startCamBtn" class="btn-main" style="background:#0284c7; height:38px; font-size:13px; margin-top:8px;" onclick="startRealFaceScan()">
          <i class="fa fa-camera"></i> ক্যামেরা অন ও ফেস স্ক্যান শুরু
        </button>
        
        <input type="hidden" name="face_verified" id="faceVerifiedFlag" value="0">
      </div>

      <button type="submit" name="secure_login" id="finalSubmitBtn" class="btn-main" style="background:#16a34a; margin-top:15px;" disabled>
        <i class="fa fa-lock"></i> 🔐 সুরক্ষিত লগইন / SECURE LOGIN
      </button>

      <div style="margin-top:15px; font-size:11px; color:#64748b; text-align:center;">
        ⚠️ ওটিপি ও মুখের বায়োমেট্রিক নিশ্চিত হওয়ার পর ব্যালট পেপার আনলক হবে।
      </div>
    </form>
  </div>
</div>

<script>
  let isModelLoaded = false;
  let isOtpValid = false;
  let scanInterval = null;
  let activeStream = null;

  async function requestLinkedOTP() {
    const voterId = document.getElementById('voterIdInput').value.trim();
    const mobile = document.getElementById('userMobile').value.trim();

    if(!voterId || mobile.length !== 10) {
      alert("সঠিক ভোটার আইডি এবং ১০ সংখ্যার লিঙ্কড মোবাইল নম্বর দিন।");
      return;
    }

    const formData = new FormData();
    formData.append('voter_id', voterId);
    formData.append('mobile', mobile);

    const res = await fetch('send_vote_otp.php', { method: 'POST', body: formData });
    const data = await res.json();

    if(data.status === 'success') {
      alert(`OTP Sent to +91 ${mobile} (Demo OTP: ${data.demo_otp})`);
      document.getElementById('otpInputArea').style.display = 'block';
    } else {
      alert(data.message);
    }
  }

  async function verifyOtpCode() {
    const otp = document.getElementById('fullOtpInput').value;
    const voterId = document.getElementById('voterIdInput').value.trim();

    if(otp.length !== 6) {
      alert("অনুগ্রহ করে ৬ সংখ্যার ওটিপি কোডটি লিখুন।");
      return;
    }

    const formData = new FormData();
    formData.append('otp', otp);
    formData.append('voter_id', voterId);

    const res = await fetch('verify_otp_ajax.php', { method: 'POST', body: formData });
    const data = await res.json();

    if(data.status === 'success') {
      isOtpValid = true;
      document.getElementById('otpInputArea').style.opacity = '0.7';
      document.getElementById('faceVerificationSection').style.display = 'block';
      alert("✅ ওটিপি সফল হয়েছে! এবার লাইভ ফেস ম্যাচিং সম্পন্ন করুন।");
    } else {
      alert("❌ ভুল ওটিপি কোড! আবার চেষ্টা করুন।");
    }
  }

  async function startRealFaceScan() {
    const status = document.getElementById('cameraStatus');
    const btn = document.getElementById('startCamBtn');
    const video = document.getElementById('webcam');
    const previewImg = document.getElementById('capturedPhotoPreview');
    const wrapper = document.getElementById('videoWrapper');
    const voterId = document.getElementById('voterIdInput').value.trim();

    status.innerHTML = '<span style="color:#0284c7;"><i class="fa fa-spinner fa-spin"></i> এআই মডেল ও ক্যামেরা চালু হচ্ছে...</span>';

    try {
      if(!isModelLoaded) {
        const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';
        await faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL);
        await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
        await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
        isModelLoaded = true;
      }

      activeStream = await navigator.mediaDevices.getUserMedia({ video: { width: 320, height: 240 } });
      video.srcObject = activeStream;
      video.style.display = 'block';
      previewImg.style.display = 'none';
      wrapper.style.display = 'block';
      btn.style.display = 'none';
      status.innerHTML = '<span style="color:#d97706;">ক্যামেরার দিকে স্থিরভাবে সোজা তাকান...</span>';

      video.addEventListener('play', () => {
        scanInterval = setInterval(async () => {
          const detection = await faceapi.detectSingleFace(video).withFaceLandmarks().withFaceDescriptor();

          if (detection && detection.descriptor) {
            status.innerHTML = '<span style="color:#0284c7;">মুখ শনাক্ত হয়েছে। সার্ভারে যাচাই হচ্ছে...</span>';

            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth || 320;
            canvas.height = video.videoHeight || 240;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            const liveImageBase64 = canvas.toDataURL('image/png');

            const postData = new FormData();
            postData.append('voter_id', voterId);
            postData.append('live_descriptor', JSON.stringify(Array.from(detection.descriptor)));
            postData.append('live_image', liveImageBase64);

            const verifyRes = await fetch('verify_face_backend.php', { method: 'POST', body: postData });
            const verifyJson = await verifyRes.json();

            if (verifyJson.status === 'success') {
              status.innerHTML = '<span style="color:#16a34a; font-weight:800;">✓ ফেস ভেরিফাইড (Face Match 100% Verified)</span>';
              document.getElementById('faceVerifiedFlag').value = "1";
              document.getElementById('finalSubmitBtn').disabled = false;
              
              clearInterval(scanInterval);

              previewImg.src = liveImageBase64;
              previewImg.style.display = 'block';
              video.style.display = 'none';

              if(activeStream) {
                activeStream.getTracks().forEach(track => track.stop());
              }
            } else {
              status.innerHTML = `<span style="color:#ef4444; font-weight:700;">❌ মুখ মিলেনি! (${verifyJson.message})</span>`;
              document.getElementById('faceVerifiedFlag').value = "0";
              document.getElementById('finalSubmitBtn').disabled = true;
            }
          } else {
            status.innerHTML = '<span style="color:#ef4444;">⚠️ কোনো মুখ পাওয়া যায়নি! ক্যামেরার সামনে আসুন।</span>';
          }
        }, 1000);
      });

    } catch (err) {
      status.innerHTML = '<span style="color:#ef4444;">ক্যামেরা বা এআই ইঞ্জিন লোড করতে ব্যর্থ হয়েছে!</span>';
    }
  }

  function validateFinalAuth() {
    if(!isOtpValid) {
      alert("❌ প্রথমে ওটিপি যাচাই করুন!");
      return false;
    }
    const isFaceOk = document.getElementById('faceVerifiedFlag').value;
    if (isFaceOk !== "1") {
      alert("❌ ফেস ভেরিফিকেশন সম্পন্ন হয়নি!");
      return false;
    }
    return true;
  }

  function moveOtp(current, index) {
    const digits = document.querySelectorAll('.otp-digit');
    if (current.value.length === 1 && index < digits.length) {
      digits[index].focus();
    }
    let combined = '';
    digits.forEach(d => combined += d.value);
    document.getElementById('fullOtpInput').value = combined;
  }
</script>
</body>
</html>