<?php
  include 'includes/session.php';

  if(isset($_POST['add'])){
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $position = $_POST['position'];
    $constituency = $_POST['constituency'];
    $platform = trim($_POST['platform']);
    
    // প্রার্থীর আসল ছবি আপলোড
    $filename = $_FILES['photo']['name'];
    if(!empty($filename)){
      $ext = pathinfo($filename, PATHINFO_EXTENSION);
      $photo_new_name = 'cand_' . time() . '_' . rand(100, 999) . '.' . $ext;
      move_uploaded_file($_FILES['photo']['tmp_name'], '../images/' . $photo_new_name);
    } else {
      $photo_new_name = 'profile.jpg';
    }

    // প্রার্থীর দলীয় প্রতীক আপলোড
    $symbolname = $_FILES['symbol']['name'];
    if(!empty($symbolname)){
      $sym_ext = pathinfo($symbolname, PATHINFO_EXTENSION);
      $symbol_new_name = 'sym_' . time() . '_' . rand(100, 999) . '.' . $sym_ext;
      move_uploaded_file($_FILES['symbol']['tmp_name'], '../images/' . $symbol_new_name);
    } else {
      $symbol_new_name = 'symbol_demo.png';
    }

    // ডেটাবেজে নতুন ফিল্ডসহ (constituency ও symbol) ইনসার্ট
    $stmt = $conn->prepare("INSERT INTO candidates (position_id, constituency, firstname, lastname, photo, symbol, platform) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $position, $constituency, $firstname, $lastname, $photo_new_name, $symbol_new_name, $platform);
    
    if($stmt->execute()){
      $_SESSION['success'] = 'প্রার্থী সফলভাবে তালিকায় যুক্ত হয়েছে।';
    } else {
      $_SESSION['error'] = $conn->error;
    }
  } else {
    $_SESSION['error'] = 'ফর্মটি পূরণ করুন।';
  }

  header('location: candidates.php');
?>