
<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

include('../admin/includes/conn.php');

$data = json_decode(file_get_contents("php://input"), true);

$voter_id = isset($data['voter_id']) ? $data['voter_id'] : '';
$candidate_id = isset($data['candidate_id']) ? $data['candidate_id'] : '';

if(empty($voter_id) || empty($candidate_id)) {
    echo json_encode(array(
        "status" => "error",
        "message" => "Voter ID ebong Candidate select kora dorkar!"
    ));
    exit;
}

$check_sql = "SELECT * FROM votes WHERE voters_id = '$voter_id'";
$check_res = $conn->query($check_sql);

if($check_res && $check_res->num_rows > 0) {
    echo json_encode(array(
        "status" => "error",
        "message" => "Apni already vote diye ফেলেছেন!"
    ));
    exit;
}

$vote_sql = "INSERT INTO votes (voters_id, candidate_id) VALUES ('$voter_id', '$candidate_id')";
if($conn->query($vote_sql) === TRUE) {
    echo json_encode(array(
        "status" => "success",
        "message" => "Vote successfully submitted!"
    ));
} else {
    echo json_encode(array(
        "status" => "error",
        "message" => "Vote submit korte somoshya hocche: " . $conn->error
    ));
}
?>