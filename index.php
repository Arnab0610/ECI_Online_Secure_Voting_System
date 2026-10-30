<?php
  session_start();
  // ক্যাশ ক্লিয়ার হেডার (যাতে ব্যাক বাটন চাপলে আগের পেজ মেমোরি থেকে লোড না হয়)
  header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>National E-Voting | ডিজিটাল নির্বাচন</title>
  
  <!-- FontAwesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    :root {
      --voter-primary: #ff7a18;
      --voter-glow: rgba(255, 122, 24, 0.45);
      --admin-primary: #00f0ff;
      --admin-glow: rgba(0, 240, 255, 0.45);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Plus Jakarta Sans', 'Hind Siliguri', sans-serif;
    }

    html, body {
      height: 100vh;
      overflow: hidden;
    }

    body {
      background: #020611;
      color: #ffffff;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      position: relative;
    }

    /* Slow Motion Video Background */
    .video-viewport {
      position: fixed;
      inset: 0;
      z-index: 0;
      overflow: hidden;
    }

    .video-viewport video {
      width: 100vw;
      height: 100vh;
      object-fit: cover;
      filter: brightness(0.85) contrast(1.12);
    }

    .viewport-overlay {
      position: fixed;
      inset: 0;
      background: radial-gradient(circle at center, rgba(2, 6, 17, 0.15) 0%, rgba(2, 6, 17, 0.62) 100%);
      z-index: 1;
      pointer-events: none;
    }

    /* Top Navigation */
    .portal-nav {
      position: relative;
      z-index: 10;
      padding: 16px 45px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .brand-wrap {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .brand-logo {
      width: 42px;
      height: 42px;
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #ff9933;
      font-size: 19px;
      box-shadow: 0 0 15px rgba(255,153,51,0.2);
    }

    .brand-title h1 {
      font-size: 16px;
      font-weight: 800;
      letter-spacing: 0.5px;
      color: #ffffff;
    }

    .brand-title p {
      font-size: 11px;
      color: #94a3b8;
    }

    .nav-status {
      display: flex;
      align-items: center;
      gap: 8px;
      background: rgba(16, 185, 129, 0.15);
      border: 1px solid rgba(16, 185, 129, 0.35);
      padding: 6px 15px;
      border-radius: 50px;
      font-size: 12px;
      color: #34d399;
      font-weight: 600;
      backdrop-filter: blur(10px);
    }

    .status-pulse {
      width: 7px;
      height: 7px;
      background: #34d399;
      border-radius: 50%;
      box-shadow: 0 0 10px #34d399;
      animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
      0%, 100% { transform: scale(1); opacity: 1; }
      50% { transform: scale(1.4); opacity: 0.4; }
    }

    /* Main Gateway Hub */
    .gateway-center {
      position: relative;
      z-index: 10;
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .portal-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 30px;
      max-width: 860px;
      width: 100%;
    }

    /* Ultra Smooth 3D Flip Card Container */
    .flip-card-container {
      background-color: transparent;
      perspective: 1400px;
      min-height: 380px;
      width: 100%;
      cursor: pointer;
    }

    .flip-card-inner {
      position: relative;
      width: 100%;
      height: 100%;
      transition: transform 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      transform-style: preserve-3d;
    }

    .flip-card-container:hover .flip-card-inner,
    .flip-card-container.is-flipped .flip-card-inner {
      transform: rotateY(180deg);
    }

    /* Card Sides */
    .flip-card-front, .flip-card-back {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      -webkit-backface-visibility: hidden;
      backface-visibility: hidden;
      border-radius: 24px;
      padding: 34px 28px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      background: rgba(8, 15, 30, 0.78);
      backdrop-filter: blur(25px);
      -webkit-backdrop-filter: blur(25px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
      transition: border-color 0.4s ease, box-shadow 0.4s ease;
    }

    .voter-card .flip-card-front { border-top: 3.5px solid var(--voter-primary); }
    .admin-card .flip-card-front { border-top: 3.5px solid var(--admin-primary); }

    .voter-card:hover .flip-card-front, .voter-card:hover .flip-card-back {
      box-shadow: 0 30px 65px rgba(0,0,0,0.65), 0 0 40px var(--voter-glow);
      border-color: rgba(255, 122, 24, 0.5);
    }

    .admin-card:hover .flip-card-front, .admin-card:hover .flip-card-back {
      box-shadow: 0 30px 65px rgba(0,0,0,0.65), 0 0 40px var(--admin-glow);
      border-color: rgba(0, 240, 255, 0.5);
    }

    .flip-card-back {
      transform: rotateY(180deg);
      background: rgba(10, 20, 38, 0.92);
      align-items: center;
      text-align: center;
      justify-content: center;
      gap: 18px;
    }

    .card-top {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
    }

    .icon-badge {
      width: 48px;
      height: 48px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
    }

    .voter-card .icon-badge {
      background: rgba(255, 122, 24, 0.15);
      color: var(--voter-primary);
      border: 1px solid rgba(255, 122, 24, 0.3);
    }

    .admin-card .icon-badge {
      background: rgba(0, 240, 255, 0.15);
      color: var(--admin-primary);
      border: 1px solid rgba(0, 240, 255, 0.3);
    }

    .category-tag {
      font-family: 'Orbitron', sans-serif;
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      padding: 5px 10px;
      border-radius: 6px;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .voter-card .category-tag { color: var(--voter-primary); }
    .admin-card .category-tag { color: var(--admin-primary); }

    .card-body h3 {
      font-size: 20px;
      font-weight: 700;
      margin-bottom: 8px;
      color: #ffffff;
    }

    .rules-list {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin: 12px 0;
    }

    .rule-item {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      font-size: 13px;
      color: #cbd5e1;
      line-height: 1.4;
    }

    .voter-card .rule-item i { color: var(--voter-primary); margin-top: 3px; }
    .admin-card .rule-item i { color: var(--admin-primary); margin-top: 3px; }

    .flip-hint {
      font-size: 11.5px;
      color: #94a3b8;
      text-align: center;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      font-family: 'Orbitron', sans-serif;
    }

    .portal-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      width: 100%;
      max-width: 260px;
      padding: 14px 20px;
      border-radius: 12px;
      font-weight: 700;
      font-size: 14px;
      text-decoration: none;
      transition: all 0.3s ease;
      font-family: 'Orbitron', sans-serif;
      letter-spacing: 0.5px;
    }

    .voter-btn {
      background: linear-gradient(135deg, #ff7a18, #d94806);
      color: #ffffff;
      box-shadow: 0 5px 20px var(--voter-glow);
    }
    .voter-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255,122,24,0.6); }

    .admin-btn {
      background: linear-gradient(135deg, #00f0ff, #0099b8);
      color: #020611;
      box-shadow: 0 5px 20px var(--admin-glow);
    }
    .admin-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,240,255,0.6); }

    .portal-footer {
      position: relative;
      z-index: 10;
      padding: 12px 30px;
      text-align: center;
      font-size: 11.5px;
      color: #64748b;
      border-top: 1px solid rgba(255, 255, 255, 0.05);
    }

    @media (max-width: 768px) {
      .portal-nav { padding: 12px 20px; }
      .portal-grid { grid-template-columns: 1fr; gap: 16px; }
      html, body { height: auto; overflow-y: auto; }
    }
  </style>
</head>
<body>

  <!-- Slow Motion Video Background -->
  <div class="video-viewport">
    <video id="bg-video" autoplay muted loop playsinline preload="auto">
      <source src="images/A_vibrant_D_D_cartoon_style.mp4" type="video/mp4">
      <source src="videos/election-motion.mp4" type="video/mp4">
    </video>
  </div>
  <div class="viewport-overlay"></div>

  <!-- Header Navigation with National E-Voting Title -->
  <nav class="portal-nav">
    <div class="brand-wrap">
      <div class="brand-logo">
        <i class="fa-solid fa-check-to-slot"></i>
      </div>
      <div class="brand-title">
        <h1>National E-Voting</h1>
        <p>ডিজিটাল নির্বাচন পোর্টাল</p>
      </div>
    </div>
    <div class="nav-status">
      <span class="status-pulse"></span>
      <span>Secure System Live</span>
    </div>
  </nav>

  <!-- Central 3D Flip Hub -->
  <main class="gateway-center">
    <div class="portal-grid">
      
      <!-- 1. Voter Flip Card -->
      <div class="flip-card-container voter-card" onclick="this.classList.toggle('is-flipped')">
        <div class="flip-card-inner">
          
          <!-- Front Side: Rules -->
          <div class="flip-card-front">
            <div>
              <div class="card-top">
                <div class="icon-badge"><i class="fa-solid fa-house-laptop"></i></div>
                <span class="category-tag">Public Guidelines</span>
              </div>
              <div class="card-body" style="margin-top: 14px;">
                <h3>ভোটার নির্দেশিকা ও নিয়ম</h3>
                <div class="rules-list">
                  <div class="rule-item"><i class="fa-solid fa-circle-check"></i><span>সঠিক Voter ID এবং পাসওয়ার্ড দিন।</span></div>
                  <div class="rule-item"><i class="fa-solid fa-circle-check"></i><span>কেওয়াইসি ভেরিফায়েড অ্যাকাউন্ট নিশ্চিত করুন।</span></div>
                  <div class="rule-item"><i class="fa-solid fa-circle-check"></i><span>এনক্রিপ্টেড ও সম্পূর্ণ নিরাপদ গোপন ভোট।</span></div>
                </div>
              </div>
            </div>
            <div class="flip-hint">
              <i class="fa-solid fa-rotate"></i> ক্লিক বা হোভার করে লগইন দেখুন
            </div>
          </div>

          <!-- Back Side: Login Button -->
          <div class="flip-card-back">
            <div class="icon-badge" style="width: 55px; height: 55px; font-size: 24px;"><i class="fa-solid fa-fingerprint"></i></div>
            <h3 style="font-size: 20px;">Voter Gateway</h3>
            <p style="font-size: 13px; color: #94a3b8; max-width: 260px;">আপনার ডিজিটাল ভোট দিতে নিচের বাটনে ক্লিক করুন।</p>
            <a href="voter_secure_login.php" class="portal-btn voter-btn" onclick="event.stopPropagation();">
              <span>DIGITAL VOTER LOGIN</span>
              <i class="fa-solid fa-arrow-right"></i>
            </a>
            <a href="user_login.php" class="portal-btn voter-btn" onclick="event.stopPropagation();">
              <span>VOTER LOGIN</span>
              <i class="fa-solid fa-arrow-right"></i>
            </a>
          </div>

        </div>
      </div>

      <!-- 2. Admin Officer Flip Card -->
      <div class="flip-card-container admin-card" onclick="this.classList.toggle('is-flipped')">
        <div class="flip-card-inner">
          
          <!-- Front Side: Rules -->
          <div class="flip-card-front">
            <div>
              <div class="card-top">
                <div class="icon-badge"><i class="fa-solid fa-user-shield"></i></div>
                <span class="category-tag">Command Desk</span>
              </div>
              <div class="card-body" style="margin-top: 14px;">
                <h3>অ্যাডমিন নির্দেশিকা</h3>
                <div class="rules-list">
                  <div class="rule-item"><i class="fa-solid fa-circle-check"></i><span>অথরাইজড ইউজারনেম ও পাসওয়ার্ড ব্যবহার করুন।</span></div>
                  <div class="rule-item"><i class="fa-solid fa-circle-check"></i><span>লাইভ কাউন্টিং ও সিকিউরিটি লগ মনিটর করুন।</span></div>
                  <div class="rule-item"><i class="fa-solid fa-circle-check"></i><span>সেশন শেষে অবশ্যই লগআউট করুন।</span></div>
                </div>
              </div>
            </div>
            <div class="flip-hint">
              <i class="fa-solid fa-rotate"></i> ক্লিক বা হোভার করে লগইন দেখুন
            </div>
          </div>

          <!-- Back Side: Login Button -->
          <div class="flip-card-back">
            <div class="icon-badge" style="width: 55px; height: 55px; font-size: 24px;"><i class="fa-solid fa-shield-halved"></i></div>
            <h3 style="font-size: 20px;">Officer Command</h3>
            <p style="font-size: 13px; color: #94a3b8; max-width: 260px;">অ্যাডমিন কন্ট্রোল প্যানেলে প্রবেশ করুন।</p>
            <a href="admin/index.php" class="portal-btn admin-btn" onclick="event.stopPropagation();">
              <span>AUTHORIZE ACCESS</span>
              <i class="fa-solid fa-lock"></i>
            </a>
          </div>

        </div>
      </div>

    </div>
  </main>

  <!-- Minimal Footer -->
  <footer class="portal-footer">
    <p>National Digital Democracy Protocol • End-to-End Encrypted • 2026</p>
  </footer>

  <!-- Video Slow-Motion & Click Script -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const video = document.getElementById('bg-video');
      if (video) {
        video.playbackRate = 0.55;
        video.muted = true;
        video.play().catch(e => console.log('Autoplay handled:', e));
      }
    });


    // ব্রাউজার ব্যাক বাটন প্রেস গার্ড (অটো লগআউট)
window.addEventListener('pageshow', function (event) {
  if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
    // ক্যাশ মেমোরি থেকে পেজ ব্যাক হলে সরাসরি লগআউট
    window.location.replace('logout.php');
  }
});

// ব্যাক হিস্ট্রি ট্র্যাপ
history.pushState(null, null, location.href);
window.onpopstate = function () {
  window.location.replace('logout.php');
};
  </script>

</body>
</html>