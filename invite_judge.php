<?php
session_start();
require 'db.php';

// 1. IMPORT PHPMAILER (Make sure the 'PHPMailer/src/...' folder path is correct!)
require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: Home.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $email = trim($_POST['email']);

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $_SESSION['error_msg'] = "Error: This email is already registered.";
        header("Location: invite_judge_page.php");
        exit();
    }
    $stmt->close();

    $token = bin2hex(random_bytes(32)); 
    $dummy_password = "PENDING"; 
    $role = "judge";
    $is_active = 0;

    $insert_stmt = $conn->prepare("INSERT INTO users (firstName, lastName, email, password, role, is_active, verification_token) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $insert_stmt->bind_param("sssssis", $firstName, $lastName, $email, $dummy_password, $role, $is_active, $token);
    
    // THIS SHOULD ONLY HAPPEN ONCE
    if ($insert_stmt->execute()) {
        
        $invite_link = "http://localhost/expo2026/setup_judge.php?token=" . $token;
        $mail = new PHPMailer(true);

        try {
            // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'khawlahani18@gmail.com';
        $mail->Password   = 'jvomvifgqluacgqq';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

            // Recipients
            $mail->setFrom('khawlahani18@gmail.com', 'EXPO IAU 2026');
            $mail->addAddress($email, $firstName . ' ' . $lastName);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Invitation to Judge EXPO IAU 2026';
            $mail->Body    = "Hello $firstName,<br><br>You have been invited to be a judge for EXPO IAU 2026! Please click the link below to set up your password and activate your account:<br><br><a href='$invite_link'>Set up my account</a>";

            $mail->send();

            // SUCCESS: Save message and redirect
            $_SESSION['success_msg'] = "Success! An email invitation has been sent to " . htmlspecialchars($email);
            header("Location: invite_judge_page.php");
            exit();

        } catch (Exception $e) {
            // EMAIL ERROR: Save message and redirect
            $_SESSION['error_msg'] = "Account created, but email failed to send. Mailer Error: {$mail->ErrorInfo}";
            header("Location: invite_judge_page.php");
            exit();
        }
        
    } else {
        // DATABASE ERROR
        $_SESSION['error_msg'] = "System Error: Could not invite judge.";
        header("Location: invite_judge_page.php");
        exit();
    }
    
    $insert_stmt->close();
} // End of POST request
?>