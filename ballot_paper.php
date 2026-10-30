<?php
  session_start();
  include 'includes/conn.php';

  if(!isset($_SESSION['voter'])){
    header('location: voter_secure_login.php');
    exit();
  }

  $voter_db_id = $_SESSION['voter'];

  // ১. নির্বাচনের সময়সীমা ভ্যালিডেশন (Timezone Safe Check)
  date_default_timezone_set('Asia/Kolkata');
  $now = date('Y-m-d H:i:s');

  // স্ট্যাটাস লাইভ এবং বর্তমান সময় স্টার্ট ও এন্ড টাইমের মধ্যে আছে কিনা তা যাচাই
  $election_check = $conn->query("
    SELECT * FROM elections 
    WHERE status = 'live' 
    AND (
      (NOW() BETWEEN start_time AND end_time) 
      OR ('$now' BETWEEN start_time AND end_time)
      OR (start_time <= '$now' AND end_time >= '$now')
    )
    LIMIT 1
  ");

  // যদি কোনো কারণে ডেটাবেজে টাইম স্ট্রিং মিসম্যাচ থাকে, তবে স্ট্যাটাস লাইভ থাকলেই অ্যাক্সেস দেওয়া
  if(!$election_check || $election_check->num_rows == 0){
    $fallback_check = $conn->query("SELECT * FROM elections WHERE status = 'live' LIMIT 1");
    if(!$fallback_check || $fallback_check->num_rows == 0){
      echo "<script>
        alert('⚠️ বর্তমানে কোনো নির্বাচন সক্রিয় নেই অথবা ভোটদানের সময়সীমা সমাপ্ত হয়েছে!');
        window.location.href = 'voter_logout.php';
      </script>";
      exit();
    }
  }

 // ২. ভোটারের তথ্য এবং বিধানসভা সংগ্রহ
  $voter_query = $conn->query("SELECT * FROM voters WHERE id = '$voter_db_id' LIMIT 1");
  $voter = $voter_query->fetch_assoc();

  if(!$voter){
    session_destroy();
    header('location: voter_secure_login.php');
    exit();
  }

  // সক্রিয় নির্বাচন খোঁজা
  $elec_q = $conn->query("SELECT id FROM elections WHERE status = 'live' ORDER BY id DESC LIMIT 1");
  $active_election_id = ($elec_q && $elec_q->num_rows > 0) ? $elec_q->fetch_assoc()['id'] : 0;

  // এই নির্দিষ্ট নির্বাচনে তিনি ইতিমধ্যে ভোট দিয়েছেন কি না যাচাই
  $history_check = $conn->query("SELECT id FROM voter_election_history WHERE voter_id = '$voter_db_id' AND election_id = '$active_election_id' LIMIT 1");

  if($history_check && $history_check->num_rows > 0){
    session_destroy();
    echo "<script>alert('⚠️ আপনি ইতিমধ্যে এই নির্বাচনে ভোট সম্পন্ন করেছেন!'); window.location.href='voter_secure_login.php';</script>";
    exit();
  }

  // ফর্ম ৬ থেকে বিস্তারিত ঠিকানা ও বিধানসভা সংগ্রহ
  $fn = $conn->real_escape_string($voter['firstname']);
  $ln = $conn->real_escape_string($voter['lastname']);
  $ph = $conn->real_escape_string($voter['phone'] ?? '');

  $app_query = $conn->query("SELECT gender, relative_type, relative_name, constituency, state, district, house_no, street, city, post_office, pincode FROM voter_applications WHERE mobile = '$ph' OR (firstname = '$fn' AND lastname = '$ln') ORDER BY id DESC LIMIT 1");
  $extra_info = ($app_query && $app_query->num_rows > 0) ? $app_query->fetch_assoc() : [];
  $voter = array_merge((array)$voter, (array)$extra_info);

  $voter_constituency = $voter['constituency'] ?? '';
  $voter_photo = (!empty($voter['photo']) && file_exists('images/'.$voter['photo'])) ? 'images/'.$voter['photo'] : 'images/profile.jpg';

  // ৩. ডাইনামিক ফিল্টারিং: শুধুমাত্র ভোটারের বিধানসভার প্রার্থীদের আনা
  $cands_stmt = $conn->prepare("SELECT * FROM candidates WHERE constituency = ? OR constituency = 'ALL' ORDER BY id ASC");
  $cands_stmt->bind_param("s", $voter_constituency);
  $cands_stmt->execute();
  $cands_result = $cands_stmt->get_result();

  $candidates_list = [];
  while($row = $cands_result->fetch_assoc()){
    $candidates_list[] = $row;
  }

  // যদি নির্দিষ্ট বিধানসভায় প্রার্থী না থাকে, ডিফল্ট NOTA বাধ্যতামূলক যুক্ত করা
  if(count($candidates_list) == 0){
    $candidates_list[] = [
      'id' => 999,
      'firstname' => 'NOTA',
      'lastname' => '(কাউকে নয়)',
      'platform' => 'None of the Above',
      'photo' => 'profile.jpg',
      'symbol' => 'symbol_nota.png'
    ];
  }
?>
<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="utf-8">
  <title>ডিজিটাল ব্যালট ইউনিট • Election Commission of India</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Tiro+Bangla&family=Share+Tech+Mono&display=swap" rel="stylesheet">

  <style>
    :root {
      --eci-blue: #0b63c5;
      --eci-dark: #0f172a;
      --evm-metal: #1e293b;
      --eci-orange: #ff9933;
      --eci-green: #10b981;
    }
    * { box-sizing: border-box; font-family: 'Plus Jakarta Sans', 'Tiro Bangla', sans-serif; }
    body {
      background: #090d16;
      color: #f8fafc;
      min-height: 100vh;
      margin: 0;
      padding: 16px 8px;
    }
    .evm-wrapper {
      max-width: 980px;
      margin: 0 auto;
      background: #111827;
      border-radius: 16px;
      border: 2px solid #374151;
      box-shadow: 0 20px 40px rgba(0,0,0,0.7);
      overflow: hidden;
    }
    .evm-header {
      background: linear-gradient(135deg, #0b63c5, #0369a1);
      padding: 14px 18px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 4px solid var(--eci-orange);
      gap: 10px;
    }
    .timer-badge {
      background: #0f172a;
      color: #fbbf24;
      font-family: 'Share Tech Mono', monospace;
      font-size: 16px;
      font-weight: 800;
      padding: 6px 12px;
      border-radius: 8px;
      border: 1.5px solid #d97706;
      display: flex;
      align-items: center;
      gap: 6px;
      white-space: nowrap;
    }
    .voter-profile-strip {
      background: #1f2937;
      border-bottom: 2px solid #374151;
      padding: 16px 18px;
      display: flex;
      flex-wrap: wrap;
      gap: 16px;
      align-items: center;
    }
    .voter-avatar {
      width: 75px;
      height: 85px;
      border-radius: 8px;
      object-fit: cover;
      border: 2px solid var(--eci-green);
      background: #0f172a;
    }
    .profile-meta {
      flex-grow: 1;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
      gap: 8px 12px;
      font-size: 13px;
    }
    .meta-item b { color: #94a3b8; font-weight: 600; }
    .meta-item span { color: #ffffff; font-weight: 700; word-break: break-word; }

    .evm-ballot-board { padding: 18px 14px; background: #0b1120; }
    
    /* ডেস্কটপ ও ট্যাবলেট গ্রিড */
    .evm-row {
      background: #ffffff;
      color: #0f172a;
      border-radius: 12px;
      padding: 12px 14px;
      display: grid;
      grid-template-columns: 35px 55px 1fr 50px 130px;
      align-items: center;
      gap: 12px;
      margin-bottom: 12px;
      border: 2px solid #cbd5e1;
    }
    .cand-img { width: 50px; height: 50px; border-radius: 6px; object-fit: cover; border: 1px solid #cbd5e1; }
    .party-symbol { width: 45px; height: 45px; object-fit: contain; }
    .btn-vote-blue {
      background: #0b63c5;
      color: #ffffff;
      border: none;
      height: 44px;
      border-radius: 8px;
      font-weight: 800;
      font-size: 13.5px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      width: 100%;
    }

    /* মোবাইল ও ছোট ডিভাইসের জন্য অপ্টিমাইজেশন */
    @media (max-width: 640px) {
      .evm-header { flex-direction: column; align-items: flex-start; }
      .timer-badge { align-self: flex-start; }
      .voter-profile-strip { flex-direction: column; text-align: left; }
      .voter-avatar { width: 70px; height: 80px; align-self: center; }
      
      .evm-row {
        grid-template-columns: 25px 45px 1fr 40px;
        grid-template-areas: 
          "sl img name sym"
          "btn btn btn btn";
        gap: 10px 8px;
        padding: 10px;
      }
      .evm-row > div:first-child { grid-area: sl; }
      .cand-img { grid-area: img; width: 44px; height: 44px; }
      .evm-row > div:nth-child(3) { grid-area: name; }
      .party-symbol { grid-area: sym; width: 38px; height: 38px; }
      .btn-vote-blue { grid-area: btn; height: 40px; font-size: 13px; margin-top: 4px; }
      /* ================= ECI POPUP CONFIRMATION MODAL CSS ================= */
    .modal-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(15, 23, 42, 0.75);
      backdrop-filter: blur(4px);
      z-index: 999999;
      align-items: center;
      justify-content: center;
      padding: 15px;
    }
    .modal-box {
      background: #ffffff;
      color: #0f172a;
      max-width: 460px;
      width: 100%;
      border-radius: 16px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
      padding: 28px 24px;
      text-align: center;
      border: 2px solid #0b63c5;
      animation: modalFadeIn 0.25s ease-out;
    }
    @keyframes modalFadeIn {
      from { opacity: 0; transform: scale(0.92); }
      to { opacity: 1; transform: scale(1); }
    }
    .modal-btns {
      display: flex;
      gap: 12px;
      margin-top: 24px;
    }
    .modal-btns button {
      height: 44px;
      border-radius: 8px;
      font-weight: 800;
      font-size: 14px;
      cursor: pointer;
      border: none;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      transition: all 0.2s;
    }
    .modal-btns button:hover {
      filter: brightness(1.1);
      transform: translateY(-1px);
    }
    .modal-btns button:active {
      transform: translateY(0);
    }
    }
  </style>
</head>
<body>

<div class="evm-wrapper">
  
  <div class="evm-header">
    <div style="display:flex; align-items:center; gap:12px;">
      <div style="background:#ffffff; width:40px; height:40px; border-radius:8px; display:flex; align-items:center; justify-content:center;">
        <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/Emblem_of_India.svg" style="height:30px;" alt="ECI">
      </div>
      <div>
        <h3 style="margin:0; font-size:17px; font-weight:800; color:#fff;">ভারতের নির্বাচন কমিশন • Digital EVM</h3>
        <small style="opacity:0.9; color:#e0f2fe;">Constituency-Locked Secret Ballot Terminal</small>
      </div>
    </div>
    <div class="timer-badge">
      <i class="fa fa-clock-o"></i> <span id="sessionTimer">05:00</span>
    </div>
  </div>

  <div class="voter-profile-strip">
    <img src="<?php echo $voter_photo; ?>" class="voter-avatar" alt="Voter Photo">
    
    <div class="profile-meta">
      <div class="meta-item">
        <b>ভোটারের নাম:</b> <span><?php echo htmlspecialchars($voter['firstname'].' '.$voter['lastname']); ?></span>
      </div>
      <div class="meta-item">
        <b>ভোটার আইডি (EPIC):</b> <span style="font-family:'Share Tech Mono', monospace; color:#38bdf8;"><?php echo htmlspecialchars($voter['voters_id']); ?></span>
      </div>
      <div class="meta-item">
        <b>অভিভাবকের নাম:</b> <span><?php echo htmlspecialchars($voter['relative_name'] ?? 'N/A'); ?> (<?php echo htmlspecialchars($voter['relative_type'] ?? ''); ?>)</span>
      </div>
      <div class="meta-item">
        <b>নিবন্ধিত বিধানসভা:</b> <span style="color:#fbbf24; font-weight:800;"><?php echo htmlspecialchars($voter_constituency); ?></span>
      </div>
      <div class="meta-item" style="grid-column: 1 / -1;">
        <b>স্থায়ী ঠিকানা:</b> <span><?php 
          $addr = array_filter([$voter['house_no'] ?? '', $voter['street'] ?? '', $voter['city'] ?? '', $voter['post_office'] ?? '', $voter['district'] ?? '', $voter['pincode'] ?? '']);
          echo htmlspecialchars(implode(', ', $addr));
        ?></span>
      </div>
    </div>

    <div>
      <span style="background:rgba(16,185,129,0.2); color:#10b981; border:1px solid #10b981; font-size:12px; font-weight:800; padding:6px 12px; border-radius:20px;">
        ✓ বায়োমেট্রিক অনুমোদিত
      </span>
    </div>
  </div>

  <div class="evm-ballot-board">
    <div style="background:#1e293b; border-left:4px solid var(--eci-orange); padding:10px 14px; border-radius:6px; font-size:12.5px; color:#cbd5e1; margin-bottom:18px;">
      ℹ️ নির্দেশিকা: আপনার নির্বাচিত প্রার্থীর পাশের নীল বাটনে চাপ দিন এবং পপ-আপ বক্সে ভোট নিশ্চিত করুন।
    </div>

    <?php 
      $sl = 1;
      foreach($candidates_list as $cand): 
        $cand_photo = (!empty($cand['photo']) && file_exists('images/'.$cand['photo'])) ? 'images/'.$cand['photo'] : 'images/profile.jpg';
        $party_symbol = (!empty($cand['symbol']) && file_exists('images/'.$cand['symbol'])) ? 'images/'.$cand['symbol'] : 'images/symbol_demo.png';
        $cand_fullname = $cand['firstname'] . ' ' . $cand['lastname'];
    ?>
      <div class="evm-row">
        <div style="font-family:'Share Tech Mono', monospace; font-size:18px; font-weight:800; color:#64748b;">
          <?php echo str_pad($sl, 2, '0', STR_PAD_LEFT); ?>
        </div>
        
        <img src="<?php echo $cand_photo; ?>" class="cand-img" alt="Candidate" onerror="this.src='images/profile.jpg'">

        <div>
          <div style="font-weight:800; font-size:15.5px; color:#0f172a;"><?php echo htmlspecialchars($cand_fullname); ?></div>
          <div style="font-size:12.5px; color:#475569; font-weight:600;"><?php echo htmlspecialchars($cand['platform']); ?></div>
        </div>

        <img src="<?php echo $party_symbol; ?>" class="party-symbol" alt="Symbol" onerror="this.src='https://placehold.co/50x50?text=Symbol'">

        <button type="button" class="btn-vote-blue" onclick="openConfirmModal('<?php echo $cand['id']; ?>', '<?php echo addslashes($cand_fullname); ?>', '<?php echo addslashes($cand['platform']); ?>')">
          ভোট দিন 🔵
        </button>
      </div>
    <?php 
      $sl++; 
      endforeach; 
    ?>
  </div>

</div>

<!-- কনফার্মেশন মোডাল -->
<div class="modal-overlay" id="confirmModal">
  <div class="modal-box">
    <div style="font-size:42px; margin-bottom:10px;">⚠️</div>
    <h3 style="margin:0; font-size:19px; font-weight:800;">আপনার ভোটটি নিশ্চিত করুন</h3>
    <p style="color:#475569; font-size:14px; margin:12px 0 0;" id="modalCandidateText"></p>
    
    <input type="hidden" id="modalCandId">
    <input type="hidden" id="modalConstituency" value="<?php echo htmlspecialchars($voter_constituency); ?>">
    
    <div class="modal-btns">
      <button type="button" class="btn-vote-blue" id="btnCancelVote" style="background:#94a3b8; flex:1;" onclick="closeModal()">
        ❌ পরিবর্তন করুন
      </button>
      <button type="button" class="btn-vote-blue" id="btnConfirmVote" style="background:#10b981; flex:1;" onclick="executeSecureVote()">
        🟢 হ্যাঁ, নিশ্চিত
      </button>
    </div>
    <div id="voteStatusText" style="font-size:13px; margin-top:12px; font-weight:700; display:none;"></div>
  </div>
</div>

<script>
  let seconds = 300;
  const timerElem = document.getElementById('sessionTimer');

  const timerTimer = setInterval(() => {
    seconds--;
    let m = Math.floor(seconds / 60);
    let s = seconds % 60;
    timerElem.innerText = `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;

    if(seconds <= 0){
      clearInterval(timerTimer);
      window.location.replace('voter_logout.php');
    }
  }, 1000);

  history.pushState(null, null, location.href);
  window.onpopstate = function () { history.go(1); };

  function openConfirmModal(candId, candName, partyName) {
    document.getElementById('modalCandId').value = candId;
    document.getElementById('modalCandidateText').innerHTML = `আপনি কি <b>"${candName} (${partyName})"</b>-কে ভোট দিতে নিশ্চিত?`;
    document.getElementById('confirmModal').style.display = 'flex';
  }

  function closeModal() {
    document.getElementById('confirmModal').style.display = 'none';
  }

  // ইভিএম দীর্ঘ বিপ সাউন্ড (শুধু ডেটাবেজে সেভ হলে বাজবে)
  function playRealEvmBeep() {
    return new Promise((resolve) => {
      try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = 'sine';
        osc.frequency.value = 850; // খাঁটি EVM বিপ ফ্রিকোয়েন্সি
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start();
        setTimeout(() => {
          osc.stop();
          resolve();
        }, 1800); // ১.৮ সেকেন্ড দীর্ঘ বিপ
      } catch(e) {
        resolve();
      }
    });
  }

  // নিরাপদ অ্যাসিঙ্ক্রোনাস ভোট সাবমিট মেকানিজম
  async function executeSecureVote() {
    const btnConfirm = document.getElementById('btnConfirmVote');
    const btnCancel = document.getElementById('btnCancelVote');
    const statusText = document.getElementById('voteStatusText');
    const candId = document.getElementById('modalCandId').value;
    const constituency = document.getElementById('modalConstituency').value;

    // বাটন লক করা যাতে ডবল ক্লিক না হয়
    btnConfirm.disabled = true;
    btnCancel.disabled = true;
    btnConfirm.innerHTML = '<i class="fa fa-spinner fa-spin"></i> ভোট প্রসেস হচ্ছে...';
    statusText.style.display = 'block';
    statusText.style.color = '#0284c7';
    statusText.innerText = 'সার্ভারের সাথে নিরাপদ সংযোগ স্থাপন হচ্ছে...';

    const formData = new FormData();
    formData.append('candidate_id', candId);
    formData.append('constituency', constituency);

    try {
      const response = await fetch('submit_vote_ajax.php', {
        method: 'POST',
        body: formData
      });

      const res = await response.json();

      if (res.status === 'success') {
        // ১. ডেটাবেজে ভোট সফলভাবে জমা পড়েছে -> এখন দীর্ঘ বিপ সাউন্ড বাজবে
        statusText.style.color = '#10b981';
        statusText.innerHTML = '✓ ব্যালট বক্সে ভোট সফলভাবে রেকর্ড হয়েছে!';
        
        await playRealEvmBeep();

        // সফলতার পেজে রিডাইরেক্ট
        window.location.href = 'vote_success.php?hash=' + encodeURIComponent(res.block_hash);

      } else {
        // ২. ডেটাবেজে কোনো কারণে জমা না পড়লে -> কোনো শব্দ হবে না, লাল সতর্কতা আসবে
        alert("⚠️ ত্রুটি: " + res.message);
        statusText.style.color = '#ef4444';
        statusText.innerText = 'ভোট জমা পড়েনি। অনুগ্রহ করে পুনরায় চেষ্টা করুন।';
        btnConfirm.disabled = false;
        btnCancel.disabled = false;
        btnConfirm.innerHTML = '🟢 হ্যাঁ, নিশ্চিত';
      }
    } catch (err) {
      // ৩. নেটওয়ার্ক বিচ্ছিন্ন বা সার্ভার ক্র্যাশ হলে রোলব্যাক বার্তা
      alert("❌ নেটওয়ার্ক ত্রুটি! সার্ভারে যোগাযোগ করা সম্ভব হয়নি। আপনার ভোটটি গৃহীত হয়নি, অনুগ্রহ করে পুনরায় চেষ্টা করুন।");
      statusText.style.color = '#ef4444';
      statusText.innerText = 'সংযোগ বিচ্ছিন্ন হয়েছে!';
      btnConfirm.disabled = false;
      btnCancel.disabled = false;
      btnConfirm.innerHTML = '🟢 হ্যাঁ, নিশ্চিত';
    }
  }
</script>
</body>
</html>