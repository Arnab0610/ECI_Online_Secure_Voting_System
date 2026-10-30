
<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

include('../admin/includes/conn.php');

$data = json_decode(file_get_contents("php://input"), true);

$voter_id = isset($data['voter_id']) ? trim($data['voter_id']) : '';
$password = isset($data['password']) ? trim($data['password']) : '';

if(empty($voter_id) || empty($password)) {
    echo json_encode(array(
        "status" => "error", 
        "message" => "Voter ID ebong password dewa mandatory!"
    ));
    exit;
}

$sql = "SELECT * FROM voters WHERE voters_id = '$voter_id'";
$result = $conn->query($sql);

if($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Password match
    if($password === $row['password'] || password_verify($password, $row['password']) || md5($password) === $row['password']) {
        
        // 1. Check: Already vote diyeche kina
        $vote_check = $conn->query("SELECT * FROM votes WHERE voters_id = '$voter_id'");
        if($vote_check && $vote_check->num_rows > 0) {
            echo json_encode(array(
                "status" => "already_voted", 
                "message" => "Apni already apnar vote sampurna korechen! Ar login kora jabe na."
            ));
            exit;
        }

        // 2. Security 6-digit OTP
        $otp = rand(100000, 999999);

        echo json_encode(array(
            "status" => "otp_sent", 
            "message" => "Voter credentials verified. Complete OTP verification.",
            "voter_id" => $row['voters_id'],
            "name" => $row['firstname'] . ' ' . $row['lastname'],
            "generated_otp" => (string)$otp
        ));
    } else {
        echo json_encode(array(
            "status" => "error", 
            "message" => "Bhul password diechen!"
        ));
    }
} else {
    echo json_encode(array(
        "status" => "error", 
        "message" => "Ei Voter ID ti registered noy!"
    ));
}
?>
