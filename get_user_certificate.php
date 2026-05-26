<?php

session_start();

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "expo2026_new"
);

if($conn->connect_error){
    die("Connection failed");
}

$user_id = $_SESSION['user_id'];

$sql = "

SELECT
    certificate_file
FROM certificates
WHERE user_id = '$user_id'
ORDER BY id DESC
LIMIT 1

";

$result = mysqli_query($conn, $sql);

$data = mysqli_fetch_assoc($result);

echo json_encode($data);

?>