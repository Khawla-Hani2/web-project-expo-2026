<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');


require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

        $mail->setFrom('khawlahani18@gmail.com', 'EXPO 2026');
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $messageHTML;
        $mail->AltBody = $messageText;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email failed: " . $mail->ErrorInfo);
        return false;
    }
}

require 'db.php';
$input = json_decode(file_get_contents("php://input"), true);

// التحقق من الحقول المطلوبة
$required = ['full_name', 'email', 'phone'];
foreach ($required as $field) {
    if (empty($input[$field])) {
        echo json_encode(["success" => false, "message" => "Missing field: $field"]);
        exit();
    }
}

// تنظيف البيانات
$full_name = $conn->real_escape_string($input['full_name']);
$email        = $conn->real_escape_string($input['email']);
$phone        = $conn->real_escape_string($input['phone']);
$visitor_id = 'VIS' . rand(1000,9999);
$organization = isset($input['organization']) ? $conn->real_escape_string($input['organization']) : null;
$position     = isset($input['position']) ? $conn->real_escape_string($input['position']) : null;
$attendance   = isset($input['attendance']) ? $conn->real_escape_string($input['attendance']) : 'yes';
$qr_code      = isset($input['qr_code']) ? $conn->real_escape_string($input['qr_code']) : null;

// حفظ في قاعدة البيانات
$sql = "INSERT INTO visitors (visitor_id, full_name, email, phone, organization, position, attendance, qr_code) 
        VALUES ('$visitor_id', '$full_name', '$email', '$phone', '$organization', '$position', '$attendance', '$qr_code')";

if ($conn->query($sql) === TRUE) {
    // ========== إرسال إيميل ترحيبي للزائر ==========
    $subject = 'مرحباً بك في EXPO 2026 | Welcome to EXPO 2026';
    
    // إنشاء رابط QR code (للاستخدام في الإيميل)
    $qr_url = "https://yourdomain.com/visitor_qr.php?visitor_id=" . urlencode($visitor_id);
    
    $messageHTML = '<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head><meta charset="UTF-8"></head>
<body style="margin:0; padding:0; background:#F5F5F5; font-family: Arial, sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#F5F5F5; padding: 30px 0;">
    <tr><td align="center">
        <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:16px; overflow:hidden; box-shadow: 0 8px 30px rgba(0,0,0,0.1);">
          <tr>
            <td style="background:#632949; padding:20px; text-align:center;">
              <p style="margin:0; color:#fff; font-size:22px; font-weight:bold;">EXPO IAU 2026</p>
             </td>
           </tr>
           <tr>
            <td style="padding: 36px 40px; direction:rtl; text-align:right;">
              <h2 style="color:#632949; font-size:24px;">أهلاً ' . $full_name . '!</h2>
              <p style="color:#555; font-size:15px; line-height:1.7;">
                يسعدنا انضمامك إلى <strong style="color:#632949;">EXPO IAU 2026</strong> كزائر.
                تم تسجيل بياناتك بنجاح، وفيما يلي ملخص معلوماتك.
              </p>
              <table width="100%" style="background:#fdf7fa; border:1px solid #e8d0dc; border-radius:12px; margin-bottom:28px;">
                <tr><td style="padding:14px 20px;"><span style="color:#888;">الاسم الكامل</span><br><strong>' . $full_name . '</strong></td></tr>
                <tr><td style="padding:14px 20px;"><span style="color:#888;">البريد الإلكتروني</span><br><strong>' . $email . '</strong></td></tr>
                <tr><td style="padding:14px 20px;"><span style="color:#888;">رقم الجوال</span><br><strong>' . $phone . '</strong></td></tr>
                <tr><td style="padding:14px 20px;"><span style="color:#888;">جهة العمل</span><br><strong>' . ($organization ?: '—') . '</strong></td></tr>
                <tr><td style="padding:14px 20px;"><span style="color:#888;">المنصب</span><br><strong>' . ($position ?: '—') . '</strong></td></tr>
              </table>
              
              <!-- QR Code Section -->
              <div style="text-align:center; margin:25px 0; padding:20px; background:#f9f9f9; border-radius:16px;">
                <p style="color:#632949; font-weight:bold; margin-bottom:15px;">بطاقة الدخول الخاصة بك (QR Code)</p>
                <img src="' . $qr_url . '" alt="QR Code" style="width:150px; height:150px; border:2px solid #632949; border-radius:12px; padding:10px; background:white;">
                <p style="color:#666; font-size:13px; margin-top:15px;">يرجى حفظ هذا الرمز أو طباعته لإبرازه عند دخول المعرض</p>
              </div>
              
              <div style="text-align:center; margin-bottom:28px;">
                <a href="https://yoursite.com/index.html" style="display:inline-block; background:#632949; color:#ffffff; padding:14px 40px; border-radius:12px; text-decoration:none;">زيارة الموقع</a>
              </div>
              <hr style="border-top:1px solid #eee;">
              <p style="color:#888; font-size:13px; direction:ltr; text-align:left;">
                <strong style="color:#632949;">Welcome ' . $full_name . '!</strong><br>
                Your visitor registration has been confirmed for EXPO IAU 2026.<br>
                Please save the QR code above to enter the event.
              </p>
             </td>
           </tr>
           <tr>
            <td style="background:#632949; padding:18px 30px; text-align:center;">
              <p style="color:#e8c8d8; font-size:12px;">Vice Deanship of Scientific Research and Innovation</p>
              <p style="color:#c9a0b8; font-size:11px;">هذا إيميل تلقائي، يرجى عدم الرد عليه.</p>
             </td>
           </tr>
        </table>
     </td></tr>
  </table>
</body>
</html>';
    
    $messageText = "Welcome $full_name - Your visitor registration for EXPO IAU 2026 has been confirmed. Save your QR code to enter the event.";
    
    // إرسال الإيميل
    $emailSent = sendEmailNotification($email, $full_name, $subject, $messageHTML, $messageText);
    
    echo json_encode([
        "success" => true,
        "message" => "Visitor saved successfully",
        "email_sent" => $emailSent
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
}

$conn->close();
?>