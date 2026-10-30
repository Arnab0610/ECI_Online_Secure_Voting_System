<?php
	session_start();
	include 'conn.php';

	// ব্রাউজার ক্যাশ পুরোপুরি বন্ধ করা (যাতে ব্যাক বাটন চাপলে পুরোনো ডেটা না দেখায়)
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

	if(!isset($_SESSION['admin']) || trim($_SESSION['admin']) == ''){
		header('location: index.php');
		exit();
	}

	$sql = "SELECT * FROM admin WHERE id = '".$_SESSION['admin']."'";
	$query = $conn->query($sql);
	if($query->num_rows < 1){
		session_destroy();
		header('location: index.php');
		exit();
	}
	$user = $query->fetch_assoc();
?>