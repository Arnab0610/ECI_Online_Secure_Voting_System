<?php
$conn = new mysqli('localhost', 'root', '', 'votesystem');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// এই লাইনটি বাংলা ও ইউনিকোড ফন্ট সাপোর্ট নিশ্চিত করে
$conn->set_charset("utf8mb4");
?>