<?php
  include 'includes/session.php';
  include '../includes/conn.php';
  include 'includes/header.php';

  $selected_election_id = isset($_GET['election_id']) ? (int)$_GET['election_id'] : 0;
  $selected_constituency = $_GET['constituency'] ?? '';
  $cipher_method = "AES-256-CBC";
  $enc_key = hash('sha256', 'ECI_SECRET_BALLOT_MASTER_KEY_2026', true);

  $candidate_tally = [];
  $total_valid_votes = 0;
  $invalid_tampered_votes = 0;
  $election_info = null;

  // নির্বাচন তথ্য সংগ্রহ
  if($selected_election_id > 0){
    $e_stmt = $conn->prepare("SELECT * FROM elections WHERE id = ? LIMIT 1");
    $e_stmt->bind_param("i", $selected_election_id);
    $e_stmt->execute();
    $election_info = $e_stmt->get_result()->fetch_assoc();
  }

  // নির্দিষ্ট নির্বাচন ও বিধানসভার ফলাফল প্রসেসিং
  if($selected_election_id > 0 && !empty($selected_constituency)){
    // ১. শুধুমাত্র নির্দিষ্ট নির্বাচন এবং বিধানসভার এনক্রিপ্ট ব্যালট ফিল্টার
    $stmt = $conn->prepare("SELECT encrypted_vote_payload, block_hash FROM encrypted_ballots WHERE election_id = ? AND constituency = ?");
    $stmt->bind_param("is", $selected_election_id, $selected_constituency);
    $stmt->execute();
    $res = $stmt->get_result();

    // ২. প্রার্থীর তালিকা ম্যাপিং
    $cand_map = [];
    $c_query = $conn->query("SELECT id, firstname, lastname, platform, photo FROM candidates WHERE constituency = '$selected_constituency' OR constituency = 'ALL'");
    while($c = $c_query->fetch_assoc()){
      $cand_map[$c['id']] = $c;
      $candidate_tally[$c['id']] = 0;
    }
    $cand_map[999] = ['id' => 999, 'firstname' => 'NOTA', 'lastname' => '(None of the Above)', 'platform' => 'NOTA', 'photo' => 'profile.jpg'];
    $candidate_tally[999] = 0;

    // ৩. নির্দিষ্ট নির্বাচনের ভোট ডিক্রিপশন
    while($row = $res->fetch_assoc()){
      $payload = $row['encrypted_vote_payload'];
      $expected_hash = hash('sha256', $payload . $selected_constituency);

      if($row['block_hash'] !== $expected_hash){
        $invalid_tampered_votes++;
        continue;
      }

      $raw_data = base64_decode($payload);
      $iv_len = openssl_cipher_iv_length($cipher_method);
      $iv = substr($raw_data, 0, $iv_len);
      $ciphertext = substr($raw_data, $iv_len);

      $decrypted = openssl_decrypt($ciphertext, $cipher_method, $enc_key, 0, $iv);
      if($decrypted){
        $data = json_decode($decrypted, true);
        
        // নিরাপত্তা চেক: পেলোডের নির্বাচন আইডি মিলেছে কিনা
        if(isset($data['election_id']) && (int)$data['election_id'] === $selected_election_id){
          $cand_id = (int)$data['candidate_id'];
          if(isset($candidate_tally[$cand_id])){
            $candidate_tally[$cand_id]++;
          } else {
            $candidate_tally[$cand_id] = 1;
          }
          $total_valid_votes++;
        }
      }
    }
  }

  // ড্রপডাউন ডেটা
  $all_elections = $conn->query("SELECT id, title, status, start_time FROM elections ORDER BY id DESC");
  $constituencies_query = $conn->query("SELECT DISTINCT constituency FROM voter_applications WHERE constituency IS NOT NULL AND constituency != ''");
