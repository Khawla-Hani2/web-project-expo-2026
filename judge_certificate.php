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

$id = $_GET['id'];

$sql = "

SELECT

    firstName,

    lastName

FROM users

WHERE id = ?

AND role='judge'

";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i",$id);

$stmt->execute();

$result = $stmt->get_result();

$judge = $result->fetch_assoc();

if(!$judge){

    die("Judge not found");

}

$fullName =

    $judge['firstName']

    ." ".

    $judge['lastName'];

?>

<!DOCTYPE html>

<html lang="ar" dir="rtl">

<head>

<meta charset="UTF-8">

<title>Judge Certificate</title>

<style>

@font-face{

    font-family:
    "Frutiger";

    src:url(
      "FrutigerLTArabic-55Roman.ttf"
    );
}

body{

    margin:0;
    padding:0;
}

.certificate{

    position:relative;

    width:794px;
    height:1123px;

    margin:auto;
}

.bg{

    position:absolute;

    width:100%;
    height:100%;

    top:0;
    left:0;
}



/* اسم المحكم */

.judge-name{

    position:absolute;

    top:505px;

    left:0;

    width:100%;

    text-align:center;

    font-family:"Frutiger";

    font-size:42px;

    font-weight:bold;

    color:#B48E2B;

    z-index:999;

}

</style>

</head>

<body>

<div class="certificate">

    <img

    src="file:///<?php echo str_replace('\\','/',__DIR__); ?>/judge_template.jpg"

    class="bg"

    >



    <div class="judge-name">

        <?php
        echo htmlspecialchars($fullName);
        ?>

    </div>

</div>

</body>

</html>