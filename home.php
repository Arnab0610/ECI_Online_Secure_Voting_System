<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Tiro+Bangla:ital@0;1&family=Share+Tech+Mono&display=swap" rel="stylesheet">

<style>
  :root {
    --bg-main: #f8fafc;
    --sidebar-bg: rgba(255, 255, 255, 0.9);
    --card-bg: rgba(255, 255, 255, 0.88);
    --card-border: rgba(226, 232, 240, 0.8);
    --text-primary: #0f172a;
    --text-secondary: #334155;
    --text-muted: #64748b;
    --header-bg: rgba(255, 255, 255, 0.95);
    --accent-blue: #0b63c5;
    --shadow-card: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
    --btn-blue: #0b63c5;
    
    --chakra-stroke: #000080;
    --chakra-opacity: 0.12;
    --saffron-top-opacity: 0.28;
    --tricolor-wave-opacity: 0.85;
    --ribbon-right-opacity: 0.75;
    --emblem-filter: none;
  }

  /* ---------------- DARK THEME (VIVID TRICOLOR CYBER) ---------------- */
  body.dark-mode {
    --bg-main: #060b17;
    --sidebar-bg: rgba(15, 23, 42, 0.85);
    --card-bg: rgba(18, 26, 43, 0.8);
    --card-border: rgba(255, 255, 255, 0.09);
    --text-primary: #f8fafc;
    --text-secondary: #cbd5e1;
    --text-muted: #94a3b8;
    --header-bg: rgba(11, 17, 32, 0.95);
    --accent-blue: #38bdf8;
    --shadow-card: 0 10px 30px rgba(0, 0, 0, 0.5);
    --btn-blue: #0284c7;

    --chakra-stroke: #38bdf8;
    --chakra-opacity: 0.15;
    --saffron-top-opacity: 0.45;
    --tricolor-wave-opacity: 0.85;
    --ribbon-right-opacity: 0.85;
    --emblem-filter: brightness(0) invert(1);
  }

  body {
    background-color: var(--bg-main);
    color: var(--text-primary);
    font-family: 'Plus Jakarta Sans', 'Tiro Bangla', sans-serif;
    transition: background-color 0.3s ease, color 0.3s ease;
    min-height: 100vh;
    margin: 0;
    position: relative;
    overflow-x: hidden;
  }

  .content-wrapper {
    background: transparent !important;
  }

  /* ---------------- TRICOLOR VECTOR BACKGROUND ELEMENTS ---------------- */
  .tricolor-saffron-top {
    position: fixed;
    top: -100px;
    left: -100px;
    width: 450px;
    height: 450px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(255, 153, 51, var(--saffron-top-opacity)) 0%, rgba(255, 153, 51, 0) 70%);
    filter: blur(40px);
    pointer-events: none;
    z-index: 0;
  }

  .bg-ashoka-chakra {
    position: fixed;
    top: 50%;
    left: -140px;
    transform: translateY(-50%);
    width: 600px;
    height: 600px;
    opacity: var(--chakra-opacity);
    pointer-events: none;
    z-index: 0;
    transition: opacity 0.3s ease;
  }

  .tricolor-wave-bottom-left {
    position: fixed;
    bottom: -120px;
    left: -120px;
    width: 580px;
    height: 580px;
    border-radius: 50%;
    background: conic-gradient(
      from 210deg at 50% 50%,
      #ff9933 0deg,
      #ffb066 35deg,
      #ffffff 70deg,
      #138808 105deg,
      transparent 180deg
    );
    filter: blur(42px);
    opacity: var(--tricolor-wave-opacity);
    pointer-events: none;
    z-index: 0;
  }

  .tricolor-ribbon-right {
    position: fixed;
    top: -40px;
    right: -80px;
    width: 420px;
    height: 100vh;
    background: linear-gradient(
      140deg,
      rgba(255, 153, 51, 0.7) 0%,
      rgba(255, 153, 51, 0.3) 28%,
      rgba(255, 255, 255, 0.4) 48%,
      rgba(19, 136, 8, 0.35) 68%,
      rgba(19, 136, 8, 0.75) 100%
    );
    border-radius: 65% 0 0 65%;
    filter: blur(45px);
    opacity: var(--ribbon-right-opacity);
    pointer-events: none;
    z-index: 0;
  }

  .cyber-grid-overlay {
    position: fixed;
    inset: 0;
    background-image: 
      radial-gradient(rgba(56, 189, 248, 0.08) 1px, transparent 1px),
      linear-gradient(to right, rgba(56, 189, 248, 0.02) 1px, transparent 1px),
      linear-gradient(to bottom, rgba(56, 189, 248, 0.02) 1px, transparent 1px);
    background-size: 32px 32px;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
    z-index: 0;
  }
  body.dark-mode .cyber-grid-overlay {
    opacity: 1;
  }

  /* ---------------- TOP NAVBAR ---------------- */
  .portal-navbar {
    background: var(--header-bg);
    border-bottom: 1px solid var(--card-border);
    backdrop-filter: blur(16px);
    padding: 10px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 2px 15px rgba(0,0,0,0.03);
  }
  .portal-brand {
    display: flex;
    align-items: center;
    gap: 14px;
  }

  /* Official Emblem Container */
  .emblem-logo-badge {
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .emblem-logo-img {
    height: 46px;
    width: auto;
    object-fit: contain;
    filter: var(--emblem-filter);
    transition: filter 0.3s ease;
  }

  .portal-brand h2 {
    margin: 0;
    font-size: 21px;
    font-weight: 800;
    color: var(--text-primary);
  }

  .theme-toggle-btn {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    color: var(--text-primary);
    padding: 8px 18px;
    border-radius: 20px;
    cursor: pointer;
    font-weight: 600;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    box-shadow: var(--shadow-card);
  }
  .theme-toggle-btn:hover {
    border-color: var(--accent-blue);
  }

  /* ---------------- MAIN PORTAL LAYOUT ---------------- */
  .portal-container {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 25px;
    padding: 30px 40px;
    max-width: 1600px;
    margin: 0 auto;
  }

  /* Sidebar */
  .portal-sidebar {
    background: var(--sidebar-bg);
    border: 1px solid var(--card-border);
    backdrop-filter: blur(14px);
    border-radius: 20px;
    padding: 30px 20px;
    height: fit-content;
    box-shadow: var(--shadow-card);
  }
  .sidebar-user-section {
    text-align: center;
    margin-bottom: 25px;
  }
  .user-badge-title {
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 12px;
    color: var(--text-primary);
  }
  .user-avatar-ring {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    margin: 0 auto 12px;
    padding: 3px;
    background: linear-gradient(135deg, #ff9933, #ffffff, #138808);
    box-shadow: 0 4px 14px rgba(0,0,0,0.12);
  }
  .user-avatar-ring img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    background: #fff;
  }

  .nav-menu-list {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  .nav-menu-list li a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 18px;
    color: var(--text-secondary);
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    border-radius: 12px;
    transition: all 0.2s;
    margin-bottom: 6px;
  }
  .nav-menu-list li.active a,
  .nav-menu-list li a:hover {
    background: rgba(11, 99, 197, 0.12);
    color: var(--accent-blue);
  }

  /* ---------------- DASHBOARD CARDS GRID ---------------- */
  .grid-3-col {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 20px;
  }

  .portal-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    backdrop-filter: blur(14px);
    border-radius: 18px;
    padding: 22px;
    box-shadow: var(--shadow-card);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .portal-card:hover {
    transform: translateY(-3px);
  }
  .portal-card h3 {
    margin: 0 0 10px;
    font-size: 16px;
    font-weight: 700;
    color: var(--text-primary);
  }

  .biometric-center-card {
    border: 2px solid #38bdf8;
    background: linear-gradient(135deg, rgba(56, 189, 248, 0.08), rgba(15, 23, 42, 0.7));
    box-shadow: 0 0 25px rgba(56, 189, 248, 0.18);
    text-align: center;
  }
  .fingerprint-btn {
    width: 75px;
    height: 75px;
    border-radius: 50%;
    margin: 12px auto;
    display: flex;
    align-items: center;
    justify-content: center;
    background: radial-gradient(circle, #0284c7, #0369a1);
    color: white;
    font-size: 32px;
    box-shadow: 0 0 18px rgba(2, 132, 199, 0.4);
    cursor: pointer;
    transition: transform 0.2s;
  }
  .fingerprint-btn:hover {
    transform: scale(1.08);
  }

  .btn-gov-blue {
    background: var(--btn-blue);
    color: #ffffff !important;
    border: none;
    border-radius: 8px;
    padding: 8px 18px;
    font-weight: 700;
    font-size: 13px;
    text-decoration: none;
    display: inline-block;
    cursor: pointer;
  }

  .quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
    margin-bottom: 25px;
  }
  .action-btn-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    backdrop-filter: blur(14px);
    border-radius: 14px;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: var(--shadow-card);
    text-decoration: none;
    color: var(--text-primary);
    font-weight: 700;
    font-size: 13.5px;
    transition: all 0.2s;
  }
  .action-btn-card:hover {
    transform: translateY(-2px);
    border-color: var(--accent-blue);
    color: var(--accent-blue);
  }
  .action-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
  }

  .ballot-slate-box {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    backdrop-filter: blur(14px);
    border-radius: 20px;
    padding: 25px;
    box-shadow: var(--shadow-card);
  }
  .candidate-row-box {
    display: flex;
    align-items: center;
    padding: 14px 18px;
    background: rgba(0, 0, 0, 0.02);
    border: 1px solid var(--card-border);
    border-radius: 12px;
    margin-bottom: 10px;
  }
  body.dark-mode .candidate-row-box {
    background: rgba(255, 255, 255, 0.03);
  }
