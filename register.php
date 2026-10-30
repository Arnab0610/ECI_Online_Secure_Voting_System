<?php
  session_start();
  if(isset($_SESSION['voter'])){
    header('location: home.php');
    exit();
  }
?>
<?php include 'includes/header.php'; ?>

<!-- FontAwesome 6 & Fonts -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Tiro+Bangla:ital@0;1&family=Share+Tech+Mono&display=swap" rel="stylesheet">
<!-- Face API AI Model Engine -->
<script defer src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.min.js"></script>

<style>
  :root {
    --eci-blue: #0b63c5;
    --eci-navy: #0f172a;
    --step-border: #e2e8f0;
  }
  * { box-sizing: border-box; font-family: 'Plus Jakarta Sans', 'Tiro Bangla', sans-serif; }
  body {
    background: #f0f4f9 url('images/vote-bg.png') no-repeat center center fixed;
    background-size: cover;
    min-height: 100vh;
    margin: 0;
    padding: 25px 15px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }
  .form6-container {
    width: 100%;
    max-width: 980px;
    background: #ffffff;
    border-radius: 18px;
    box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.22);
    overflow: hidden;
    border: 1px solid rgba(226, 232, 240, 0.9);
  }
  .form6-header {
    background: linear-gradient(135deg, #0b63c5, #0369a1);
    color: #ffffff;
    padding: 18px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 3.5px solid #ff9933;
  }
  .steps-progressbar {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    background: #f8fafc;
    border-bottom: 1px solid var(--step-border);
    padding: 14px 25px;
    gap: 10px;
  }
  .step-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    font-weight: 700;
    color: #94a3b8;
  }
  .step-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e2e8f0;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 800;
    flex-shrink: 0;
  }
  .step-item.active { color: var(--eci-blue); }
  .step-item.active .step-circle { background: var(--eci-blue); color: #ffffff; box-shadow: 0 0 10px rgba(11,99,197,0.35); }
  .step-item.completed .step-circle { background: #10b981; color: #ffffff; }

  .form6-body { padding: 30px 35px; }
  .form-section-title {
    font-size: 16.5px;
    font-weight: 800;
    color: var(--eci-navy);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 8px;
    border-bottom: 1.5px solid #f1f5f9;
  }
  .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 14px; }
  .form-group-custom { display: flex; flex-direction: column; gap: 5px; }
  .form-group-custom label { font-size: 12.5px; font-weight: 700; color: #334155; }
  .form-control-eci {
    width: 100%;
    height: 42px;
    border-radius: 8px;
    border: 1.5px solid #cbd5e1;
    padding: 7px 12px;
    font-size: 13.5px;
    outline: none;
    background: #ffffff;
  }
  .form-control-eci:focus { border-color: var(--eci-blue); box-shadow: 0 0 0 3px rgba(11,99,197,0.12); }

  /* Input With Keyboard Trigger */
  .input-with-kb {
    position: relative;
    display: flex;
    align-items: center;
  }
  .input-with-kb input {
    padding-right: 42px;
  }
  .kb-trigger-btn {
    position: absolute;
    right: 8px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    color: #475569;
    padding: 4px 7px;
    cursor: pointer;
    font-size: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
  }
  .kb-trigger-btn:hover {
    background: var(--eci-blue);
    color: #ffffff;
    border-color: var(--eci-blue);
  }
  
  .form-step-panel { display: none; }
  .form-step-panel.active-panel { display: block; animation: fadeIn 0.3s ease-in-out; }
  @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

  .form-footer-actions {
    margin-top: 25px;
    padding-top: 18px;
    border-top: 1px solid var(--step-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .btn-eci {
    padding: 10px 22px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: none;
    text-decoration: none;
  }
  .btn-eci-primary { background: var(--eci-blue); color: #ffffff; }
  .btn-eci-primary:hover { background: #094d99; }
  .btn-eci-secondary { background: #f1f5f9; color: #475569; }
  .btn-eci-success { background: #10b981; color: #ffffff; }

  .radio-group { display: flex; gap: 15px; align-items: center; margin-top: 4px; font-size: 13px; color: #334155; }
  .radio-group label { font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 4px; }
  .doc-upload-box { background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 14px; margin-bottom: 14px; }

  /* ================= DRAGGABLE ECI BENGALI KEYBOARD ================= */
  .eci-kb-modal {
    display: none;
    position: fixed;
    top: 55%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 650px;
    background: #0284c7;
    background: linear-gradient(135deg, #0284c7, #0369a1);
    border-radius: 12px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.5);
    z-index: 99999;
    padding: 6px;
    border: 1.5px solid #38bdf8;
    user-select: none;
  }
  .eci-kb-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #ffffff;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 700;
    cursor: move; /* Drag cursor */
  }
  .eci-kb-header i.drag-icon {
    margin-right: 6px;
    opacity: 0.8;
  }
  .eci-kb-close {
    background: #ef4444;
    border: none;
    color: #fff;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: bold;
  }
  .eci-kb-board {
    background: #0f172a;
    padding: 10px;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    gap: 5px;
  }
  .kb-row {
    display: flex;
    gap: 4px;
    justify-content: center;
  }
  .kb-btn {
    background: #1e293b;
    color: #ffffff;
    border: 1px solid #334155;
    border-radius: 5px;
    font-size: 14.5px;
    font-weight: 600;
    min-width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.12s;
  }
  .kb-btn:hover {
    background: #38bdf8;
    color: #0f172a;
  }
  .kb-btn:active {
    transform: scale(0.92);
  }
  .kb-btn-special {
    background: #334155;
    font-size: 11.5px;
    font-weight: 700;
    padding: 0 10px;
  }
  .kb-btn-active {
    background: #f59e0b !important;
    color: #000 !important;
  }

  @media (max-width: 768px) {
    .steps-progressbar { grid-template-columns: repeat(2, 1fr); }
    .grid-2 { grid-template-columns: 1fr; }
    .form6-body { padding: 20px 15px; }
    .eci-kb-modal { width: 96%; }
    .kb-btn { min-width: 28px; height: 32px; font-size: 12px; }
  }
</style>

<body>

<div class="form6-container">
  
  <!-- Header -->
  <div class="form6-header">
    <div style="display:flex; align-items:center; gap:12px;">
      <div style="background:#ffffff; width:40px; height:40px; border-radius:8px; display:flex; align-items:center; justify-content:center;">
        <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/Emblem_of_India.svg" style="height:30px;" alt="ECI">
      </div>
      <div>
        <h2 style="margin:0; font-size:18px; font-weight:800;">ভোটার সার্ভিস পোর্টাল • Form 6</h2>
        <p style="margin:2px 0 0; font-size:11px; opacity:0.9;">Application Form for New Voters (Election Commission of India)</p>
      </div>
    </div>
    <a href="user_login.php" class="btn-eci btn-eci-secondary" style="background:rgba(255,255,255,0.2); color:#fff; border:1px solid rgba(255,255,255,0.4);">
      <i class="fa fa-sign-in"></i> লগইন
    </a>
  </div>

  <!-- Progress Bar -->
  <div class="steps-progressbar">
    <div class="step-item active" id="stepIndicator1"><div class="step-circle">1</div><span>Location</span></div>
    <div class="step-item" id="stepIndicator2"><div class="step-circle">2</div><span>Personal Info</span></div>
    <div class="step-item" id="stepIndicator3"><div class="step-circle">3</div><span>Address & Docs</span></div>
    <div class="step-item" id="stepIndicator4"><div class="step-circle">4</div><span>Declaration</span></div>
  </div>

  <form action="register_action.php" method="POST" enctype="multipart/form-data" id="form6Wizard">
    <div class="form6-body">

      <!-- STEP 1: DYNAMIC CASCADING LOCATION -->
      <div class="form-step-panel active-panel" id="stepPanel1">
        <div class="form-section-title">📍 Select State, District & Assembly Constituency</div>
        
        <div class="grid-2">
          <div class="form-group-custom">
            <label>* Select State / UT:</label>
            <select name="state" id="stateSelect" class="form-control-eci" required onchange="onStateChange()">
              <option value="" disabled selected>-- Select State / UT --</option>
              <option value="West Bengal">West Bengal (পশ্চিমবঙ্গ)</option>
              <option value="Telangana">Telangana (তেলেঙ্গানা)</option>
              <option value="Tripura">Tripura (ত্রিপুরা)</option>
              <option value="Assam">Assam (আসাম)</option>
              <option value="Bihar">Bihar (বিহার)</option>
              <option value="Odisha">Odisha (ওড়িশা)</option>
            </select>
          </div>
          <div class="form-group-custom">
            <label>* Select District:</label>
            <select name="district" id="districtSelect" class="form-control-eci" required onchange="onDistrictChange()" disabled>
              <option value="" disabled selected>-- Select State First --</option>
            </select>
          </div>
        </div>

        <div class="form-group-custom">
          <label>* No. & Name of Assembly Constituency (AC):</label>
          <div style="display: flex; gap: 10px;">
            <input type="text" id="acNumberBox" placeholder="No." readonly style="width: 90px; height: 42px; border-radius: 8px; border: 1.5px solid #cbd5e1; background: #f1f5f9; text-align: center; font-weight: 700; color: #0b63c5;">
            <select name="constituency" id="acSelect" class="form-control-eci" required onchange="onAcChange()" disabled>
              <option value="" disabled selected>-- Select District First --</option>
            </select>
          </div>
        </div>

        <div class="form-footer-actions">
          <span></span>
          <button type="button" class="btn-eci btn-eci-primary" onclick="validateAndNext(1, 2)">Save & Next ➔</button>
        </div>
      </div>

      <!-- STEP 2: PERSONAL INFO WITH ON-SCREEN KEYBOARD -->
      <!-- STEP 2: PERSONAL INFO WITH ON-SCREEN KEYBOARD -->
      <div class="form-step-panel" id="stepPanel2">
        <div class="form-section-title">👤 Personal Information & Verification</div>
        <div class="grid-2">
          <div class="form-group-custom">
            <label>* First Name:</label>
            <input type="text" name="firstname" id="fn_en" class="form-control-eci" placeholder="e.g. Rahul" required oninput="autoBanglaTranslate('fn_en', 'fn_bn')">
          </div>
          <div class="form-group-custom">
            <label>* First Name (বাংলায় - Auto translated):</label>
            <div class="input-with-kb">
              <input type="text" name="firstname_bn" id="fn_bn" class="form-control-eci" placeholder="রাহুল" required>
              <button type="button" class="kb-trigger-btn" onclick="openBanglaKeyboard('fn_bn')" title="বাংলা কিবোর্ড খুলুন"><i class="fa fa-keyboard-o"></i></button>
            </div>
          </div>
        </div>
        <div class="grid-2">
          <div class="form-group-custom">
            <label>* Surname / Last Name:</label>
            <input type="text" name="lastname" id="ln_en" class="form-control-eci" placeholder="e.g. Das" required oninput="autoBanglaTranslate('ln_en', 'ln_bn')">
          </div>
          <div class="form-group-custom">
            <label>* Surname (বাংলায়):</label>
            <div class="input-with-kb">
              <input type="text" name="lastname_bn" id="ln_bn" class="form-control-eci" placeholder="দাস" required>
              <button type="button" class="kb-trigger-btn" onclick="openBanglaKeyboard('ln_bn')" title="বাংলা কিবোর্ড খুলুন"><i class="fa fa-keyboard-o"></i></button>
            </div>
          </div>
        </div>
        <div class="grid-2">
          <div class="form-group-custom">
            <label>* Gender:</label>
            <div class="radio-group">
              <label><input type="radio" name="gender" value="Male" required> Male</label>
              <label><input type="radio" name="gender" value="Female"> Female</label>
              <label><input type="radio" name="gender" value="Third Gender"> Third Gender</label>
            </div>
          </div>
          <div class="form-group-custom">
            <label>* Relative Type:</label>
            <div class="radio-group">
              <label><input type="radio" name="relative_type" value="Father" required> Father</label>
              <label><input type="radio" name="relative_type" value="Mother"> Mother</label>
              <label><input type="radio" name="relative_type" value="Husband"> Husband</label>
              <label><input type="radio" name="relative_type" value="Guardian"> Guardian</label>
            </div>
          </div>
        </div>
        
        <div class="grid-2">
          <div class="form-group-custom">
            <label>* Relative's Name:</label>
            <input type="text" name="relative_name" id="rel_en" class="form-control-eci" placeholder="e.g. Subhash Das" required oninput="autoBanglaTranslate('rel_en', 'rel_bn')">
          </div>
          <div class="form-group-custom">
            <label>* Relative's Name (বাংলায়):</label>
            <div class="input-with-kb">
              <input type="text" name="relative_name_bn" id="rel_bn" class="form-control-eci" placeholder="সুভাষ দাস" required>
              <button type="button" class="kb-trigger-btn" onclick="openBanglaKeyboard('rel_bn')" title="বাংলা কিবোর্ড খুলুন"><i class="fa fa-keyboard-o"></i></button>
            </div>
          </div>
        </div>
       
        <!-- মোবাইল নম্বর ও ওটিপি ভেরিফিকেশন -->
        <div class="grid-2">
          <div class="form-group-custom">
            <label>* Mobile Number (for OTP):</label>
            <div style="display:flex; gap:8px;">
              <input type="tel" name="mobile" id="mobInput" class="form-control-eci" placeholder="9876543210" required maxlength="10" pattern="[0-9]{10}">
              <button type="button" id="sendOtpBtn" class="btn-eci btn-eci-primary" style="white-space:nowrap;" onclick="sendRegistrationOtpLive()">📩 Send OTP</button>
            </div>
          </div>
          <div class="form-group-custom">
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <label style="margin:0;">* Enter 6-Digit OTP:</label>
              <span id="otpTimerDisplay" style="font-family:'Share Tech Mono', monospace; font-size:12px; font-weight:800; color:#dc2626; display:none;">⏱️ 01:00</span>
            </div>
            <div style="display:flex; gap:8px;">
              <input type="text" id="otpInput" class="form-control-eci" placeholder="6-digit OTP" maxlength="6" style="letter-spacing:4px; font-weight:700; text-align:center;" disabled>
              <button type="button" id="verifyOtpBtn" class="btn-eci btn-eci-success" style="white-space:nowrap;" onclick="verifyRegistrationOtpLive()" disabled>✓ Verify</button>
              <button type="button" id="resendOtpBtn" class="btn-eci" style="background:#d97706; color:#fff; white-space:nowrap; display:none;" onclick="sendRegistrationOtpLive()">🔄 Resend</button>
            </div>
            <div id="otpFeedbackText" style="font-size:11.5px; margin-top:4px; font-weight:700;"></div>
          </div>
        </div>
        <input type="hidden" id="isMobileVerified" value="0">

        <!-- ইমেল ঠিকানা ও ওটিপি ভেরিফিকেশন (পাশাপাশি সাজানো) -->
        <div class="grid-2">
          <div class="form-group-custom">
            <label>* Email Address (for OTP):</label>
            <div style="display:flex; gap:8px;">
              <input type="email" name="email" id="emailInput" class="form-control-eci" placeholder="name@example.com" required>
              <button type="button" id="sendEmailOtpBtn" class="btn-eci btn-eci-primary" style="white-space:nowrap;" onclick="sendEmailOtpLive()">📩 Send OTP</button>
            </div>
          </div>
          <div class="form-group-custom">
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <label style="margin:0;">* Enter Email 6-Digit OTP:</label>
            </div>
            <div style="display:flex; gap:8px;">
              <input type="text" id="emailOtpInput" class="form-control-eci" placeholder="6-digit OTP" maxlength="6" style="letter-spacing:4px; font-weight:700; text-align:center;" disabled>
              <button type="button" id="verifyEmailOtpBtn" class="btn-eci btn-eci-success" style="white-space:nowrap;" onclick="verifyEmailOtpLive()" disabled>✓ Verify</button>
            </div>
            <div id="emailOtpFeedbackText" style="font-size:11.5px; margin-top:4px; font-weight:700;"></div>
          </div>
        </div>
        <input type="hidden" id="isEmailVerified" value="0">

        <div class="form-group-custom" style="background:#f8fafc; border:1px solid #e2e8f0; padding:14px; border-radius:10px; margin-top:8px;">
          <label style="margin-bottom:6px;">* Aadhaar Details:</label>
          <div style="display:flex; flex-direction:column; gap:8px;">
            <label style="font-size:13px; font-weight:600; cursor:pointer;">
              <input type="radio" name="has_aadhaar" value="yes" id="aadhaarYes" required onchange="toggleAadhaarInput(true)"> I have Aadhaar Number
            </label>
            
            <div id="aadhaarInputContainer" style="display: none; margin-left: 22px; margin-top: 4px;">
              <input type="text" name="aadhaar_number" id="aadhaarNumberInput" class="form-control-eci" placeholder="Enter 12-digit Aadhaar Number" maxlength="12" pattern="[0-9]{12}" style="max-width: 320px;">
              <small style="color: #64748b; font-size: 11px; display: block; margin-top: 3px;">১২ সংখ্যার বৈধ আধার নম্বর লিখুন</small>
            </div>

            <label style="font-size:13px; font-weight:600; cursor:pointer;">
              <input type="radio" name="has_aadhaar" value="no" id="aadhaarNo" required onchange="toggleAadhaarInput(false)"> I do not have Aadhaar
            </label>
          </div>
        </div>

        <div class="form-footer-actions">
          <button type="button" class="btn-eci btn-eci-secondary" onclick="goToStep(1)">⬅ Previous</button>
          <button type="button" class="btn-eci btn-eci-primary" onclick="validateAndNext(2, 3)">Save & Next ➔</button>
        </div>
      </div>

      <!-- STEP 3: ADDRESS & DOCS -->
      <div class="form-step-panel" id="stepPanel3">
        <div class="form-section-title">🏠 Present Address & Supporting Documents</div>
        <div class="grid-2">
          <div class="form-group-custom">
            <label>* House / Door / Flat No:</label>
            <input type="text" name="house_no" class="form-control-eci" placeholder="e.g. 12/B" required>
          </div>
          <div class="form-group-custom">
            <label>* Street / Area / Locality:</label>
            <input type="text" name="street" class="form-control-eci" placeholder="e.g. M.G. Road" required>
          </div>
        </div>
        <div class="grid-2">
          <div class="form-group-custom">
            <label>* Village / Town / City:</label>
            <input type="text" name="city" class="form-control-eci" placeholder="e.g. Kolkata" required>
          </div>
          <div class="form-group-custom">
            <label>* Post Office:</label>
            <input type="text" name="post_office" class="form-control-eci" placeholder="e.g. Bowbazar" required>
          </div>
        </div>
        <div class="grid-2">
          <div class="form-group-custom">
            <label>* PIN Code:</label>
            <input type="text" name="pincode" class="form-control-eci" placeholder="e.g. 700012" maxlength="6" pattern="[0-9]{6}" required>
          </div>
          <div class="form-group-custom">
            <label>* Tehsil / Taluqa / Mandal:</label>
            <input type="text" name="tehsil" class="form-control-eci" placeholder="e.g. Kolkata Municipal Corp" required>
          </div>
        </div>

        <div class="form-group-custom" style="background:#f8fafc; border:1.5px dashed #0284c7; padding:15px; border-radius:10px; margin-bottom:15px; text-align:center;">

          <label style="font-weight:800; color:#0f172a; margin-bottom:6px; display:block;">
            📸 লাইভ ফেস বায়োমেট্রিক রেজিস্ট্রেশন (Mandatory for Online Voting)
          </label>
            <div id="regVideoWrapper" style="display:none; width:260px; height:195px; margin:8px auto; border-radius:8px; overflow:hidden; border:2px solid #0b63c5; background:#000;">
              <video id="regWebcam" autoplay playsinline style="width:100%; height:100%; object-fit:cover;"></video>
              <img id="regPhotoPreview" style="width:100%; height:100%; object-fit:cover; display:none;">
            </div>
            <div id="regCameraStatus" style="font-size:12px; color:#64748b;">অনলাইন ভোটদানের সুরক্ষায় আপনার ফেস স্ক্যান সম্পন্ন করুন।</div>
            <button type="button" id="startRegCamBtn" class="btn-eci btn-eci-primary" style="margin-top:8px;" onclick="initForm6FaceScan()">
              <i class="fa fa-camera"></i> ক্যামেরা অন ও ফেস ক্যাপচার
            </button>
            <input type="hidden" name="face_descriptor" id="form6FaceDescriptor">
        </div>

        
        <div class="doc-upload-box">
          <div style="font-weight:700; font-size:13px; color:var(--eci-navy); margin-bottom:10px;"><i class="fa fa-folder-open"></i> Document Upload Section (Max size: 2MB | Formats: JPG, PNG, PDF)</div>
          <div class="form-group-custom" style="margin-bottom:12px;">
            <label>* Applicant's Passport Photo:</label>
            <input type="file" name="photo" class="form-control-eci" accept="image/*" required>
          </div>
          <div class="grid-2">
            <div class="form-group-custom">
              <label>* Proof of Date of Birth:</label>
              <select name="dob_doc_type" class="form-control-eci" required>
                <option value="" disabled selected>-- Select DOB Document --</option>
                <option value="Aadhaar Card">Aadhaar Card[cite: 1]</option>
                <option value="Birth Certificate">Birth Certificate</option>
                <option value="PAN Card">PAN Card</option>
                <option value="Passport">Passport</option>
              </select>
            </div>
            <div class="form-group-custom">
              <label>* Upload DOB Document:</label>
              <input type="file" name="dob_doc_file" class="form-control-eci" accept="image/*,application/pdf" required>
            </div>
          </div>
          <div class="grid-2">
            <div class="form-group-custom">
              <label>* Proof of Address:</label>
              <select name="address_doc_type" class="form-control-eci" required>
                <option value="" disabled selected>-- Select Address Proof --</option>
                <option value="Electricity Bill">Electricity Bill</option>
                <option value="Ration Card">Ration Card</option>
                <option value="Bank Passbook">Bank Passbook</option>
              </select>
            </div>
            <div class="form-group-custom">
              <label>* Upload Address Proof:</label>
              <input type="file" name="nid_photo" class="form-control-eci" accept="image/*,application/pdf" required>
            </div>
          </div>
        </div>
        <div class="form-footer-actions">
          <button type="button" class="btn-eci btn-eci-secondary" onclick="goToStep(2)">⬅ Previous</button>
          <button type="button" class="btn-eci btn-eci-primary" onclick="validateAndNext(3, 4)">Save & Next ➔</button>
        </div>
      </div>

      <!-- STEP 4: DECLARATION -->
      <div class="form-step-panel" id="stepPanel4">
        <div class="form-section-title">📝 Final Declaration & Submission</div>
        <div style="background:#f8fafc; border:1px solid #e2e8f0; padding:15px; border-radius:10px; font-size:12.5px; line-height:1.7; color:#334155; margin-bottom:15px;">
          <div style="margin-bottom:8px;">
            * I am a citizen of India and currently residing at the address given above since:
            <div style="display:inline-flex; gap:8px; margin-left:8px;">
              <select name="residing_month" class="form-control-eci" style="width:auto; height:34px; padding:3px 8px;" required>
                <option value="" disabled selected>Month</option>
                <option value="January">January</option>
                <option value="February">February</option>
                <option value="March">March</option>
                <option value="April">April</option>
                <option value="May">May</option>
                <option value="June">June</option>
                <option value="July">July</option>
                <option value="August">August</option>
                <option value="September">September</option>
                <option value="October">October</option>
                <option value="November">November</option>
                <option value="December">December</option>
              </select>
              <select name="residing_year" class="form-control-eci" style="width:auto; height:34px; padding:3px 8px;" required>
                <option value="" disabled selected>Year</option>
                <option value="2018">2018</option>
                <option value="2019">2019</option>
                <option value="2020">2020</option>
                <option value="2021">2021</option>
                <option value="2022">2022</option>
                <option value="2023">2023</option>
                <option value="2024">2024</option>
                <option value="2025">2025</option>
                <option value="2026">2026</option>
              </select>
            </div>
          </div>
          <div><label><input type="checkbox" required> I declare that to the best of my knowledge and belief, all statements are true.</label></div>
          <div><label><input type="checkbox" required> I have not applied for registration in any other constituency.</label></div>
        </div>
        <div class="grid-2">
          <div class="form-group-custom">
            <label>* Place:</label>
            <input type="text" name="declaration_place" class="form-control-eci" placeholder="e.g. Kolkata" required>
          </div>
          <div class="form-group-custom">
            <label>* Date:</label>
            <input type="text" name="declaration_date" class="form-control-eci" value="<?php date_default_timezone_set('Asia/Kolkata'); echo date('d-m-Y'); ?>" readonly style="background:#f1f5f9; font-weight: 700; color: #0f172a;">
          </div>
        </div>
        <div class="grid-2" style="margin-top:8px;">
          <div class="form-group-custom">
            <label>🔐 Security Captcha:</label>
            <div style="display:flex; align-items:center; gap:8px;">
              <div id="captchaDisplayBox" style="font-family:'Share Tech Mono', monospace; font-size:19px; font-weight:800; background:#e0f2fe; color:#0369a1; padding:6px 14px; border-radius:6px; letter-spacing:4px; border:1px solid #bae6fd; user-select:none;">
                <?php
                  if(!isset($_SESSION['reg_captcha'])){
                    $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
                    $init_c = '';
                    for($i=0; $i<5; $i++) { $init_c .= $chars[rand(0, strlen($chars)-1)]; }
                    $_SESSION['reg_captcha'] = $init_c;
                  }
                  echo $_SESSION['reg_captcha'];
                ?>
              </div>
              <button type="button" class="btn-eci btn-eci-secondary" style="padding:7px 10px;" onclick="refreshLiveCaptcha()" title="নতুন ক্যাপচা আনুন">🔄</button>
            </div>
          </div>
          <div class="form-group-custom">
            <label>* Enter Captcha Code:</label>
            <input type="text" name="captcha" id="captchaInput" class="form-control-eci" placeholder="Enter Captcha" required maxlength="5" style="text-transform:uppercase; letter-spacing:2px; font-weight:700;">
          </div>
        </div>
        <div class="form-footer-actions">
          <button type="button" class="btn-eci btn-eci-secondary" onclick="goToStep(3)">⬅ Previous</button>
          <button type="button" class="btn-eci btn-eci-secondary" onclick="alert('Draft saved successfully!')">💾 Save Draft</button>
          <!-- সরাসরি সাবমিট না হয়ে প্রিভিউ মডাল ওপেন হবে -->
          <button type="button" class="btn-eci btn-eci-success" style="padding:11px 26px; font-size:14px;" onclick="openFormPreview()">
            👁 Preview & Submit
          </button>
        </div>
      </div>

    </div>
  </form>

  <!-- ================= ECI FORM PREVIEW MODAL ================= -->
<div id="previewModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15,23,42,0.7); z-index:999999; overflow-y:auto; padding:20px 10px;">
  <div style="max-width:750px; background:#fff; margin:30px auto; border-radius:14px; box-shadow:0 25px 50px rgba(0,0,0,0.25); overflow:hidden; border:2px solid #0b63c5;">
    
    <div style="background:linear-gradient(135deg, #0b63c5, #0369a1); color:#fff; padding:16px 22px; display:flex; justify-content:space-between; align-items:center;">
      <h3 style="margin:0; font-size:17px; font-weight:800;">📋 আবেদনপত্র প্রিভিউ (Form 6 Preview)</h3>
      <button type="button" onclick="closeFormPreview()" style="background:none; border:none; color:#fff; font-size:20px; cursor:pointer;">✕</button>
    </div>

    <div style="padding:22px; max-height:70vh; overflow-y:auto; font-size:13.5px; line-height:1.8; color:#1e293b;" id="previewContent">
      <!-- জাভাস্ক্রিপ্ট দিয়ে ডেটা ঢুকবে -->
       
    </div>

    <div style="background:#f8fafc; padding:14px 22px; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:12px;">
      <button type="button" onclick="closeFormPreview()" class="btn-eci btn-eci-secondary" style="background:#e2e8f0; color:#334155;">✏️ এডিট করুন</button>
      <button type="button" onclick="confirmAndSubmitForm()" class="btn-eci btn-eci-success" style="padding:10px 24px;">✅ নিশ্চিত ও সাবমিট করুন</button>
    </div>

  </div>
</div>

</div>

<!-- ================= DRAGGABLE ECI BENGALI VIRTUAL KEYBOARD ================= -->
<div class="eci-kb-modal" id="bengaliKeyboardModal">
  <div class="eci-kb-header" id="bengaliKbHeader">
    <span><i class="fa fa-arrows drag-icon"></i> Enhanced Inscript Keyboard (BIS - IS 16350) - বাংলা</span>
    <button type="button" class="eci-kb-close" onclick="closeBanglaKeyboard()">✕</button>
  </div>
  
  <div class="eci-kb-board">
    <!-- ROW 1: Numbers & Symbols -->
    <div class="kb-row" id="kbRow1">
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">১</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">২</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">৩</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">৪</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">৫</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">৬</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">৭</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">৮</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">৯</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">০</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">-</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">ৃ</button>
      <button type="button" class="kb-btn kb-btn-special" style="min-width:60px;" onclick="backspaceKb()">Bksp ⌫</button>
    </div>

    <!-- ROW 2: Vowels & Top Consonants -->
    <div class="kb-row" id="kbRow2">
      <button type="button" class="kb-btn kb-btn-special" style="min-width:45px;" onclick="typeKbChar('\t')">Tab</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">ৌ</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">ৈ</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">া</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">ী</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">ূ</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">ব</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">হ</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">গ</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">দ</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">জ</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">ড</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">়</button>
    </div>

    <!-- ROW 3: Middle Consonants -->
    <div class="kb-row" id="kbRow3">
      <button type="button" class="kb-btn kb-btn-special" id="capsBtn" style="min-width:50px;" onclick="toggleKbCaps()">Caps</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">ো</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">ে</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">্</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">ি</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">ু</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">প</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">র</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">ক</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">ত</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">চ</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">ট</button>
      <button type="button" class="kb-btn kb-btn-special" style="min-width:55px; background:#0b63c5;" onclick="closeBanglaKeyboard()">Enter ↵</button>
    </div>

    <!-- ROW 4: Lower Consonants & Shift -->
    <div class="kb-row" id="kbRow4">
      <button type="button" class="kb-btn kb-btn-special" id="shiftBtn" style="min-width:60px;" onclick="toggleKbShift()">Shift ⇧</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">ং</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">ম</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">ন</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">ভ</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">ল</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">স</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">শ</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">ষ</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">য</button>
      <button type="button" class="kb-btn" onclick="typeKbChar(this.innerText)">য়</button>
      <button type="button" class="kb-btn kb-btn-special" style="min-width:60px;" onclick="toggleKbShift()">Shift ⇧</button>
    </div>

    <!-- ROW 5: Space & Done -->
    <div class="kb-row">
      <button type="button" class="kb-btn kb-btn-special" onclick="typeKbChar('ৎ')">ৎ</button>
      <button type="button" class="kb-btn kb-btn-special" onclick="typeKbChar('ঁ')">ঁ</button>
      <button type="button" class="kb-btn kb-btn-special" onclick="typeKbChar('ঃ')">ঃ</button>
      <button type="button" class="kb-btn kb-btn-special" style="flex-grow:1;" onclick="typeKbChar(' ')">Space (ফাঁকা স্থান)</button>
      <button type="button" class="kb-btn kb-btn-special" style="background:#10b981; padding:0 20px;" onclick="closeBanglaKeyboard()">Done ✓</button>
    </div>
  </div>
</div>

<script>
  // ECI State -> District -> Assembly Constituency Data
  const eciLocationData = {
    "West Bengal": {
      "Kolkata South": [
        { no: "158", name: "Kolkata Port" },
        { no: "159", name: "Bhabanipur" },
        { no: "160", name: "Rashbehari" },
        { no: "161", name: "Ballygunge" }
      ],
      "Kolkata North": [
        { no: "162", name: "Chowrangee" },
        { no: "163", name: "Entally" },
        { no: "164", name: "Beliaghata" },
        { no: "165", name: "Jorasanko" },
        { no: "166", name: "Shyampukur" },
        { no: "167", name: "Maniktala" }
      ],
      "South 24 Parganas": [
        { no: "133", name: "Kulpi" },
        { no: "134", name: "Raidighi" },
        { no: "135", name: "Mandirbazar" },
        { no: "136", name: "Jaynagar" },
        { no: "137", name: "Baruipur Purba" },
        { no: "138", name: "Canning Paschim" },
        { no: "139", name: "Canning Purba" },
        { no: "140", name: "Baruipur Paschim" },
        { no: "141", name: "Magrahat Purba" },
        { no: "142", name: "Magrahat Paschim" },
        { no: "143", name: "Diamond Harbour" },
        { no: "144", name: "Falta" },
        { no: "145", name: "Satgachia" },
        { no: "146", name: "Bishnupur" },
        { no: "147", name: "Sonarpur Dakshin" },
        { no: "148", name: "Bhangar" },
        { no: "149", name: "Kasba" },
        { no: "150", name: "Jadavpur" },
        { no: "151", name: "Sonarpur Uttar" },
        { no: "152", name: "Tollygunge" },
        { no: "153", name: "Behala Purba" },
        { no: "154", name: "Behala Paschim" },
        { no: "155", name: "Maheshtala" },
        { no: "156", name: "Budge Budge" },
        { no: "157", name: "Metiaburuz" }
      ],
      "North 24 Parganas": [
        { no: "100", name: "Habra" },
        { no: "101", name: "Ashoknagar" },
        { no: "115", name: "Rajarhat New Town" },
        { no: "116", name: "Bidhannagar" },
        { no: "121", name: "Haroa" },
        { no: "124", name: "Basirhat Dakshin" },
        { no: "125", name: "Basirhat Uttar" }
      ],
      "Howrah": [
        { no: "169", name: "Howrah Madhya" },
        { no: "170", name: "Howrah Uttar" },
        { no: "171", name: "Howrah Dakshin" },
        { no: "172", name: "Shibpur" },
        { no: "173", name: "Bally" }
      ],
      "Hooghly": [
        { no: "185", name: "Uttarpara" },
        { no: "186", name: "Sreerampur" },
        { no: "187", name: "Champdani" },
        { no: "188", name: "Singur" },
        { no: "189", name: "Chandan Nagar" },
        { no: "190", name: "Chunchura" }
      ]
    },
    "Telangana": {
      "Hyderabad": [
        { no: "57", name: "Musheerabad" },
        { no: "58", name: "Malakpet" },
        { no: "59", name: "Amberpet" },
        { no: "60", name: "Khairatabad" },
        { no: "61", name: "Jubilee Hills" },
        { no: "62", name: "Sanathnagar" },
        { no: "63", name: "Nampally" },
        { no: "64", name: "Karwan" },
        { no: "65", name: "Goshamahal" },
        { no: "66", name: "Charminar" },
        { no: "67", name: "Chandrayangutta" },
        { no: "68", name: "Yakutpura" },
        { no: "69", name: "Bahadurpura" },
        { no: "70", name: "Secunderabad" },
        { no: "71", name: "Secunderabad Cantt." }
      ],
      "Vikarabad": [
        { no: "54", name: "Pargi" },
        { no: "55", name: "Vikarabad" },
        { no: "56", name: "Tandur" }
      ]
    }
  };

  function onStateChange() {
    const state = document.getElementById('stateSelect').value;
    const districtSelect = document.getElementById('districtSelect');
    const acSelect = document.getElementById('acSelect');
    const acNumberBox = document.getElementById('acNumberBox');

    districtSelect.innerHTML = '<option value="" disabled selected>-- Select District --</option>';
    acSelect.innerHTML = '<option value="" disabled selected>-- Select District First --</option>';
    acSelect.disabled = true;
    acNumberBox.value = '';

    if (state && eciLocationData[state]) {
      districtSelect.disabled = false;
      Object.keys(eciLocationData[state]).forEach(dist => {
        const opt = document.createElement('option');
        opt.value = dist;
        opt.textContent = dist;
        districtSelect.appendChild(opt);
      });
    } else {
      districtSelect.disabled = true;
    }
  }

  function onDistrictChange() {
    const state = document.getElementById('stateSelect').value;
    const dist = document.getElementById('districtSelect').value;
    const acSelect = document.getElementById('acSelect');
    const acNumberBox = document.getElementById('acNumberBox');

    acSelect.innerHTML = '<option value="" disabled selected>-- Select Assembly Constituency --</option>';
    acNumberBox.value = '';

    if (state && dist && eciLocationData[state][dist]) {
      acSelect.disabled = false;
      eciLocationData[state][dist].forEach(ac => {
        const opt = document.createElement('option');
        opt.value = `${ac.no} - ${ac.name}`;
        opt.dataset.acNo = ac.no;
        opt.textContent = `${ac.no} - ${ac.name}`;
        acSelect.appendChild(opt);
      });
    } else {
      acSelect.disabled = true;
    }
  }

  function onAcChange() {
    const acSelect = document.getElementById('acSelect');
    const selectedOption = acSelect.options[acSelect.selectedIndex];
    document.getElementById('acNumberBox').value = (selectedOption && selectedOption.dataset.acNo) ? selectedOption.dataset.acNo : '';
  }

  // Google Free Phonetic Transliteration API
  let translationTimeout = null;
  function autoBanglaTranslate(sourceId, targetId) {
    const rawVal = document.getElementById(sourceId).value.trim();
    const targetInput = document.getElementById(targetId);

    if (!rawVal) {
      targetInput.value = '';
      return;
    }

    clearTimeout(translationTimeout);
    translationTimeout = setTimeout(() => {
      const url = `https://inputtools.google.com/request?text=${encodeURIComponent(rawVal)}&itc=bn-t-i0-und&num=1&cp=0&cs=1&ie=utf-8&oe=utf-8`;

      fetch(url)
        .then(response => response.json())
        .then(data => {
          if (data[0] === 'SUCCESS' && data[1] && data[1][0] && data[1][0][1]) {
            targetInput.value = data[1][0][1][0];
          }
        })
        .catch(err => console.warn('Auto translation fallback:', err));
    }, 300);
  }

  // ================= VIRTUAL KEYBOARD CORE ENGINE =================
  let activeKbTargetInputId = null;
  let isShiftActive = false;
  let isCapsActive = false;

  const normalLayout = {
    row1: ['১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯', '০', '-', 'ৃ'],
    row2: ['ৌ', 'ৈ', 'া', 'ী', 'ূ', 'ব', 'হ', 'গ', 'দ', 'জ', 'ড', '়'],
    row3: ['ো', 'ে', '্', 'ি', 'ু', 'প', 'র', 'ক', 'ত', 'চ', 'ট'],
    row4: ['ং', 'ম', 'ন', 'ভ', 'ল', 'স', 'শ', 'ষ', 'য', 'য়']
  };

  const shiftLayout = {
    row1: ['ৠ', 'ৡ', 'ঽ', '!', '@', '#', '$', '%', '^', '&', '*', 'ঋ'],
    row2: ['ঔ', 'ঐ', 'আ', 'ঈ', 'ঊ', 'ভ', 'ঙ', 'ঘ', 'ধ', 'ঝ', 'ঢ', 'ঞ'],
    row3: ['ও', 'এ', 'অ', 'ই', 'উ', 'ফ', 'ড়', 'খ', 'থ', 'ছ', 'ঠ'],
    row4: ['ঁ', 'ণ', 'ন', 'ঢ়', 'ল', 'স', 'শ', 'ষ', 'য়', 'ৎ']
  };

  function renderKeyboard(layout) {
    const r1Btns = document.querySelectorAll('#kbRow1 .kb-btn:not(.kb-btn-special)');
    layout.row1.forEach((char, idx) => { if(r1Btns[idx]) r1Btns[idx].innerText = char; });

    const r2Btns = document.querySelectorAll('#kbRow2 .kb-btn:not(.kb-btn-special)');
    layout.row2.forEach((char, idx) => { if(r2Btns[idx]) r2Btns[idx].innerText = char; });

    const r3Btns = document.querySelectorAll('#kbRow3 .kb-btn:not(.kb-btn-special)');
    layout.row3.forEach((char, idx) => { if(r3Btns[idx]) r3Btns[idx].innerText = char; });

    const r4Btns = document.querySelectorAll('#kbRow4 .kb-btn:not(.kb-btn-special)');
    layout.row4.forEach((char, idx) => { if(r4Btns[idx]) r4Btns[idx].innerText = char; });
  }

  function toggleKbShift() {
    isShiftActive = !isShiftActive;
    document.querySelectorAll('#shiftBtn').forEach(b => b.classList.toggle('kb-btn-active', isShiftActive));
    renderKeyboard((isShiftActive || isCapsActive) ? shiftLayout : normalLayout);
  }

  function toggleKbCaps() {
    isCapsActive = !isCapsActive;
    document.getElementById('capsBtn').classList.toggle('kb-btn-active', isCapsActive);
    renderKeyboard((isShiftActive || isCapsActive) ? shiftLayout : normalLayout);
  }

  function openBanglaKeyboard(targetInputId) {
    activeKbTargetInputId = targetInputId;
    isShiftActive = false;
    renderKeyboard(isCapsActive ? shiftLayout : normalLayout);
    document.getElementById('bengaliKeyboardModal').style.display = 'block';
  }

  function closeBanglaKeyboard() {
    document.getElementById('bengaliKeyboardModal').style.display = 'none';
    if(activeKbTargetInputId) {
      document.getElementById(activeKbTargetInputId).focus();
    }
  }

  function typeKbChar(char) {
    if (!activeKbTargetInputId) return;
    const input = document.getElementById(activeKbTargetInputId);
    input.value += char;
    if (isShiftActive && !isCapsActive) {
      toggleKbShift();
    }
  }

  function backspaceKb() {
    if (!activeKbTargetInputId) return;
    const input = document.getElementById(activeKbTargetInputId);
    input.value = input.value.slice(0, -1);
  }

  // ================= DRAG & MOVE SYSTEM =================
  dragElement(document.getElementById("bengaliKeyboardModal"));

  function dragElement(elmnt) {
    var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
    const header = document.getElementById("bengaliKbHeader");
    if (header) {
      header.onmousedown = dragMouseDown;
    }

    function dragMouseDown(e) {
      e = e || window.event;
      e.preventDefault();
      pos3 = e.clientX;
      pos4 = e.clientY;
      document.onmouseup = closeDragElement;
      document.onmousemove = elementDrag;
    }

    function elementDrag(e) {
      e = e || window.event;
      e.preventDefault();
      pos1 = pos3 - e.clientX;
      pos2 = pos4 - e.clientY;
      pos3 = e.clientX;
      pos4 = e.clientY;
      elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
      elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
    }

    function closeDragElement() {
      document.onmouseup = null;
      document.onmousemove = null;
    }
  }

  function toggleAadhaarInput(hasAadhaar) {
    const container = document.getElementById('aadhaarInputContainer');
    const input = document.getElementById('aadhaarNumberInput');

    if (hasAadhaar) {
      container.style.display = 'block';
      input.setAttribute('required', 'required');
      input.focus();
    } else {
      container.style.display = 'none';
      input.removeAttribute('required');
      input.value = '';
    }
  }

  let regOtpTimer = null;
  let regTimeLeft = 60;

  // মোবাইল ওটিপি পাঠানোর ফাংশন
  async function sendRegistrationOtpLive() {
    const mob = document.getElementById('mobInput').value.trim();
    const sendBtn = document.getElementById('sendOtpBtn');
    const resendBtn = document.getElementById('resendOtpBtn');
    const otpInput = document.getElementById('otpInput');
    const verifyBtn = document.getElementById('verifyOtpBtn');
    const feedback = document.getElementById('otpFeedbackText');

    if (mob.length !== 10 || isNaN(mob)) {
      alert('অনুগ্রহ করে সঠিক ১০ সংখ্যার মোবাইল নম্বর দিন।');
      document.getElementById('mobInput').focus();
      return;
    }

    sendBtn.disabled = true;
    sendBtn.innerText = 'পাঠানো হচ্ছে...';
    resendBtn.style.display = 'none';

    try {
    const response = await fetch('send_register_otp.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'mobile=' + encodeURIComponent(mob)
});
      
      const data = await response.json();

      if (data.status === 'success') {
        alert(data.message + (data.demo_otp ? ` (Demo OTP: ${data.demo_otp})` : ''));
        otpInput.disabled = false;
        otpInput.value = '';
        otpInput.focus();
        verifyBtn.disabled = false;
        sendBtn.style.display = 'none';
        
        feedback.style.color = '#0284c7';
        feedback.innerText = 'ফোনে ওটিপি পাঠানো হয়েছে। ১ মিনিটের মধ্যে যাচাই করুন।';
      } else {
        alert(data.message);
        sendBtn.disabled = false;
        sendBtn.innerText = '📩 Send OTP';
      }
    } catch(err) {
      console.error('Fetch error:', err);
      alert('সার্ভারের সাথে সংযোগ স্থাপন করা যায়নি!');
      sendBtn.disabled = false;
      sendBtn.innerText = '📩 Send OTP';
    }
  }

  // মোবাইল ওটিপি ভেরিফাই ফাংশন
  async function verifyRegistrationOtpLive() {
    const otp = document.getElementById('otpInput').value.trim();
    const feedback = document.getElementById('otpFeedbackText');

    if (otp.length !== 6) {
      alert('অনুগ্রহ করে ৬ সংখ্যার সম্পূর্ণ ওটিপি লিখুন।');
      return;
    }

    const formData = new FormData();
    formData.append('otp', otp);

    try {
      // এখানে ফাইল নাম পরিবর্তন করে verify_register_otp.php দিতে হবে:
      const response = await fetch('verify_register_otp.php', { method: 'POST', body: formData });
      const data = await response.json();

      if (data.status === 'success') {
        alert(data.message);
        document.getElementById('isMobileVerified').value = "1";
        document.getElementById('mobInput').readOnly = true;
        document.getElementById('otpInput').disabled = true;
        document.getElementById('verifyOtpBtn').style.display = 'none';
        
        feedback.style.color = '#16a34a';
        feedback.innerHTML = '✓ মোবাইল নম্বর সফলভাবে ভেরিফাই হয়েছে';
      } else {
        alert(data.message);
      }
    } catch(err) {
      alert('ওটিপি যাচাই করতে সমস্যা হয়েছে!');
    }
  }

  // ইমেল ওটিপি পাঠানোর ফাংশন
  // ইমেল ওটিপি পাঠানোর সঠিক ফাংশন
  async function sendEmailOtpLive() {
    const email = document.getElementById('emailInput').value.trim();
    const sendBtn = document.getElementById('sendEmailOtpBtn');
    const otpInput = document.getElementById('emailOtpInput');
    const verifyBtn = document.getElementById('verifyEmailOtpBtn');
    const feedback = document.getElementById('emailOtpFeedbackText');

    if (!email || !email.includes('@')) {
      alert('অনুগ্রহ করে সঠিক ইমেল ঠিকানা দিন।');
      document.getElementById('emailInput').focus();
      return;
    }

    sendBtn.disabled = true;
    sendBtn.innerText = 'পাঠানো হচ্ছে...';

    try {
      // এখানে ফাইল নাম পরিবর্তন করে send_email_otp.php দিতে হবে:
      const response = await fetch('send_email_otp.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'email=' + encodeURIComponent(email)
      });
      
      const data = await response.json();

      if (data.status === 'success') {
        alert(data.message + (data.demo_otp ? ` (Demo OTP: ${data.demo_otp})` : ''));
        otpInput.disabled = false;
        otpInput.value = '';
        otpInput.focus();
        verifyBtn.disabled = false;
        sendBtn.style.display = 'none';
        feedback.style.color = '#0284c7';
        feedback.innerText = 'ইমেইলে ওটিপি পাঠানো হয়েছে।';
      } else {
        alert(data.message);
        sendBtn.disabled = false;
        sendBtn.innerText = '📩 Send Email OTP';
      }
    } catch(err) {
      console.error('Email OTP Error:', err);
      alert('ইমেল সার্ভারের সাথে সংযোগ স্থাপন করা যায়নি!');
      sendBtn.disabled = false;
      sendBtn.innerText = '📩 Send Email OTP';
    }
  }

  // ইমেল ওটিপি ভেরিফাই ফাংশন
  async function verifyEmailOtpLive() {
    const otp = document.getElementById('emailOtpInput').value.trim();
    const feedback = document.getElementById('emailOtpFeedbackText');

    if (otp.length !== 6) {
      alert('অনুগ্রহ করে ৬ সংখ্যার সম্পূর্ণ ওটিপি লিখুন।');
      return;
    }

    const formData = new FormData();
    formData.append('otp', otp);

    try {
      // এখানে ফাইল নাম পরিবর্তন করে verify_email_otp.php দিতে হবে:
      const response = await fetch('verify_email_otp.php', { method: 'POST', body: formData });
      const data = await response.json();

      if (data.status === 'success') {
        alert(data.message);
        document.getElementById('isEmailVerified').value = "1";
        document.getElementById('emailInput').readOnly = true;
        document.getElementById('emailOtpInput').disabled = true;
        document.getElementById('verifyEmailOtpBtn').style.display = 'none';
        feedback.style.color = '#16a34a';
        feedback.innerHTML = '✓ ইমেল সফলভাবে ভেরিফাই হয়েছে';
      } else {
        alert(data.message);
      }
    } catch(err) {
      alert('ওটিপি যাচাই করতে সমস্যা হয়েছে!');
    }
  }

  function validateAndNext(currentStep, targetStep) {
    const currentPanel = document.getElementById('stepPanel' + currentStep);
    const inputs = currentPanel.querySelectorAll('input, select, textarea');
    let isValid = true;

    for (let input of inputs) {
      if (input.offsetParent === null) continue;

      if (!input.checkValidity()) {
        input.reportValidity();
        isValid = false;
        break;
      }
    }

    if (isValid && currentStep === 2) {
      const isMobileVerified = document.getElementById('isMobileVerified').value;
      const isEmailVerified = document.getElementById('isEmailVerified').value;
      
      if (isMobileVerified !== "1" || isEmailVerified !== "1") {
        alert('❌ পরবর্তী ধাপে যাওয়ার আগে মোবাইল এবং ইমেল উভয়ই ওটিপি দিয়ে ভেরিফাই করা বাধ্যতামূলক!');
        document.getElementById('mobInput').focus();
        return;
      }
    }

    if (isValid) goToStep(targetStep);
  }

  function goToStep(stepNumber) {
    for (let i = 1; i <= 4; i++) {
      document.getElementById('stepPanel' + i).classList.remove('active-panel');
      document.getElementById('stepIndicator' + i).classList.remove('active');
    }
    for (let i = 1; i < stepNumber; i++) {
      document.getElementById('stepIndicator' + i).classList.add('completed');
    }
    document.getElementById('stepPanel' + stepNumber).classList.add('active-panel');
    document.getElementById('stepIndicator' + stepNumber).classList.add('active');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  let isRegModelLoaded = false;
  let regStream = null;
  let regInterval = null;

  async function initForm6FaceScan() {
    const video = document.getElementById('regWebcam');
    const previewImg = document.getElementById('regPhotoPreview');
    const wrapper = document.getElementById('regVideoWrapper');
    const status = document.getElementById('regCameraStatus');
    const btn = document.getElementById('startRegCamBtn');
    const descriptorInput = document.getElementById('form6FaceDescriptor');

    btn.disabled = true;
    status.innerHTML = '<span style="color:#0284c7;"><i class="fa fa-spinner fa-spin"></i> এআই মডেল ও ক্যামেরা চালু হচ্ছে...</span>';

    try {
      if (!isRegModelLoaded) {
        const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';
        await faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL);
        await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
        await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
        isRegModelLoaded = true;
      }

      regStream = await navigator.mediaDevices.getUserMedia({ video: { width: 320, height: 240 } });
      video.srcObject = regStream;
      video.style.display = 'block';
      previewImg.style.display = 'none';
      wrapper.style.display = 'block';
      status.innerHTML = '<span style="color:#d97706;">ক্যামেরার দিকে সোজা তাকান, মুখ শনাক্ত করা হচ্ছে...</span>';

      video.addEventListener('play', () => {
        regInterval = setInterval(async () => {
          const detection = await faceapi.detectSingleFace(video).withFaceLandmarks().withFaceDescriptor();

          if (detection && detection.descriptor) {
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth || 320;
            canvas.height = video.videoHeight || 240;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            const liveImgBase64 = canvas.toDataURL('image/png');

            descriptorInput.value = JSON.stringify(Array.from(detection.descriptor));

            clearInterval(regInterval);
            if (regStream) {
              regStream.getTracks().forEach(track => track.stop());
            }

            video.style.display = 'none';
            previewImg.src = liveImgBase64;
            previewImg.style.display = 'block';

            status.innerHTML = '<span style="color:#10b981; font-weight:800;">✓ বায়োমেট্রিক ফেস সফলভাবে ক্যাপচার হয়েছে!</span>';
            btn.style.display = 'none';
          } else {
            status.innerHTML = '<span style="color:#ef4444;">⚠️ মুখ স্পষ্ট বোঝা যাচ্ছে না, স্থির থাকুন...</span>';
          }
        }, 1000);
      });

    } catch (err) {
      status.innerHTML = '<span style="color:#ef4444;">❌ ক্যামেরা চালু করতে অনুমতি দিন অথবা ব্রাউজার রিফ্রেশ করুন।</span>';
      btn.disabled = false;
    }
  }

  async function refreshLiveCaptcha() {
    const displayBox = document.getElementById('captchaDisplayBox');
    const inputField = document.getElementById('captchaInput');
    
    displayBox.style.opacity = '0.5';
    
    try {
      const res = await fetch('generate_captcha.php');
      const data = await res.json();
      
      if(data.captcha) {
        displayBox.innerText = data.captcha;
        if(inputField) inputField.value = '';
      }
    } catch(err) {
      console.error('Captcha refresh failed', err);
    } finally {
      displayBox.style.opacity = '1';
    }
  }
  
  function openFormPreview() {
    const form = document.getElementById('form6Wizard');
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    const fn = document.getElementById('fn_en').value;
    const ln = document.getElementById('ln_en').value;
    const fn_bn = document.getElementById('fn_bn').value;
    const ln_bn = document.getElementById('ln_bn').value;
    const gender = document.querySelector('input[name="gender"]:checked') ? document.querySelector('input[name="gender"]:checked').value : '';
    const email = document.getElementById('emailInput').value;
    const relType = document.querySelector('input[name="relative_type"]:checked') ? document.querySelector('input[name="relative_type"]:checked').value : '';
    const relName = document.getElementById('rel_en').value;
    const relNameBn = document.getElementById('rel_bn').value;
    const mob = document.getElementById('mobInput').value;
    const state = document.getElementById('stateSelect').value;
    const dist = document.getElementById('districtSelect').value;
    const ac = document.getElementById('acSelect').value;
    const house = document.querySelector('input[name="house_no"]').value;
    const street = document.querySelector('input[name="street"]').value;
    const city = document.querySelector('input[name="city"]').value;
    const po = document.querySelector('input[name="post_office"]').value;
    const pin = document.querySelector('input[name="pincode"]').value;
    const tehsil = document.querySelector('input[name="tehsil"]').value;
    const place = document.querySelector('input[name="declaration_place"]').value;

    // ফেস ও ফাইল অবজেক্ট সংগ্রহ
    const previewImgSrc = document.getElementById('regPhotoPreview').src;
    const hasFaceCaptured = document.getElementById('form6FaceDescriptor').value !== '';
    
    const photoInput = document.querySelector('input[name="photo"]');
    const dobDocInput = document.querySelector('input[name="dob_doc_file"]');
    const addrDocInput = document.querySelector('input[name="nid_photo"]');

    const photoName = photoInput.files[0] ? photoInput.files[0].name : 'কোনো ছবি নেই';
    const photoUrl = photoInput.files[0] ? URL.createObjectURL(photoInput.files[0]) : '#';

    const dobDocType = document.querySelector('select[name="dob_doc_type"]').value;
    const dobDocName = dobDocInput.files[0] ? dobDocInput.files[0].name : 'ফাইল নেই';
    const dobDocUrl = dobDocInput.files[0] ? URL.createObjectURL(dobDocInput.files[0]) : '#';

    const addrDocType = document.querySelector('select[name="address_doc_type"]').value;
    const addrDocName = addrDocInput.files[0] ? addrDocInput.files[0].name : 'ফাইল নেই';
    const addrDocUrl = addrDocInput.files[0] ? URL.createObjectURL(addrDocInput.files[0]) : '#';

    const html = `
      <div style="background:#f0f9ff; border:1px solid #bae6fd; padding:12px 16px; border-radius:8px; margin-bottom:15px; color:#0369a1; font-weight:600;">
        দয়া করে নিশ্চিত করুন আপনার দেওয়া সমস্ত তথ্য এবং ডকুমেন্টস সঠিক রয়েছে।
      </div>
      
      <div style="text-align:center; margin-bottom:15px; background:#f8fafc; padding:10px; border-radius:8px; border:1px solid #e2e8f0;">
        <div style="font-weight:700; font-size:12px; margin-bottom:6px; color:#334155;">📸 লাইভ ফেস স্ক্যান প্রিভিউ:</div>
        ${hasFaceCaptured ? `<img src="${previewImgSrc}" style="width:100px; height:75px; object-fit:cover; border-radius:6px; border:2px solid #0b63c5;">` : `<span style="color:#dc2626; font-size:12px;">⚠️ ফেস স্ক্যান করা হয়নি!</span>`}
      </div>

      <table style="width:100%; border-collapse:collapse; font-size:13.5px;">
        <tr style="border-bottom:1px solid #e2e8f0;"><td style="padding:8px; font-weight:700; width:35%;">পূর্ণ নাম (English):</td><td style="padding:8px;">${fn} ${ln}</td></tr>
        <tr style="border-bottom:1px solid #e2e8f0;"><td style="padding:8px; font-weight:700;">পূর্ণ নাম (বাংলা):</td><td style="padding:8px;">${fn_bn} ${ln_bn}</td></tr>
        <tr style="border-bottom:1px solid #e2e8f0;"><td style="padding:8px; font-weight:700;">লিঙ্গ (Gender):</td><td style="padding:8px;">${gender}</td></tr>
        <tr style="border-bottom:1px solid #e2e8f0;"><td style="padding:8px; font-weight:700;">ইমেল ঠিকানা:</td><td style="padding:8px;">${email}</td></tr>
        <tr style="border-bottom:1px solid #e2e8f0;"><td style="padding:8px; font-weight:700;">মোবাইল নম্বর:</td><td style="padding:8px;">${mob}</td></tr>
        <tr style="border-bottom:1px solid #e2e8f0;"><td style="padding:8px; font-weight:700;">অভিভাবকের বিবরণ:</td><td style="padding:8px;">${relType}: ${relName} (${relNameBn})</td></tr>
        <tr style="border-bottom:1px solid #e2e8f0;"><td style="padding:8px; font-weight:700;">রাজ্য ও জেলা:</td><td style="padding:8px;">${state}, ${dist}</td></tr>
        <tr style="border-bottom:1px solid #e2e8f0;"><td style="padding:8px; font-weight:700;">বিধানসভা ক্ষেত্র (AC):</td><td style="padding:8px;">${ac}</td></tr>
        <tr style="border-bottom:1px solid #e2e8f0;"><td style="padding:8px; font-weight:700;">সম্পূর্ণ ঠিকানা:</td><td style="padding:8px;">House: ${house}, Street: ${street}, City: ${city}, PO: ${po}, Tehsil: ${tehsil} - ${pin}</td></tr>
        
        <!-- ডকুমেন্ট উইথ ভিউ বাটন -->
        <tr style="border-bottom:1px solid #e2e8f0;">
          <td style="padding:8px; font-weight:700;">অ্যাপ্লিটান্ট ছবি:</td>
          <td style="padding:8px; display:flex; justify-content:space-between; align-items:center;">
            <span>${photoName}</span>
            ${photoInput.files[0] ? `<a href="${photoUrl}" target="_blank" style="background:#0284c7; color:#fff; padding:3px 10px; border-radius:4px; text-decoration:none; font-size:11px; font-weight:700;">👁 View</a>` : ''}
          </td>
        </tr>
        <tr style="border-bottom:1px solid #e2e8f0;">
          <td style="padding:8px; font-weight:700;">জন্ম প্রমাণপত্র (DOB):</td>
          <td style="padding:8px; display:flex; justify-content:space-between; align-items:center;">
            <span>${dobDocType} (${dobDocName})</span>
            ${dobDocInput.files[0] ? `<a href="${dobDocUrl}" target="_blank" style="background:#0284c7; color:#fff; padding:3px 10px; border-radius:4px; text-decoration:none; font-size:11px; font-weight:700;">👁 View</a>` : ''}
          </td>
        </tr>
        <tr style="border-bottom:1px solid #e2e8f0;">
          <td style="padding:8px; font-weight:700;">ঠিকানার প্রমাণপত্র (Address):</td>
          <td style="padding:8px; display:flex; justify-content:space-between; align-items:center;">
            <span>${addrDocType} (${addrDocName})</span>
            ${addrDocInput.files[0] ? `<a href="${addrDocUrl}" target="_blank" style="background:#0284c7; color:#fff; padding:3px 10px; border-radius:4px; text-decoration:none; font-size:11px; font-weight:700;">👁 View</a>` : ''}
          </td>
        </tr>

        <tr><td style="padding:8px; font-weight:700;">ঘোষণার স্থান:</td><td style="padding:8px;">${place}</td></tr>
      </table>
    `;

    document.getElementById('previewContent').innerHTML = html;
    document.getElementById('previewModal').style.display = 'block';
  }
  function closeFormPreview() {
    document.getElementById('previewModal').style.display = 'none';
  }

  function confirmAndSubmitForm() {
    closeFormPreview();
    const form = document.getElementById('form6Wizard');
    const submitBtn = document.createElement('input');
    submitBtn.type = 'hidden';
    submitBtn.name = 'register';
    submitBtn.value = '1';
    form.appendChild(submitBtn);
    form.submit();
  }
</script>

<?php include 'includes/scripts.php'; ?>
</body>
</html>