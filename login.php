<?php
	session_start();
	include 'includes/conn.php';

	if(isset($_POST['login'])){
		$voter = trim($_POST['voter']);
		$password = $_POST['password'];

		$sql = "SELECT * FROM voters WHERE voters_id = '$voter'";
		$query = $conn->query($sql);

		if($query->num_rows < 1){
			$_SESSION['error'] = 'Cannot find voter with the ID';
			header('location: index.php');
			exit();
		}
		else{
			$row = $query->fetch_assoc();
			
			// পাসওয়ার্ড চেক
			if(password_verify($password, $row['password']) || $password == $row['password']){
				
				// ভোটার KYC ভেরিফিকেশন চেক
				if(isset($row['is_verified']) && $row['is_verified'] == 0){
					$_SESSION['error'] = 'Your account KYC is pending approval by the Election Officer!';
					header('location: index.php');
					exit();
				} else {
					$_SESSION['voter'] = $row['id'];
					// সরাসরি হোম ড্যাশবোর্ডে রিডাইরেক্ট
					header('location: home.php');
					exit();
				}
			}
			else{
				$_SESSION['error'] = 'Incorrect password';
				header('location: index.php');
				exit();
			}
		}
	}
	else{
		$_SESSION['error'] = 'Input voter credentials first';
		header('location: index.php');
		exit();
	}
?>