</style>

<body>
  <!-- VIVID TRICOLOR VECTOR BACKGROUND ELEMENTS -->
  <div class="tricolor-saffron-top"></div>
  <div class="tricolor-ribbon-right"></div>
  <div class="tricolor-wave-bottom-left"></div>
  
  <svg class="bg-ashoka-chakra" viewBox="0 0 100 100" fill="none">
    <circle cx="50" cy="50" r="46" stroke="var(--chakra-stroke)" stroke-width="2.5"/>
    <circle cx="50" cy="50" r="8" fill="var(--chakra-stroke)"/>
    <?php for($i=0; $i<24; $i++): ?>
      <line x1="50" y1="50" x2="<?php echo 50 + 40*cos(deg2rad($i*15)); ?>" y2="<?php echo 50 + 40*sin(deg2rad($i*15)); ?>" stroke="var(--chakra-stroke)" stroke-width="1.8"/>
    <?php endfor; ?>
  </svg>
  
  <div class="cyber-grid-overlay"></div>

  <!-- Top Navbar with Official State Emblem of India -->
  <nav class="portal-navbar">
    <div class="portal-brand">
      <div class="emblem-logo-badge">
        <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/Emblem_of_India.svg" 
             alt="National Emblem of India" 
             class="emblem-logo-img">
      </div>

      <div>
        <h2>ভারত ভোটার পোর্টাল</h2>
        <span style="font-size:10px; font-weight:700; color:var(--text-muted); letter-spacing:0.8px; text-transform:uppercase;">National E-Voting Service</span>
      </div>
    </div>

    <div style="display: flex; align-items: center; gap: 15px;">
      <button class="theme-toggle-btn" id="themeBtn">
        <i class="fa fa-moon-o" id="themeIco"></i> <span id="themeLbl">ডার্ক মোড</span>
      </button>
      <a href="logout.php" class="btn-gov-blue" style="background: #dc2626;"><i class="fa fa-sign-out"></i> লগআউট</a>
    </div>
  </nav>

  <!-- Main Grid Layout -->
  <div class="portal-container">
    
    <!-- LEFT SIDEBAR -->
    <aside class="portal-sidebar">
      <div class="sidebar-user-section">
        <div class="user-badge-title">স্বাগতম, <?php echo htmlspecialchars($voter['firstname']); ?></div>
        <div class="user-avatar-ring">
          <img src="<?php echo (!empty($voter['photo'])) ? 'images/'.$voter['photo'] : 'images/profile.jpg'; ?>" alt="User Avatar">
        </div>
        <span class="label label-success" style="border-radius: 6px; font-size: 10.5px; padding: 4px 8px;"><i class="fa fa-check-circle"></i> ভেরিফাইড ভোটার</span>
      </div>

      <ul class="nav-menu-list">
        <li class="active"><a href="#"><i class="fa fa-th-large"></i> ড্যাশবোর্ড</a></li>
        <li><a href="#voter_details"><i class="fa fa-user-circle"></i> প্রোফাইল</a></li>
        <li><a href="#ballot_area"><i class="fa fa-check-square-o"></i> ডিজিটাল ভোটিং</a></li>
        <li><a href="voter_download_epic.php"><i class="fa fa-download"></i> ডাউনলোড সেন্টার</a></li>
        <li><a href="#"><i class="fa fa-question-circle"></i> Help & Support</a></li>
      </ul>
    </aside>

    <!-- RIGHT CONTENT MATRIX -->
    <main>
      <?php
        date_default_timezone_set('Asia/Kolkata');
        $e_sql = "SELECT * FROM elections ORDER BY id DESC LIMIT 1";
        $e_query = $conn->query($e_sql);
        $election_active = true;
        $election_msg = '';
        $e_title = 'জাতীয় নির্বাচন ২০২৬';
        $start_txt = '৩০ আগস্ট, ২০২৬';

        if($e_query->num_rows > 0){
          $election = $e_query->fetch_assoc();
          $e_title = $election['title'];
          $start_txt = date('d M, Y - h:i A', strtotime($election['start_time']));
          $now = date('Y-m-d H:i:s');

          if($election['status'] == 'paused'){
            $election_active = false;
            $election_msg = 'Voting is temporarily suspended by Emergency Freeze / Kill Switch.';
          } elseif($election['status'] == 'completed'){
            $election_active = false;
            $election_msg = 'নির্বাচন আনুষ্ঠানিকভাবে সমাপ্ত হয়েছে।';
          } elseif($election['status'] == 'scheduled' && $now < $election['start_time']){
            $election_active = false;
            $election_msg = 'ভোটগ্রহণ এখনো শুরু হয়নি। শুরুর সময়: '.$start_txt;
          } elseif($now > $election['end_time']){
            $election_active = false;
            $election_msg = 'ভোটগ্রহণের সময়সীমা শেষ হয়েছে।';
          }
        }
      ?>

      <!-- ROW 1: PRIMARY STATUS CARDS -->
      <div class="grid-3-col">
        <!-- 1. ভোটারের স্থিতি -->
        <div class="portal-card">
          <div>
            <h3>ভোটারের স্থিতি</h3>
            <p style="font-size: 13px; color: var(--text-secondary); margin: 3px 0;">আপনার ভোটার স্থিতি: <b style="color: #10b981;">নিবন্ধিত</b></p>
            <p style="font-size: 12px; color: var(--text-muted); margin: 2px 0;">আসন্ন নির্বাচন: <b><?php echo htmlspecialchars($e_title); ?></b></p>
          </div>
          <div style="margin-top: 15px;">
            <a href="#ballot_area" class="btn-gov-blue">স্থানান্তরিত ভোটকেন্দ্র দেখুন</a>
          </div>
        </div>

        <!-- 2. আধার লিঙ্ক -->
        <div class="portal-card">
          <div>
            <h3>আধার লিঙ্ক</h3>
            <div style="display: flex; align-items: center; gap: 8px; margin: 8px 0;">
              <i class="fa fa-check-circle" style="color: #10b981; font-size: 20px;"></i>
              <span style="font-weight: 700; color: #10b981; font-size: 15px;">লিঙ্ক করা আছে</span>
            </div>
            <p style="font-size: 11.5px; color: var(--text-muted); margin: 0;">UIDAI 256-Bit Vault Verified</p>
          </div>
          <div style="margin-top: 15px;">
            <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 5px 10px; border-radius: 6px;">KYC Cleared</span>
          </div>
        </div>

        <!-- 3. ডাউনলোড ই-এপিক -->
        <div class="portal-card">
          <div>
            <h3>ডাউনলোড ই-এপিক</h3>
            <p style="font-size: 12.5px; color: var(--text-secondary); margin: 3px 0;">আপনার ডিজিটাল ভারতীয় ভোটার কার্ড</p>
            <p style="font-family: 'Share Tech Mono', monospace; font-size: 14px; font-weight: 800; color: var(--accent-blue); margin-top: 5px;">
              EPIC: <?php echo htmlspecialchars($voter['voters_id']); ?>
            </p>
          </div>
          <div style="margin-top: 15px;">
            <button class="btn-gov-blue" onclick="alert('আপনার ডিজিটাল ই-এপিক কার্ডটি নিরাপদ অবস্থায় আছে।');">আপনার ই-এপিক ডাউনলোড করুন</button>
          </div>
        </div>
      </div>

      <!-- ROW 2: PROFILE, BIOMETRIC NODE & ELECTION CENTER -->
      <div class="grid-3-col">
        <!-- 4. আপনার প্রোফাইল -->
        <div class="portal-card" id="voter_details">
          <div>
            <h3>আপনার প্রোফাইল</h3>
            <div style="font-size: 12px; color: var(--text-secondary); line-height: 1.8;">
              <div><b>Voter Name:</b> <?php echo htmlspecialchars($voter['firstname'].' '.$voter['lastname']); ?></div>
              <div><b>EPIC Number:</b> <span style="font-family: monospace;"><?php echo htmlspecialchars($voter['voters_id']); ?></span></div>
              <div><b>NID / ডক:</b> <?php echo htmlspecialchars($voter['nid_number'] ?? 'WB/2026/09812'); ?></div>
              <div><b>Address:</b> পশ্চিমবঙ্গ, ভারত</div>
            </div>
          </div>
          <div style="margin-top: 12px;">
            <button class="btn-gov-blue" style="padding: 6px 14px;">প্রোফাইল আপডেট করুন</button>
          </div>
        </div>

        <!-- 5. আপনার ভোটকেন্দ্র / বায়োমেট্রিক গেটওয়ে -->
        <div class="portal-card biometric-center-card">
          <div>
            <h3 style="color: var(--accent-blue); margin: 0;"><i class="fa fa-shield"></i> ডিজিটাল ভোট</h3>
            <p style="font-size: 11.5px; color: var(--text-muted); margin-top: 4px;">বায়োমেট্রিক ও এনক্রিপশন সক্রিয়</p>
          </div>
          <div class="fingerprint-btn" onclick="document.getElementById('ballot_area').scrollIntoView({behavior: 'smooth'})">
            <i class="fa fa-hand-o-up"></i>
          </div>
          <div style="font-size: 11px; color: var(--text-muted);">
            আপনার ভোট নির্বাচন করতে নিচে স্ক্রোল করুন
          </div>
        </div>

        <!-- 6. আসন্ন নির্বাচন তথ্য -->
        <div class="portal-card">
          <div>
            <h3>আসন্ন নির্বাচন তথ্য</h3>
            <div style="font-size: 12px; color: var(--text-secondary); line-height: 1.8;">
              <div><b>নির্বাচন:</b> <?php echo htmlspecialchars($e_title); ?></div>
              <div><b>তারিখ:</b> <?php echo $start_txt; ?></div>
              <div><b>সময়:</b> সকাল ৯টা থেকে বিকেল ৫টা</div>
            </div>
          </div>
          <div style="margin-top: 12px;">
            <a href="#ballot_area" style="font-size: 13px; font-weight: 700; color: var(--accent-blue); text-decoration: none;">প্রার্থীদের তালিকা <i class="fa fa-arrow-right"></i></a>
          </div>
        </div>
      </div>

      <!-- ROW 3: QUICK SERVICES ACTIONS -->
      <div class="quick-actions-grid">
        <a href="register.php" class="action-btn-card">
          <div class="action-icon" style="background: #2563eb;"><i class="fa fa-user-plus"></i></div>
          <span>নতুন ভোটার নিবন্ধন</span>
        </a>
        <a href="#voter_details" class="action-btn-card">
          <div class="action-icon" style="background: #10b981;"><i class="fa fa-pencil"></i></div>
          <span>প্রোফাইলে পরিবর্তন</span>
        </a>
        <a href="#ballot_area" class="action-btn-card">
          <div class="action-icon" style="background: #f59e0b;"><i class="fa fa-exchange"></i></div>
          <span>ভোটকেন্দ্র পরিবর্তন</span>
        </a>
        <a href="#ballot_area" class="action-btn-card">
          <div class="action-icon" style="background: #0284c7;"><i class="fa fa-search"></i></div>
          <span>ভোটার তালিকা অনুসন্ধান</span>
        </a>
      </div>

      <!-- BALLOTING SLATE -->
      <div class="ballot-slate-box" id="ballot_area">
        <h3 style="margin-top: 0; font-size: 17px; font-weight: 800; border-bottom: 1px solid var(--card-border); padding-bottom: 12px; color: var(--text-primary);">
          <i class="fa fa-check-square-o text-primary"></i> লাইভ ই-ব্যালট পেপার (Official Secret Ballot)
        </h3>

        <?php
          if(!$election_active){
            echo "
              <div class='text-center' style='padding: 40px 20px;'>
                <div style='font-size: 45px; color: #ef4444; margin-bottom: 10px;'><i class='fa fa-lock'></i></div>
                <h4 style='color: #ef4444; font-weight: 800;'>ভোটগ্রহণ গেটওয়ে সীমাবদ্ধ</h4>
                <p style='color: var(--text-muted); font-size: 14px;'>".$election_msg."</p>
              </div>
            ";
          } else {
            $sql = "SELECT * FROM votes WHERE voters_id = '".$voter['id']."'";
            $vquery = $conn->query($sql);
            if($vquery->num_rows > 0){
              echo "
                <div class='text-center' style='padding: 40px 20px;'>
                  <div style='font-size: 50px; color: #10b981; margin-bottom: 10px;'><i class='fa fa-check-circle'></i></div>
                  <h3 style='font-weight: 800;'>আপনার ভোট সফলভাবে গ্রহণ করা হয়েছে!</h3>
                  <p style='color: var(--text-muted); font-size: 14px;'>আপনার ভোটটি ক্রিপ্টোগ্রাফিক ব্লকচেইন লেজারে সুরক্ষিত রয়েছে।</p>
                  <a href='#view' data-toggle='modal' class='btn-gov-blue' style='margin-top: 10px;'><i class='fa fa-eye'></i> প্রদত্ত ব্যালট দেখুন</a>
                </div>
              ";
            } else {
              ?>
              <form method="POST" id="ballotForm" action="submit_ballot.php">
                <?php
                  include 'includes/slugify.php';
                  $candidate = '';
                  $sql = "SELECT * FROM positions ORDER BY priority ASC";
                  $query = $conn->query($sql);

                  if($query->num_rows > 0){
                    while($row = $query->fetch_assoc()){
                      $sql = "SELECT * FROM candidates WHERE position_id='".$row['id']."'";
                      $cquery = $conn->query($sql);
                      while($crow = $cquery->fetch_assoc()){
                        $slug = slugify($row['description']);
                        $checked = '';
                        if(isset($_SESSION['post'][$slug])){
                          $value = $_SESSION['post'][$slug];
                          if(is_array($value)){
                            foreach($value as $val){ if($val == $crow['id']) $checked = 'checked'; }
                          } else {
                            if($value == $crow['id']) $checked = 'checked';
                          }
                        }
                        
                        $input = ($row['max_vote'] > 1) 
                          ? '<input type="checkbox" class="flat-red '.$slug.'" name="'.$slug."[]".'" value="'.$crow['id'].'" '.$checked.'>' 
                          : '<input type="radio" class="flat-red '.$slug.'" name="'.slugify($row['description']).'" value="'.$crow['id'].'" '.$checked.'>';
                        
                        $image = (!empty($crow['photo'])) ? 'images/'.$crow['photo'] : 'images/profile.jpg';
                        
                        $candidate .= '
                          <div class="candidate-row-box">
                            '.$input.'
                            <img src="'.$image.'" style="width:48px; height:48px; border-radius:10px; margin:0 15px; object-fit:cover;">
                            <span style="font-weight:700; font-size:15px; flex-grow:1; color: var(--text-primary);">'.htmlspecialchars($crow['firstname'].' '.$crow['lastname']).'</span>
                            <button type="button" class="btn btn-default btn-xs platform" data-platform="'.htmlspecialchars($crow['platform']).'" data-fullname="'.htmlspecialchars($crow['firstname'].' '.$crow['lastname']).'" style="border-radius:6px;"><i class="fa fa-info-circle"></i> ইশতেহার</button>
                          </div>
                        ';
                      }

                      $instruct = ($row['max_vote'] > 1) ? 'সর্বোচ্চ <b>'.$row['max_vote'].'</b> জন প্রার্থীকে নির্বাচন করতে পারেন' : 'শুধুমাত্র <b>১</b> জন প্রার্থী নির্বাচন করুন';

                      echo '
                        <div style="margin-bottom: 25px;">
                          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                            <h4 style="margin:0; font-weight:800; color:var(--accent-blue);">'.htmlspecialchars($row['description']).'</h4>
                            <small style="color:var(--text-muted);">'.$instruct.'</small>
                          </div>
                          '.$candidate.'
                        </div>
                      ';
                      $candidate = '';
                    }
                  } else {
                    echo "
                      <div class='text-center' style='padding: 30px;'>
                        <p style='color: var(--text-muted); font-size: 14px;'>অনুগ্রহ করে অ্যাডমিন প্যানেল (Category/Positions & Candidate Slate) থেকে পদ ও প্রার্থী যুক্ত করুন।</p>
                      </div>
                    ";
                  }
                ?>
                
                <div class="text-center" style="margin-top: 25px;">
                  <button type="button" class="btn btn-default" id="preview" style="border-radius:8px; padding:9px 20px; font-weight:700; margin-right:10px;"><i class="fa fa-file-text-o"></i> প্রিভিউ</button>
                  <button type="submit" class="btn-gov-blue" style="background: #10b981; padding: 10px 25px;" name="vote"><i class="fa fa-check-square-o"></i> ভোট সাবমিট করুন</button>
                </div>
              </form>
              <?php
            }
          }
        ?>
      </div>
    </main>

  </div>

  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/ballot_modal.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>

