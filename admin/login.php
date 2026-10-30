<?php
session_start();
include 'includes/conn.php'; // আপনার ডেটাবেস কানেকশন ফাইল

if(isset($_POST['login'])){
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // SQL Injection রোধ করতে Prepared Statement ব্যবহার
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1){
        $row = $result->fetch_assoc();
        
        // পাসওয়ার্ড ভেরিফিকেশন (যদি ডেটাবেসে password_hash ব্যবহার করে থাকেন)
        // অথবা সাধারণ প্লেইন টেক্সট তুলনা (প্রজেক্ট অনুযায়ী পরিবর্তন করে নেবেন)
        if(password_verify($password, $row['password']) || $password == $row['password']){
            $_SESSION['admin'] = $row['id'];
            
            // সিকিউর সেশন রিজেনারেট করা (Session Fixation Attack রোধ করতে)
            session_regenerate_id(true);
            
            header('location: home.php');
            exit();
        } else {
            $_SESSION['error'] = 'ভুল পাসওয়ার্ড দেওয়া হয়েছে!';
            header('location: index.php');
            exit();
        }
    } else {
        $_SESSION['error'] = 'এই নামের কোনো অ্যাডমিন পাওয়া যায়নি!';
        header('location: index.php');
        exit();
    }
}
else{
    header('location: index.php');
    exit();
}
?>