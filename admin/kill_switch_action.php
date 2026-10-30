<?php
include 'includes/session.php';
include 'includes/logger.php';

if(isset($_GET['action']) && isset($_GET['id'])){
    $action = $_GET['action'];
    $id = (int)$_GET['id'];
    $admin_id = $_SESSION['admin'];
    $admin_role = $user['role'] ?? 'Admin';

    if($action === 'freeze'){
        $stmt = $conn->prepare("UPDATE elections SET status = 'paused' WHERE id = ?");
        $stmt->bind_param("i", $id);
        if($stmt->execute()){
            logSecurityEvent($conn, $admin_id, $admin_role, "TRIGGERED EMERGENCY FREEZE for Election ID: ".$id);
            $_SESSION['success'] = 'Emergency Kill Switch Triggered! Voting is now paused.';
        } else {
            $_SESSION['error'] = 'Failed to execute Kill Switch.';
        }
        $stmt->close();
    } 
    elseif($action === 'resume'){
        $stmt = $conn->prepare("UPDATE elections SET status = 'live' WHERE id = ?");
        $stmt->bind_param("i", $id);
        if($stmt->execute()){
            logSecurityEvent($conn, $admin_id, $admin_role, "RESUMED Voting for Election ID: ".$id);
            $_SESSION['success'] = 'Voting has been resumed successfully!';
        } else {
            $_SESSION['error'] = 'Failed to resume election.';
        }
        $stmt->close();
    }
}

header('location: kill_switch.php');
exit();
?>