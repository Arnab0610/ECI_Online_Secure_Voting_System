<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include('../admin/includes/conn.php');

$sql = "SELECT * FROM candidates";
$result = $conn->query($sql);

$candidates = array();

if($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $candidates[] = $row;
    }
    echo json_encode(array(
        "status" => "success",
        "data" => $candidates
    ));
} else {
    echo json_encode(array(
        "status" => "error",
        "message" => "Database-e kono candidate paoa jayni!"
    ));
}
?>