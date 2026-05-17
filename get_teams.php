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

projects.id,
projects.title,
projects.track,
projects.supervisor,

ROUND(
    AVG(evaluations.total_score),
    2
) AS final_score,

GROUP_CONCAT(
    project_members.member_name
    SEPARATOR ', '
) AS members

FROM projects

LEFT JOIN evaluations
ON projects.id = evaluations.project_id

LEFT JOIN project_members
ON projects.id = project_members.project_id

GROUP BY projects.id

ORDER BY final_score DESC

";

$result = mysqli_query($conn, $sql);

$teams = [];

while($row = mysqli_fetch_assoc($result)){

    $teams[] = $row;

}

echo json_encode($teams);

?>