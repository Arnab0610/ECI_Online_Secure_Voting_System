<?php
include 'includes/session.php';
include 'includes/logger.php';

if(isset($_GET['action'])){
    $action = $_GET['action'];
    $admin_id = $_SESSION['admin'];
    $admin_role = $user['role'] ?? 'Admin';

    if($action === 'enable'){
        // ডেমো সিক্রেট কি তৈরি (প্রোডাকশনে Google Authenticator secret বা OTP API ব্যবহার করা যাবে)
        $secret = bin2hex(random_bytes(16));
        $stmt = $conn->prepare("UPDATE admin SET two_factor_secret = ? WHERE id = ?");
        $stmt->bind_param("si", $secret, $admin_id);

        if($stmt->execute()){
            logSecurityEvent($conn, $admin_id, $admin_role, "ENABLED Two-Factor Authentication");
            $_SESSION['success'] = 'Two-Factor Authentication has been successfully enabled!';
        } else {
            $_SESSION['error'] = 'Failed to enable 2FA.';
        }
        $stmt->close();
    } 
    elseif($action === 'disable'){
        $stmt = $conn->prepare("UPDATE admin SET two_factor_secret = NULL WHERE id = ?");
        $stmt->bind_param("i", $admin_id);

        if($stmt->execute()){
            logSecurityEvent($conn, $admin_id, $admin_role, "DISABLED Two-Factor Authentication");
            $_SESSION['success'] = 'Two-Factor Authentication has been disabled.';
        } else {
            $_SESSION['error'] = 'Failed to disable 2FA.';
        }
        $stmt->close();
    }
}

header('location: two_factor.php');
exit();
?>