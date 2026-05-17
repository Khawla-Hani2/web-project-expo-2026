<?php
session_start();

// Include your centralized database connection
include 'db.php';

// If user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// ==========================================
// HANDLE FORM SUBMISSION (SAVE CHANGES)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone_number'] ?? '';
    $photoPath = null;

    // 1. Handle Image Upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES["profile_photo"]["name"]);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $targetFile)) {
            $photoPath = $targetFile;
        }
    }

    // 2. Update Database
    if ($photoPath) {
        $stmt = $conn->prepare("UPDATE users SET email=?, phone_number=?, profile_photo=? WHERE id=?");
        $stmt->bind_param("sssi", $email, $phone, $photoPath, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET email=?, phone_number=? WHERE id=?");
        $stmt->bind_param("ssi", $email, $phone, $user_id);
    }

    if ($stmt->execute()) {
        $message = "success";
    } else {
        $message = "error";
    }
}

// ==========================================
// FETCH CURRENT USER DATA TO DISPLAY ON PAGE
// ==========================================
$stmt = $conn->prepare("SELECT firstName, lastName, email, phone_number, role, profile_photo FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$fullName = htmlspecialchars($user['firstName'] . " " . $user['lastName']);
$email = htmlspecialchars($user['email']);
$phone = htmlspecialchars($user['phone_number']);
$role = htmlspecialchars($user['role']);
$photoUrl = !empty($user['profile_photo']) ? $user['profile_photo'] : 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png';
?>

<?php include 'header.php'; ?>

<style>
    :root {
        --primary: #632949;
        --primary-hover: #4e1f38;
        --gold: #8B6E4E;
        --gold-hover: #7a6044;
        --bg: #f3f4f6;
        --card-bg: #ffffff;
        --text-main: #1f2937;
        --text-label: #4b5563;
        --text-muted: #6b7280;
        --border: #d1d5db;
        --input-bg: #ffffff;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        --radius: 16px;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', 'Noto Sans Arabic', -apple-system, BlinkMacSystemFont, sans-serif;
        background-color: var(--bg);
        color: var(--text-main);
        line-height: 1.6;
        min-height: 100vh;
        padding: 40px 20px;
    }

    /* RTL layout support */
    [dir="rtl"] .container {
        direction: rtl;
    }
    [dir="ltr"] .container {
        direction: ltr;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 24px;
        align-items: start;
    }

    @media (max-width: 900px) {
        .container {
            grid-template-columns: 1fr;
        }
    }

    .card {
        background: var(--card-bg);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 32px;
        margin-bottom: 24px;
    }

    .card:last-child {
        margin-bottom: 0;
    }

    .card-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 24px;
    }

    [dir="rtl"] .card-title {
        text-align: right;
    }
    [dir="ltr"] .card-title {
        text-align: left;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }

    .field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .field.full-width {
        grid-column: 1 / -1;
    }

    @media (max-width: 600px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }

    .field label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-label);
    }

    [dir="rtl"] .field label {
        text-align: right;
    }
    [dir="ltr"] .field label {
        text-align: left;
    }

    .field input {
        padding: 12px 16px;
        border: 1.5px solid var(--border);
        border-radius: 10px;
        font-size: 0.95rem;
        color: var(--text-main);
        background: var(--input-bg);
        transition: border-color 0.2s, box-shadow 0.2s;
        font-family: inherit;
    }

    [dir="rtl"] .field input {
        text-align: right;
        direction: rtl;
    }
    [dir="ltr"] .field input {
        text-align: left;
        direction: ltr;
    }

    .field input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(99, 41, 73, 0.1);
    }

    .field input[readonly] {
        background-color: #f9fafb;
        cursor: default;
    }

    .field input::placeholder {
        color: #9ca3af;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 28px;
        border: none;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        font-family: inherit;
    }

    [dir="rtl"] .btn {
        float: left;
    }
    [dir="ltr"] .btn {
        float: right;
    }

    .btn-primary {
        background-color: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background-color: var(--primary-hover);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(99, 41, 73, 0.25);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .card::after {
        content: "";
        display: table;
        clear: both;
    }

    .profile-card {
        text-align: center;
        position: sticky;
        top: 40px;
    }

    .photo-wrapper {
        position: relative;
        width: 140px;
        height: 140px;
        margin: 0 auto 20px;
    }

    .photo-circle {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        overflow: hidden;
        background: #e5e7eb;
        border: 3px solid #f3f4f6;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }

    .photo-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .edit-badge {
        position: absolute;
        bottom: 8px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--primary);
        color: white;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        border: 2px solid white;
        transition: background 0.2s;
    }

    .edit-badge:hover {
        background: var(--primary-hover);
    }

    .profile-name {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 10px;
    }

    .role-badge {
        display: inline-block;
        background: var(--gold);
        color: white;
        padding: 6px 18px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 700;
        margin-bottom: 16px;
        text-transform: capitalize;
    }

    .profile-desc {
        font-size: 0.9rem;
        color: var(--text-muted);
        line-height: 1.7;
        max-width: 280px;
        margin: 0 auto;
    }

    .alert {
        padding: 14px 18px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    input[type="file"] {
        display: none;
    }
</style>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">

<div class="container">
    <!-- LEFT COLUMN -->
    <div class="main-content">

        <!-- PERSONAL INFO -->
        <div class="card">
            <h2 class="card-title" data-en="Personal Information" data-ar="المعلومات الشخصية">Personal Information</h2>

            <?php if ($message === "success"): ?>
                <div class="alert alert-success" data-en="Profile updated successfully!" data-ar="تم تحديث الملف الشخصي بنجاح!">Profile updated successfully!</div>
            <?php elseif ($message === "error"): ?>
                <div class="alert alert-error" data-en="Error updating profile." data-ar="خطأ في تحديث الملف الشخصي.">Error updating profile.</div>
            <?php endif; ?>

            <form id="profileForm" method="POST" enctype="multipart/form-data" action="profile.php">
                <input type="hidden" name="update_profile" value="1">

                <div class="form-grid">
                    <div class="field">
                        <label data-en="Full Name" data-ar="الاسم الكامل">Full Name</label>
                        <input type="text" id="nameInput" value="<?php echo $fullName; ?>" readonly>
                    </div>

                    <div class="field">
                        <label data-en="Email Address" data-ar="عنوان البريد الإلكتروني">Email Address</label>
                        <input type="email" name="email" id="emailInput" value="<?php echo $email; ?>" readonly>
                    </div>

                    <div class="field">
                        <label data-en="Phone Number" data-ar="رقم الهاتف">Phone Number</label>
                        <input type="text" name="phone_number" id="phoneInput" value="<?php echo $phone; ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" data-en="Save Changes" data-ar="حفظ التغييرات">Save Changes</button>
            </form>
        </div>

        <!-- ACCOUNT SECURITY -->
        <div class="card">
            <h2 class="card-title" data-en="Account Security" data-ar="أمان الحساب">Account Security</h2>

            <form id="passwordForm" onsubmit="return false;">
                <div class="form-grid">
                    <div class="field">
                        <label data-en="New Password" data-ar="كلمة المرور الجديدة">New Password</label>
                        <input type="password" id="newPassword" placeholder="********">
                    </div>

                    <div class="field">
                        <label data-en="Confirm Password" data-ar="تأكيد كلمة المرور">Confirm Password</label>
                        <input type="password" id="confirmPassword" placeholder="********">
                    </div>
                </div>

                <button type="button" class="btn btn-primary" onclick="updatePassword()" data-en="Update Password" data-ar="تحديث كلمة المرور">Update Password</button>
            </form>
        </div>

    </div>

    <!-- RIGHT COLUMN: PROFILE SIDEBAR -->
    <div class="card profile-card">
        <div class="photo-wrapper">
            <div class="photo-circle">
                <img id="profilePreview" src="<?php echo $photoUrl; ?>" alt="Profile Photo">
            </div>

            <input type="file" name="profile_photo" id="photoInput" accept="image/*" onchange="previewImage(event)" form="profileForm">

            <div class="edit-badge" onclick="document.getElementById('photoInput').click();" data-en="Edit" data-ar="تعديل">
                Edit
            </div>
        </div>

        <h1 class="profile-name" id="displayName"><?php echo $fullName; ?></h1>

        <div class="role-badge"><?php echo ucfirst($role); ?></div>

        <p class="profile-desc" data-en="Manage your EXPO IAU profile, update your contact details, and keep your account secure before the exhibition days." data-ar="أدر ملفك الشخصي في EXPO IAU، وقم بتحديث بيانات الاتصال الخاصة بك، وحافظ على أمان حسابك قبل أيام المعرض.">
            Manage your EXPO IAU profile, update your contact details, and keep your account secure before the exhibition days.
        </p>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
    // Image preview before upload
    function previewImage(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profilePreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    // Password update demo
    function updatePassword() {
        const newPass = document.getElementById('newPassword').value;
        const confirmPass = document.getElementById('confirmPassword').value;

        if (!newPass || !confirmPass) {
            alert(currentLang === 'ar' ? 'يرجى ملء كلا حقلي كلمة المرور.' : 'Please fill in both password fields.');
            return;
        }

        if (newPass !== confirmPass) {
            alert(currentLang === 'ar' ? 'كلمات المرور غير متطابقة.' : 'Passwords do not match.');
            return;
        }

        alert(currentLang === 'ar' ? 'تم تحديث كلمة المرور بنجاح!' : 'Password updated successfully!');
        document.getElementById('newPassword').value = '';
        document.getElementById('confirmPassword').value = '';
    }

    // Language handling
    let currentLang = localStorage.getItem("lang") || "ar";

    function applyPageLanguage(lang) {
        document.documentElement.dir = (lang === "ar") ? "rtl" : "ltr";
        document.documentElement.lang = (lang === "ar") ? "ar" : "en";

        document.querySelectorAll("[data-en][data-ar]").forEach(function(el) {
            const text = el.getAttribute("data-" + lang);
            if (text !== null) {
                if (el.tagName === 'INPUT') {
                    el.placeholder = text;
                } else {
                    el.textContent = text;
                }
            }
        });
    }

    function init() { 
        applyPageLanguage(currentLang); 
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init);
    } else {
        init();
    }

    window.addEventListener("expoLangChanged", function(e) {
        const lang = (e.detail && e.detail.lang) ? e.detail.lang : "ar";
        currentLang = lang;
        applyPageLanguage(lang);
    });
</script>