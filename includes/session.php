<?php
	session_start();
	include 'conn.php';

	// ব্রাউজার ক্যাশ বন্ধ করা
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

	if(!isset($_SESSION['voter']) || trim($_SESSION['voter']) == ''){
		header('location: index.php');
		exit();
	}

	$sql = "SELECT * FROM voters WHERE id = '".$_SESSION['voter']."'";
	$query = $conn->query($sql);
	if($query->num_rows < 1){
		session_destroy();
		header('location: index.php');
		exit();
	}
	$voter = $query->fetch_assoc();
?>