<?php
$servername = "localhost"; 
$username = "root";
$password = "";
$dbname = "votesystem";

// কানেকশন তৈরি
$conn = new mysqli($servername, $username, $password, $dbname);

// কানেকশন চেক করুন
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>