<!-- Theme Switching & Ballot Script -->
<script>
  const themeBtn = document.getElementById('themeBtn');
  const themeIco = document.getElementById('themeIco');
  const themeLbl = document.getElementById('themeLbl');

  function applyTheme(isDark) {
    if (isDark) {
      document.body.classList.add('dark-mode');
      themeIco.className = 'fa fa-sun-o';
      themeLbl.innerText = 'লাইট মোড';
      localStorage.setItem('voter_portal_theme', 'dark');
    } else {
      document.body.classList.remove('dark-mode');
      themeIco.className = 'fa fa-moon-o';
      themeLbl.innerText = 'ডার্ক মোড';
      localStorage.setItem('voter_portal_theme', 'light');
    }
  }

  if (localStorage.getItem('voter_portal_theme') === 'dark') {
    applyTheme(true);
  }

  themeBtn.addEventListener('click', () => {
    applyTheme(!document.body.classList.contains('dark-mode'));
  });

  $(function(){
    $('.content').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass: 'iradio_flat-green'
    });

    $(document).on('click', '.platform', function(e){
      e.preventDefault();
      $('#platform').modal('show');
      var platform = $(this).data('platform');
      var fullname = $(this).data('fullname');
      $('.candidate').html(fullname);
      $('#plat_view').html(platform);
    });

    $('#preview').click(function(e){
      e.preventDefault();
      var form = $('#ballotForm').serialize();
      if(form == ''){
        alert('অনুগ্রহ করে অন্তত একজন প্রার্থীকে ভোট দিন');
      } else {
        $.ajax({
          type: 'POST',
          url: 'preview.php',
          data: form,
          dataType: 'json',
          success: function(response){
            if(response.error){
              alert(response.message);
            } else {
              $('#preview_modal').modal('show');
              $('#preview_body').html(response.list);
            }
          }
        });
      }
    });
  });
</script>
</body>
</html>