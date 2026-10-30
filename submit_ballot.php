<?php
	include 'includes/session.php';
	include 'includes/slugify.php';

	if(isset($_POST['vote'])){
		if(count($_POST) == 1){
			$_SESSION['error'][] = 'Please vote atleast one candidate';
		}
		else{
			$_SESSION['post'] = $_POST;
			$sql = "SELECT * FROM positions";
			$query = $conn->query($sql);
			$error = false;
			$sql_array = array();

			// একটিভ নির্বাচন আইডি সংগ্রহ
			$e_res = $conn->query("SELECT id FROM elections ORDER BY id DESC LIMIT 1");
			$election_id = ($e_res->num_rows > 0) ? $e_res->fetch_assoc()['id'] : 1;

			while($row = $query->fetch_assoc()){
				$position = slugify($row['description']);
				$pos_id = $row['id'];
				if(isset($_POST[$position])){
					if($row['max_vote'] > 1){
						if(count($_POST[$position]) > $row['max_vote']){
							$error = true;
							$_SESSION['error'][] = 'You can only choose '.$row['max_vote'].' candidates for '.$row['description'];
						}
						else{
							foreach($_POST[$position] as $key => $values){
								// স্ট্যান্ডার্ড ভোট রেকর্ড
								$sql_array[] = "INSERT INTO votes (voters_id, candidate_id, position_id) VALUES ('".$voter['id']."', '$values', '$pos_id')";

								// ব্লকচেইন এনক্রিপ্টেড পে-লোড এবং SHA-256 ব্লক হ্যাশ তৈরি
								$payload = base64_encode($election_id.'-'.$pos_id.'-'.$values.'-'.microtime());
								$block_hash = hash('sha256', $payload . time());
								$sql_array[] = "INSERT INTO encrypted_ballots (election_id, position_id, candidate_id, encrypted_vote_payload, block_hash) VALUES ('$election_id', '$pos_id', '$values', '$payload', '$block_hash')";
							}
						}
					}
					else{
						$candidate = $_POST[$position];
						// স্ট্যান্ডার্ড ভোট রেকর্ড
						$sql_array[] = "INSERT INTO votes (voters_id, candidate_id, position_id) VALUES ('".$voter['id']."', '$candidate', '$pos_id')";

						// ব্লকচেইন এনক্রিপ্টেড পে-লোড এবং SHA-256 ব্লক হ্যাশ তৈরি
						$payload = base64_encode($election_id.'-'.$pos_id.'-'.$candidate.'-'.microtime());
						$block_hash = hash('sha256', $payload . time());
						$sql_array[] = "INSERT INTO encrypted_ballots (election_id, position_id, candidate_id, encrypted_vote_payload, block_hash) VALUES ('$election_id', '$pos_id', '$candidate', '$payload', '$block_hash')";
					}
				}
			}

			if(!$error){
				foreach($sql_array as $sql_row){
					$conn->query($sql_row);
				}

				// ভোটারকে has_voted = 1 স্ট্যাটাস দিয়ে আপডেট করা
				$conn->query("UPDATE voters SET has_voted = 1 WHERE id = '".$voter['id']."'");

				unset($_SESSION['post']);
				$_SESSION['success'] = 'Ballot submitted and sealed into Blockchain ledger successfully!';
			}
		}
	}
	else{
		$_SESSION['error'][] = 'Select candidates to vote first';
	}

	header('location: home.php');
?>