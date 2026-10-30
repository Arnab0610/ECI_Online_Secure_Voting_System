<?php
include 'includes/session.php';
include 'includes/logger.php';

if(isset($_GET['action']) && isset($_GET['id'])){
    $action = $_GET['action'];
    $id = (int)$_GET['id'];
    $admin_id = $_SESSION['admin'];
    $admin_role = $user['role'] ?? 'Admin';

    // ভোটারের তথ্য সংগ্রহ
    $stmt_v = $conn->prepare("SELECT voters_id, firstname, lastname FROM voters WHERE id = ?");
    $stmt_v->bind_param("i", $id);
    $stmt_v->execute();
    $voter_res = $stmt_v->get_result();
    $voter_data = $voter_res->fetch_assoc();
    $stmt_v->close();

    if(!$voter_data){
        $_SESSION['error'] = 'Voter not found!';
        header('location: voter_approval.php');
        exit();
    }

    if($action === 'approve'){
        $stmt = $conn->prepare("UPDATE voters SET is_verified = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        if($stmt->execute()){
            logSecurityEvent($conn, $admin_id, $admin_role, "Approved KYC for voter ID: ".$voter_data['voters_id']);
            $_SESSION['success'] = 'Voter approved successfully!';
        } else {
            $_SESSION['error'] = 'Failed to approve voter.';
        }
        $stmt->close();
    } 
    elseif($action === 'revoke'){
        $stmt = $conn->prepare("UPDATE voters SET is_verified = 0 WHERE id = ?");
        $stmt->bind_param("i", $id);
        if($stmt->execute()){
            logSecurityEvent($conn, $admin_id, $admin_role, "Revoked KYC verification for voter ID: ".$voter_data['voters_id']);
            $_SESSION['success'] = 'Voter verification revoked!';
        } else {
            $_SESSION['error'] = 'Failed to update voter status.';
        }
        $stmt->close();
    } 
    elseif($action === 'delete'){
        $stmt = $conn->prepare("DELETE FROM voters WHERE id = ?");
        $stmt->bind_param("i", $id);
        if($stmt->execute()){
            logSecurityEvent($conn, $admin_id, $admin_role, "Deleted voter profile: ".$voter_data['voters_id']);
            $_SESSION['success'] = 'Voter record deleted successfully!';
        } else {
            $_SESSION['error'] = 'Failed to delete voter record.';
        }
        $stmt->close();
    }
}

header('location: voter_approval.php');
exit();
?>