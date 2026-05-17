<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "expo2026"
);

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$session_id =
$data['session_id'];

$project_name =
$data['project_name'];

$department =
$data['department'];

$track =
$data['track'];

$final_score =
$data['final_score'];

$feedback =
$data['feedback'];

$certificate_file =
"certificates/" .
$session_id .
".pdf";

$sql = "

INSERT INTO certificates(

session_id,
project_name,
department,
track,
final_score,
feedback,
certificate_file

)

VALUES(

'$session_id',
'$project_name',
'$department',
'$track',
'$final_score',
'$feedback',
'$certificate_file'

)

";

mysqli_query($conn, $sql);

echo "Saved";

?>