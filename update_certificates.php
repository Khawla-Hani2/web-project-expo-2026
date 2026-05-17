<?php

require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

require 'dompdf/autoload.inc.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;

session_start();

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "expo2026"
);

if($conn->connect_error){
    die("Connection failed");
}

// GET ALL JUDGES

$sql = "

SELECT
    id,
    firstName,
    lastName,
    email

FROM users

WHERE role='judge'

";

$result = mysqli_query($conn, $sql);
// CREATE FOLDERS IF NOT EXISTS

if(!is_dir("certificate")){
    mkdir("certificate");
}

if(!is_dir("certificate/judges")){
    mkdir("certificate/judges");
}

// LOOP JUDGES

while($judge = mysqli_fetch_assoc($result)){

    $id = $judge['id'];

    $fullName =

        $judge['firstName']
        ." ".
        $judge['lastName'];

    $email = $judge['email'];

    // CERTIFICATE PAGE

    $certificateURL =

    "http://localhost/Project_web/judge_certificate.php?id=".$id;



    $html = file_get_contents(
        $certificateURL
    );

    // CREATE PDF

    $dompdf = new Dompdf();

    $dompdf->loadHtml($html);

    $dompdf->setPaper(
        'A4',
        'portrait'
    );

    $dompdf->render();


    // PDF PATH

    $pdfFile =

    "certificate/judges/judge_".$id.".pdf";

    // SAVE PDF

    file_put_contents(
        $pdfFile,
        $dompdf->output()
    );

    // SAVE INTO DATABASE
 
    mysqli_query(

        $conn,

        "

        INSERT INTO certificates
        (
            user_id,
            certificate_type,
            certificate_file
        )

        VALUES
        (
            '$id',
            'judge',
            '$pdfFile'
        )

        "

    );


    // SEND EMAIL

    $mail = new PHPMailer(true);

    try{

        $mail->isSMTP();

        $mail->Host =
        'smtp.gmail.com';

        $mail->SMTPAuth = true;

        $mail->Username =
        'khawlahani18@gmail.com';

        $mail->Password =
        'jvomvifgqluacgqq';

        $mail->SMTPSecure =
        PHPMailer::ENCRYPTION_STARTTLS;

        $mail->Port = 587;

        $mail->SMTPOptions = [

            'ssl' => [

                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true

            ]

        ];

        $mail->CharSet = 'UTF-8';



        // FROM

        $mail->setFrom(
            'khawlahani18@gmail.com',
            'EXPO IAU 2026'
        );



        // TO

        $mail->addAddress(
            $email,
            $fullName
        );



        // SUBJECT

        $mail->Subject =
        'Judge Certificate';



        // BODY

        $mail->isHTML(true);

        $mail->Body = "

        <div dir='rtl'>

            <h2>
            شهادة شكر وتقدير
            </h2>

            <p>

            الأستاذ/ة

            <strong>

            ".$fullName."

            </strong>

            </p>

            <p>
            تجدون الشهادة مرفقة
            </p>

        </div>

        ";



        // ATTACH PDF

        $mail->addAttachment($pdfFile);



        // SEND

        $mail->send();



        echo
        "Sent to: "
        .$fullName.
        "<br>";

    }

    catch(Exception $e){

        echo

        "Failed: "
        .$fullName.
        " | "
        .$mail->ErrorInfo.
        "<br>";

    }

}

?>