?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        🔐 নির্বাচন ভিত্তিক ক্রিপ্টোগ্রাফিক ব্যালট ডিক্রিপশন ও ফলাফল
        <small>Independent Election & Constituency Ledger</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> ড্যাশবোর্ড</a></li>
        <li class="active">Tally Votes</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          
          <!-- নির্বাচন ও বিধানসভা ফিল্টার ফর্ম -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">নির্দিষ্ট নির্বাচন ও বিধানসভার ব্যালট বক্স খুলুন</h3>
            </div>
            <div class="box-body">
              <form method="GET" action="tally_encrypted_votes.php" class="form-inline">
                
                <!-- ১. নির্বাচন সিলেক্ট -->
                <div class="form-group" style="margin-right: 15px;">
                  <label style="margin-right: 8px;">নির্বাচন নির্বাচন করুন:</label>
                  <select name="election_id" class="form-control" style="min-width: 260px;" required>
                    <option value="" disabled <?php echo ($selected_election_id == 0) ? 'selected' : ''; ?>>-- নির্বাচন বেছে নিন --</option>
                    <?php while($el = $all_elections->fetch_assoc()): ?>
                      <option value="<?php echo $el['id']; ?>" <?php echo ($selected_election_id == $el['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($el['title']); ?> (<?php echo strtoupper($el['status']); ?>)
                      </option>
                    <?php endwhile; ?>
                  </select>
                </div>

                <!-- ২. বিধানসভা সিলেক্ট -->
                <div class="form-group" style="margin-right: 15px;">
                  <label style="margin-right: 8px;">বিধানসভা ক্ষেত্র:</label>
                  <select name="constituency" class="form-control" style="min-width: 220px;" required>
                    <option value="" disabled <?php echo empty($selected_constituency) ? 'selected' : ''; ?>>-- বিধানসভা বেছে নিন --</option>
                    <?php while($ac = $constituencies_query->fetch_assoc()): ?>
                      <option value="<?php echo htmlspecialchars($ac['constituency']); ?>" <?php echo ($selected_constituency == $ac['constituency']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($ac['constituency']); ?>
                      </option>
                    <?php endwhile; ?>
                  </select>
                </div>

                <button type="submit" class="btn btn-primary btn-flat">
                  <i class="fa fa-key"></i> ব্যালট ডিক্রিপ্ট ও গণনা করুন
                </button>
              </form>
            </div>
          </div>

          <?php if($selected_election_id > 0 && !empty($selected_constituency)): ?>
            <!-- নির্বাচিত নির্বাচনের সামারি -->
            <div class="row">
              <div class="col-md-3">
                <div class="info-box bg-purple">
                  <span class="info-box-icon"><i class="fa fa-bullhorn"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">নির্বাচনের নাম</span>
                    <span class="info-box-number" style="font-size: 15px;"><?php echo htmlspecialchars($election_info['title'] ?? 'N/A'); ?></span>
                  </div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="info-box bg-green">
                  <span class="info-box-icon"><i class="fa fa-check-circle"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">মোট বৈধ ভোট (Decrypted)</span>
                    <span class="info-box-number" style="font-size: 26px;"><?php echo $total_valid_votes; ?></span>
                  </div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="info-box bg-aqua">
                  <span class="info-box-icon"><i class="fa fa-institution"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">বিধানসভা ক্ষেত্র</span>
                    <span class="info-box-number" style="font-size: 15px;"><?php echo htmlspecialchars($selected_constituency); ?></span>
                  </div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="info-box bg-red">
                  <span class="info-box-icon"><i class="fa fa-shield"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">ট্যাম্পার বা অবৈধ ব্যালট</span>
                    <span class="info-box-number" style="font-size: 26px;"><?php echo $invalid_tampered_votes; ?></span>
                  </div>
                </div>
              </div>
            </div>

            <!-- ফলাফল তালিকা ও বিজয়ী হাইলাইট -->
            <div class="box box-solid">
              <div class="box-header with-border">
                <h3 class="box-title">
                  <b><?php echo htmlspecialchars($election_info['title'] ?? ''); ?></b> — প্রার্থী অনুযায়ী ফলাফল তালিকা
                </h3>
              </div>
              <div class="box-body table-responsive">
                <table class="table table-bordered table-striped" style="font-size: 14px;">
                  <thead>
                    <tr style="background: #0f172a; color: #ffffff;">
                      <th style="width: 60px; text-align: center;">SL</th>
                      <th style="width: 80px; text-align: center;">ছবি</th>
                      <th>প্রার্থীর নাম</th>
                      <th>দলীয় প্ল্যাটফর্ম</th>
                      <th style="width: 140px; text-align: center;">প্রাপ্ত ভোট</th>
                      <th style="width: 140px; text-align: center;">শতাংশ (%)</th>
                      <th style="width: 120px; text-align: center;">ফলাফল</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                      $sl = 1;
                      arsort($candidate_tally);
                      $max_vote = ($total_valid_votes > 0) ? reset($candidate_tally) : -1;

                      foreach($candidate_tally as $cand_id => $votes):
                        $c = $cand_map[$cand_id] ?? ['firstname' => 'Unknown', 'lastname' => '', 'platform' => 'N/A', 'photo' => 'profile.jpg'];
                        $pct = ($total_valid_votes > 0) ? round(($votes / $total_valid_votes) * 100, 2) : 0;
                        $photo = (!empty($c['photo']) && file_exists('../images/'.$c['photo'])) ? '../images/'.$c['photo'] : '../images/profile.jpg';
                        $is_winner = ($votes === $max_vote && $votes > 0 && $cand_id != 999);
                    ?>
                      <tr style="<?php echo $is_winner ? 'background-color: #f0fdf4;' : ''; ?>">
                        <td style="text-align: center; font-weight: bold;"><?php echo $sl++; ?></td>
                        <td style="text-align: center;">
                          <img src="<?php echo $photo; ?>" style="width: 45px; height: 45px; border-radius: 6px; object-fit: cover;">
                        </td>
                        <td style="font-weight: 700;">
                          <?php echo htmlspecialchars($c['firstname'].' '.$c['lastname']); ?>
                          <?php if($is_winner): ?>
                            <span class="label label-success" style="margin-left: 8px;"><i class="fa fa-trophy"></i> লিডিং / বিজয়ী</span>
                          <?php endif; ?>
                        </td>
                        <td><span class="label label-info"><?php echo htmlspecialchars($c['platform']); ?></span></td>
                        <td style="text-align: center; font-weight: 800; font-size: 17px; color: #0b63c5;"><?php echo $votes; ?></td>
                        <td style="text-align: center; font-weight: 700;">
                          <div class="progress progress-xs" style="margin-bottom: 3px;">
                            <div class="progress-bar progress-bar-primary" style="width: <?php echo $pct; ?>%"></div>
                          </div>
                          <span><?php echo $pct; ?>%</span>
                        </td>
                        <td style="text-align: center;">
                          <?php if($is_winner): ?>
                            <span class="badge bg-green" style="font-size: 12px; padding: 5px 10px;">WINNER</span>
                          <?php else: ?>
                            <span class="text-muted">-</span>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          <?php endif; ?>

        </div>
      </div>
    </section>
  </div>

  <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>
</body>
</html>