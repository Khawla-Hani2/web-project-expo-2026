<?php
declare(strict_types=1);
session_start();
header("Content-Type: application/json");

require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'db.php';

function sendEmailNotification($toEmail, $toName, $subject, $messageHTML, $messageText) {
    $mail = new PHPMailer(true);
    try {
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

        $mail->CharSet   = 'UTF-8';
        $mail->Timeout   = 10;
        $mail->SMTPDebug = 0;

        $mail->setFrom('khawlahani18@gmail.com', 'EXPO IAU 2026');
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $messageHTML;
        $mail->AltBody = $messageText;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

$input = file_get_contents("php://input");
$data  = json_decode($input, true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "No data received"]);
    exit();
}

$firstName = trim($data['firstName'] ?? '');
$lastName  = trim($data['lastName'] ?? '');
$email     = trim($data['email'] ?? '');
$password  = $data['password'] ?? '';
$role      = $data['role'] ?? '';
$phone     = trim($data['phone_number'] ?? '');

// All roles allowed — removed restriction

if (!$firstName || !$lastName || !$email || !$password || !$role || !$phone) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Invalid email format"]);
    exit();
}

if (strlen($password) < 6) {
    echo json_encode(["status" => "error", "message" => "Password must be at least 6 characters"]);
    exit();
}

// Check duplicate email
$checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
$checkEmail->bind_param("s", $email);
$checkEmail->execute();
$checkEmail->store_result();
if ($checkEmail->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email is already registered"]);
    exit();
}
$checkEmail->close();

$hashed = password_hash($password, PASSWORD_DEFAULT);
$verificationToken = bin2hex(random_bytes(32));

$stmt = $conn->prepare("
    INSERT INTO users (firstName, lastName, email, password, role, phone_number, is_active, verification_token) 
    VALUES (?, ?, ?, ?, ?, ?, 0, ?)
");
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
    exit();
}

$stmt->bind_param("sssssss", $firstName, $lastName, $email, $hashed, $role, $phone, $verificationToken);

if (!$stmt->execute()) {
    echo json_encode(["status" => "error", "message" => "Insert failed: " . $stmt->error]);
    exit();
}

$userId = $stmt->insert_id;
$stmt->close();

// If role is judge, add to judges table
if ($role === 'judge') {
    $judgeStmt = $conn->prepare("INSERT INTO judges (user_id) VALUES (?)");
    $judgeStmt->bind_param("i", $userId);
    $judgeStmt->execute();
    $judgeStmt->close();
}

$base_url = "http://localhost/my_project";
$activationLink = $base_url . "/verify.php?token=" . $verificationToken;

$subject = 'تفعيل حسابك في EXPO IAU 2026 | Activate your EXPO IAU 2026 account';

$messageHTML = '<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head><meta charset="UTF-8"></head>
<body style="margin:0; padding:0; background:#F5F5F5; font-family: Arial, sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#F5F5F5; padding: 30px 0;">
    <tr><td align="center">
      <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:16px; overflow:hidden; box-shadow: 0 8px 30px rgba(0,0,0,0.1);">
        <tr><td style="background:#632949; padding:20px; text-align:center;">
          <p style="margin:0; color:#fff; font-size:22px; font-weight:bold;">EXPO IAU 2026</p>
        </td></tr>
        <tr><td style="padding: 36px 40px; direction:rtl; text-align:right;">
          <h2 style="color:#632949; font-size:24px; margin:0 0 8px 0;">أهلاً ' . htmlspecialchars($firstName) . '!</h2>
          <p style="color:#555; font-size:15px; margin:0 0 28px 0; line-height:1.7;">
            يسعدنا انضمامك إلى منصة <strong style="color:#632949;">EXPO IAU 2026</strong>.
            تم إنشاء حسابك بنجاح. يرجى الضغط على الزر أدناه لتفعيل حسابك.
          </p>
          <div style="text-align:center; margin-bottom:28px;">
            <a href="' . $activationLink . '" style="display:inline-block; background:#632949; color:#ffffff; padding:14px 40px; border-radius:12px; font-size:15px; font-weight:bold; text-decoration:none;">
              تفعيل الحساب (Activate Account)
            </a>
          </div>
          <hr style="border:none; border-top:1px solid #eee; margin-bottom:20px;">
          <p style="color:#888; font-size:13px; line-height:1.8; direction:ltr; text-align:left;">
            <strong style="color:#632949;">Welcome ' . htmlspecialchars($firstName . ' ' . $lastName) . '!</strong><br>
            Your account has been created successfully. Please click the button above to activate your account.
          </p>
        </td></tr>
        <tr><td style="background:#632949; padding:18px 30px; text-align:center;">
          <p style="color:#e8c8d8; font-size:12px; margin:0;">Vice Deanship of Scientific Research and Innovation</p>
        </td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>';

$messageText = "Welcome $firstName $lastName. Activate your account: $activationLink";

$sent = sendEmailNotification($email, "$firstName $lastName", $subject, $messageHTML, $messageText);

if ($sent) {
    echo json_encode(["status" => "success", "email" => "sent"]);
} else {
    echo json_encode(["status" => "error", "message" => "Account created but email failed to send"]);
}

$conn->close();
?>