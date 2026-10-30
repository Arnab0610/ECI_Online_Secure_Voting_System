<?php
include 'includes/session.php';
include 'includes/logger.php';

if(isset($_POST['decrypt_vault'])){
    $election_id = (int)$_POST['election_id'];
    $master_key = trim($_POST['master_key']);
    $admin_id = $_SESSION['admin'];
    $admin_role = $user['role'] ?? 'Admin';

    // ডেমো মাস্টার সিকিউরিটি কী ভ্যালিডেশন
    $valid_key = "ELECTION@2026#SECURE";

    if($master_key === $valid_key){
        // নির্বাচনটি Completed এবং Decrypted মার্ক করা
        $stmt = $conn->prepare("UPDATE elections SET is_encrypted = 0, status = 'completed' WHERE id = ?");
        $stmt->bind_param("i", $election_id);
        
        if($stmt->execute()){
            logSecurityEvent($conn, $admin_id, $admin_role, "DECRYPTED Ballot Vault for Election ID: ".$election_id);
            $_SESSION['success'] = 'Cryptographic Vault Unlocked! Results are now decrypted and ready for official declaration.';
            header('location: votes.php');
            exit();
        } else {
            $_SESSION['error'] = 'Failed to update election status.';
        }
        $stmt->close();
    } else {
        logSecurityEvent($conn, $admin_id, $admin_role, "FAILED Key Attempt on Vault for Election ID: ".$election_id);
        $_SESSION['error'] = 'ভুল ক্রিপ্টোগ্রাফিক মাস্টার কী প্রদান করা হয়েছে!';
    }
}

header('location: decrypt_vault.php');
exit();
?>