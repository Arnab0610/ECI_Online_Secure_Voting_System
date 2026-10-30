<?php
	session_start();
	
	// সব সেশন ভেরিয়েবল খালি করা
	$_SESSION = array();

	// সেশন কুকি ডিলিট করা
	if (ini_get("session.use_cookies")) {
	    $params = session_get_cookie_params();
	    setcookie(session_name(), '', time() - 42000,
	        $params["path"], $params["domain"],
	        $params["secure"], $params["httponly"]
	    );
	}

	session_destroy();

	// ক্যাশ ক্লিয়ার হেডার
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header('location: index.php');
	exit();
?>