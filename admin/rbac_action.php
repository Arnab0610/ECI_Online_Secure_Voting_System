<?php
include 'includes/session.php';
include 'includes/logger.php';

if(isset($_GET['role']) && isset($_GET['id'])){
    $role = $_GET['role'];
    $target_id = (int)$_GET['id'];
    $admin_id = $_SESSION['admin'];
    $admin_role = $user['role'] ?? 'Admin';

    // রোল আপডেট করা
    $stmt = $conn->prepare("UPDATE admin SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $role, $target_id);

    if($stmt->execute()){
        logSecurityEvent($conn, $admin_id, $admin_role, "Changed Admin ID $target_id role to $role");
        $_SESSION['success'] = 'Role updated to '.strtoupper(str_replace('_', ' ', $role)).' successfully!';
    } else {
        $_SESSION['error'] = 'Failed to update admin role.';
    }
    $stmt->close();
}

header('location: rbac_roles.php');
exit();
?>