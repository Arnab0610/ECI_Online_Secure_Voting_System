<?php
  include 'includes/session.php';

  if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $position = $_POST['position'];
    $constituency = $_POST['constituency'];
    $platform = trim($_POST['platform']);

    // ডেটাবেজ আপডেট কুয়েরি
    $stmt = $conn->prepare("UPDATE candidates SET firstname = ?, lastname = ?, position_id = ?, constituency = ?, platform = ? WHERE id = ?");
    $stmt->bind_param("ssissi", $firstname, $lastname, $position, $constituency, $platform, $id);

    if($stmt->execute()){
      $_SESSION['success'] = 'প্রার্থীর তথ্য ও বিধানসভা সফলভাবে আপডেট হয়েছে।';
    } else {
      $_SESSION['error'] = $conn->error;
    }
  } else {
    $_SESSION['error'] = 'ফর্মটি পূরণ করুন।';
  }

  header('location: candidates.php');
?>