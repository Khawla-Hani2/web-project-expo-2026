//Reema Ali Alqarni
<?php
// save_visitor.php - حفظ بيانات الزائر وإرسال إيميل ترحيبي بنفس تصميم signup.php

session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// استيراد مكتبات PHPMailer
require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// دالة إرسال الإيميل
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
        error_log("Email failed: " . $mail->ErrorInfo);
        return false;
    }
}

// اتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "", "expo2026");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit();
}

// قراءة البيانات
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(["success" => false, "message" => "No data received"]);
    exit();
}

// التحقق من الحقول المطلوبة
$required = ['visitor_id', 'full_name', 'email', 'phone'];
foreach ($required as $field) {
    if (empty($input[$field])) {
        echo json_encode(["success" => false, "message" => "Missing field: $field"]);
        exit();
    }
}

// تنظيف البيانات
$visitor_id   = $conn->real_escape_string($input['visitor_id']);
$full_name    = $conn->real_escape_string($input['full_name']);
$email        = $conn->real_escape_string($input['email']);
$phone        = $conn->real_escape_string($input['phone']);
$organization = isset($input['organization']) ? $conn->real_escape_string($input['organization']) : null;
$position     = isset($input['position']) ? $conn->real_escape_string($input['position']) : null;
$attendance   = isset($input['attendance']) ? $conn->real_escape_string($input['attendance']) : 'yes';

// حفظ في قاعدة البيانات
$sql = "INSERT INTO visitors (visitor_id, full_name, email, phone, organization, position, attendance) 
        VALUES ('$visitor_id', '$full_name', '$email', '$phone', '$organization', '$position', '$attendance')";

if ($conn->query($sql) === TRUE) {
    
    // ==================== إرسال إيميل ترحيبي ====================
    $subject = 'مرحباً بك في EXPO IAU 2026 | Welcome to EXPO IAU 2026';
    
    // محتوى الإيميل (نفس تصميم signup.php مع تعديلات للزائر)
    $messageHTML = '<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0; background:#F5F5F5; font-family: Arial, sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#F5F5F5; padding: 30px 0;">
    <tr>
      <td align="center">
        <table width="560" cellpadding="0" cellspacing="0"
               style="background:#ffffff; border-radius:16px; overflow:hidden; box-shadow: 0 8px 30px rgba(0,0,0,0.1);">
          <tr>
            <td style="background:#632949; padding:0;">
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="background:#632949; padding:20px; text-align:center;">
                    <p style="margin:0; color:#fff; font-size:22px; font-weight:bold; letter-spacing:1px;">EXPO IAU 2026</p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 36px 40px; direction:rtl; text-align:right;">
              <h2 style="color:#632949; font-size:24px; margin:0 0 8px 0;">أهلاً ' . $full_name . '!</h2>
              <p style="color:#555; font-size:15px; margin:0 0 28px 0; line-height:1.7;">
                يسعدنا انضمامك كـ <strong style="color:#632949;">زائر</strong> في <strong style="color:#632949;">EXPO IAU 2026</strong>.
                تم تسجيل بياناتك بنجاح، وفيما يلي ملخص معلوماتك.
              </p>
              <table width="100%" cellpadding="0" cellspacing="0"
                     style="background:#fdf7fa; border:1px solid #e8d0dc; border-radius:12px; margin-bottom:28px;">
                <tr>
                  <td style="padding:14px 20px; border-bottom:1px solid #e8d0dc;">
                    <span style="color:#888; font-size:13px;">الاسم الكامل</span><br>
                    <strong style="color:#222; font-size:15px;">' . $full_name . '</strong>
                  </td>
                </tr>
                <tr>
                  <td style="padding:14px 20px; border-bottom:1px solid #e8d0dc;">
                    <span style="color:#888; font-size:13px;">البريد الإلكتروني</span><br>
                    <strong style="color:#222; font-size:15px;">' . $email . '</strong>
                  </td>
                </tr>
                <tr>
                  <td style="padding:14px 20px; border-bottom:1px solid #e8d0dc;">
                    <span style="color:#888; font-size:13px;">رقم الجوال</span><br>
                    <strong style="color:#222; font-size:15px;">' . $phone . '</strong>
                  </td>
                </tr>
                ' . ($organization ? '<tr>
                  <td style="padding:14px 20px; border-bottom:1px solid #e8d0dc;">
                    <span style="color:#888; font-size:13px;">جهة العمل</span><br>
                    <strong style="color:#222; font-size:15px;">' . $organization . '</strong>
                  </td>
                </tr>' : '') . '
                ' . ($position ? '<tr>
                  <td style="padding:14px 20px; border-bottom:1px solid #e8d0dc;">
                    <span style="color:#888; font-size:13px;">المنصب</span><br>
                    <strong style="color:#222; font-size:15px;">' . $position . '</strong>
                  </td>
                </tr>' : '') . '
                <tr>
                  <td style="padding:14px 20px;">
                    <span style="color:#888; font-size:13px;">الدور</span><br>
                    <span style="display:inline-block; background:#632949; color:#fff;
                                 padding:4px 16px; border-radius:20px; font-size:13px; margin-top:4px;">زائر</span>
                  </td>
                </tr>
              </table>
              
              <div style="text-align:center; margin-bottom:28px;">
                <a href="https://yoursite.com/index.html"
                   style="display:inline-block; background:#632949; color:#ffffff;
                          padding:14px 40px; border-radius:12px; font-size:15px; font-weight:bold; text-decoration:none;">
                  زيارة الموقع
                </a>
              </div>
              
              <hr style="border:none; border-top:1px solid #eee; margin-bottom:20px;">
              
              <p style="color:#888; font-size:13px; line-height:1.8; direction:ltr; text-align:left;">
                <strong style="color:#632949;">Welcome ' . $full_name . '!</strong><br>
                Your visitor registration has been confirmed for EXPO IAU 2026.<br>
                Role: <strong>Visitor</strong>
              </p>
            </td>
           </tr>
           <tr>
            <td style="background:#632949; padding:18px 30px; text-align:center;">
              <p style="color:#e8c8d8; font-size:12px; margin:0;">
                Vice Deanship of Scientific Research and Innovation | وكالة الكلية للبحث العلمي والابتكار
              </p>
              <p style="color:#c9a0b8; font-size:11px; margin:6px 0 0 0;">هذا إيميل تلقائي، يرجى عدم الرد عليه.</p>
            </td>
           </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>';
    
    $messageText = "Welcome $full_name - Your visitor registration for EXPO IAU 2026 has been confirmed. Role: Visitor";
    
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