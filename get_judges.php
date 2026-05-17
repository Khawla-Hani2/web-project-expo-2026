<?php

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "expo2026"
);

if($conn->connect_error){

    die("Connection failed");

}

$sql = "
SELECT
    id,
    firstName,
    lastName,
    email
FROM users
WHERE role='judge'
";

$result = mysqli_query($conn,$sql);

$judges = [];

while($row = mysqli_fetch_assoc($result)){

    $judges[] = $row;

}

echo json_encode($judges);

?>