<?php
// সিকিউরিটি অডিট ইভেন্ট লগ করার ফাংশন
function logSecurityEvent($conn, $user_id, $role, $action) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
    
    // Prepared statement ব্যবহার করে অডিট টেবিলে লগ সেভ করা
    $stmt = $conn->prepare("INSERT INTO audit_logs (user_id, user_role, action, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    if($stmt){
        $stmt->bind_param("issss", $user_id, $role, $action, $ip, $agent);
        $stmt->execute();
        $stmt->close();
    }
}
?>