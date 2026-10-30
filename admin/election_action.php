<?php
include 'includes/session.php';
include 'includes/logger.php';

$admin_id = $_SESSION['admin'];
$admin_role = $user['role'] ?? 'Admin';

if(isset($_GET['action'])){
    $action = $_GET['action'];

    // ১. নতুন নির্বাচন তৈরি
    if($action == 'add' && isset($_POST['title'])){
        $title = trim($_POST['title']);
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];

        $stmt = $conn->prepare("INSERT INTO elections (title, start_time, end_time, status, is_encrypted) VALUES (?, ?, ?, 'scheduled', 1)");
        $stmt->bind_param("sss", $title, $start_time, $end_time);
        
        if($stmt->execute()){
            logSecurityEvent($conn, $admin_id, $admin_role, "Created new election: ".$title);
            $_SESSION['success'] = 'Election scheduled successfully!';
        } else {
            $_SESSION['error'] = 'Failed to create election schedule.';
        }
        $stmt->close();
    }

    // ২. স্ট্যাটাস পরিবর্তন
    elseif($action == 'status' && isset($_GET['val']) && isset($_GET['id'])){
        $status = $_GET['val'];
        $id = (int)$_GET['id'];

        $stmt = $conn->prepare("UPDATE elections SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        
        if($stmt->execute()){
            logSecurityEvent($conn, $admin_id, $admin_role, "Updated election ID $id status to $status");
            $_SESSION['success'] = 'Election status updated to '.strtoupper($status).'!';
        } else {
            $_SESSION['error'] = 'Failed to update election status.';
        }
        $stmt->close();
    }

    // ৩. নির্বাচন ডিলিট
    elseif($action == 'delete' && isset($_GET['id'])){
        $id = (int)$_GET['id'];

        $stmt = $conn->prepare("DELETE FROM elections WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if($stmt->execute()){
            logSecurityEvent($conn, $admin_id, $admin_role, "Deleted election schedule ID: $id");
            $_SESSION['success'] = 'Election schedule removed!';
        } else {
            $_SESSION['error'] = 'Failed to remove election.';
        }
        $stmt->close();
    }
}

header('location: election_schedule.php');
exit();
?>