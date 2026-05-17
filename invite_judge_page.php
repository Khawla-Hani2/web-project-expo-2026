<?php
declare(strict_types=1);
session_start(); // MUST be first

// DEBUG MODE: Uncomment the next 3 lines to see what's in your session
// echo "<pre>SESSION: "; print_r($_SESSION); echo "</pre>";
// echo "<pre>COOKIE: "; print_r($_COOKIE); echo "</pre>";
// exit();

// ==========================================
// AUTH CHECK WITH LOOP PREVENTION
// ==========================================

// If no session exists at all, send to login (not home)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// If logged in but not admin, send to home
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: Home.php");
    exit();
}

// ==========================================
// REST OF PAGE
// ==========================================

require 'db.php';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

// Fetch invited judges
$judgesList = [];
$judgeQuery = $conn->query("
    SELECT u.id, u.firstName, u.lastName, u.email, u.is_active, u.created_at 
    FROM users u 
    WHERE u.role = 'judge' 
    ORDER BY u.created_at DESC
");
if ($judgeQuery) {
    while ($row = $judgeQuery->fetch_assoc()) {
        $judgesList[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-en="EXPO IAU | Invite Judge" data-ar="اكسبو جامعة الإمام | دعوة محكم">EXPO IAU | Invite Judge</title>
    <link rel="stylesheet" href="style.css">
    <style>
        :root {
            --main-burgundy: #632949;
            --accent-gold: #836F24;
            --bg-light: #F5F5F5;
            --success-green: #2ed573;
            --error-red: #ff4757;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, sans-serif;
            background: var(--bg-light);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .top-strip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--main-burgundy);
            padding: 0 20px;
            height: 80px;
        }
        .logoL img, .logoR img { height: 60px; width: auto; }
        .seg {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            padding: 8px 16px;
            border-radius: 8px;
            transition: background 0.3s;
        }
        .seg:hover { background: rgba(255,255,255,0.1); }
        .lang-btn {
            position: fixed;
            top: 100px;
            right: 20px;
            width: 44px;
            height: 44px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            border: 2px solid var(--main-burgundy);
        }
        .lang-btn img { width: 24px; height: 24px; }
        html[dir="rtl"] .lang-btn { right: auto; left: 20px; }
        .main-container {
            flex: 1;
            max-width: 1200px;
            width: 100%;
            margin: 40px auto;
            padding: 0 20px;
        }
        .form-card {
            background: #fff;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            max-width: 600px;
            margin: 0 auto 40px;
        }
        .form-card h2 {
            color: var(--main-burgundy);
            font-size: 28px;
            margin-bottom: 8px;
            text-align: center;
        }
        .form-card .subtitle {
            color: #888;
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .input-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }
        @media (max-width: 600px) {
            .input-grid { grid-template-columns: 1fr; }
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: var(--main-burgundy);
            margin-bottom: 6px;
        }
        .form-group input {
            padding: 14px 16px;
            border: 2px solid #e8e8e8;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s;
            outline: none;
        }
        .form-group input:focus {
            border-color: var(--main-burgundy);
            box-shadow: 0 0 0 4px rgba(99, 41, 73, 0.1);
        }
        .form-group input.error {
            border-color: var(--error-red);
            background: #fff5f5;
        }
        .form-group .error-msg {
            color: var(--error-red);
            font-size: 12px;
            margin-top: 4px;
            display: none;
        }
        .form-group.full-width { grid-column: 1 / -1; }
        .submit-btn {
            width: 100%;
            padding: 16px;
            background: var(--main-burgundy);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .submit-btn:hover {
            background: #4a1e35;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 41, 73, 0.3);
        }
        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        .spinner {
            width: 18px;
            height: 18px;
            border: 2px solid #fff;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            display: none;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .toast-container {
            position: fixed;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2000;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }
        .toast {
            padding: 14px 24px;
            border-radius: 12px;
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            animation: slideDown 0.4s ease, fadeOut 0.4s ease 4.6s forwards;
            display: flex;
            align-items: center;
            gap: 10px;
            pointer-events: auto;
            min-width: 300px;
        }
        .toast.success { background: var(--success-green); }
        .toast.error { background: var(--error-red); }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeOut {
            to { opacity: 0; transform: translateY(-20px); }
        }
        .table-card {
            background: #fff;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        }
        .table-card h3 {
            color: var(--main-burgundy);
            font-size: 22px;
            margin-bottom: 24px;
        }
        .judges-table {
            width: 100%;
            border-collapse: collapse;
        }
        .judges-table th {
            color: var(--main-burgundy);
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 16px;
            border-bottom: 2px solid var(--accent-gold);
            text-align: left;
        }
        html[dir="rtl"] .judges-table th { text-align: right; }
        .judges-table td {
            padding: 16px;
            border-bottom: 1px solid #f0f0f0;
            color: #555;
            font-size: 14px;
        }
        .judges-table tr:hover { background: #fafafa; }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }
        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-badge.active {
            background: #d4edda;
            color: #155724;
        }
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #aaa;
        }
        .empty-state svg {
            width: 60px;
            height: 60px;
            margin-bottom: 16px;
            opacity: 0.4;
        }
        .footer-img {
            width: 100%;
            line-height: 0;
        }
        .footer-img img {
            width: 100%;
            height: auto;
            display: block;
        }
    </style>
</head>
<body>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Language Toggle -->
    <div class="lang-btn" onclick="toggleLang()" title="تغيير اللغة / Change Language">
        <img src="lang.png" alt="Language">
    </div>

    <!-- Top Bar -->
    <div class="top-strip">
        <div class="logoL"><img src="IAU_logo_white.png" alt="IAU Logo"></div>
        <a href="Home.php" class="seg" id="backHome" data-en="← Back to Home" data-ar="العودة للرئيسية →">← Back to Home</a>
        <div class="logoR"><img src="expo2026_en_white.png" id="expoLogo" alt="Expo Logo"></div>
    </div>

    <!-- Main Content -->
    <div class="main-container">

        <!-- Invite Form Card -->
        <div class="form-card">
            <h2 data-en="Invite a New Judge" data-ar="دعوة محكم جديد">Invite a New Judge</h2>
            <p class="subtitle" data-en="Send an email invitation to evaluate projects" data-ar="إرسال دعوة بالبريد الإلكتروني لتقييم المشاريع">Send an email invitation to evaluate projects</p>

            <form id="inviteForm" action="invite_judge.php" method="POST" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf); ?>">

                <div class="input-grid">
                    <div class="form-group">
                        <label for="firstName" data-en="First Name" data-ar="الاسم الأول">First Name</label>
                        <input type="text" id="firstName" name="firstName" placeholder="John" required
                               data-en-placeholder="First Name" data-ar-placeholder="الاسم الأول">
                        <span class="error-msg" id="firstNameError">Required</span>
                    </div>

                    <div class="form-group">
                        <label for="lastName" data-en="Last Name" data-ar="اسم العائلة">Last Name</label>
                        <input type="text" id="lastName" name="lastName" placeholder="Doe" required
                               data-en-placeholder="Last Name" data-ar-placeholder="اسم العائلة">
                        <span class="error-msg" id="lastNameError">Required</span>
                    </div>

                    <div class="form-group full-width">
                        <label for="email" data-en="Email Address" data-ar="البريد الإلكتروني">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="judge@example.com" required
                               data-en-placeholder="judge@example.com" data-ar-placeholder="محكم@example.com">
                        <span class="error-msg" id="emailError">Valid email required</span>
                    </div>

                    <div class="form-group full-width">
                        <label for="phone" data-en="Phone Number" data-ar="رقم الجوال">Phone Number</label>
                        <input type="tel" id="phone" name="phone" placeholder="05XXXXXXXX" required
                               pattern="^05\d{8}$"
                               data-en-placeholder="05XXXXXXXX" data-ar-placeholder="05XXXXXXXX">
                        <span class="error-msg" id="phoneError">Must start with 05 + 8 digits</span>
                    </div>
                </div>

                <button type="submit" class="submit-btn" id="submitBtn">
                    <div class="spinner" id="btnSpinner"></div>
                    <span id="btnText" data-en="Send Invitation" data-ar="إرسال الدعوة">Send Invitation</span>
                </button>
            </form>
        </div>

        <!-- Judges Table Card -->
        <div class="table-card">
            <h3 data-en="Invited Judges" data-ar="المحكمين المدعوين">Invited Judges</h3>

            <?php if (empty($judgesList)): ?>
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <p data-en="No judges invited yet" data-ar="لم يتم دعوة محكمين بعد">No judges invited yet</p>
                </div>
            <?php else: ?>
                <table class="judges-table">
                    <thead>
                        <tr>
                            <th data-en="Name" data-ar="الاسم">Name</th>
                            <th data-en="Email" data-ar="البريد">Email</th>
                            <th data-en="Status" data-ar="الحالة">Status</th>
                            <th data-en="Invited" data-ar="تاريخ الدعوة">Invited</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($judgesList as $judge): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($judge['firstName'] . ' ' . $judge['lastName']); ?></strong></td>
                            <td><?php echo htmlspecialchars($judge['email']); ?></td>
                            <td>
                                <?php if ((int)$judge['is_active'] === 1): ?>
                                    <span class="status-badge active">
                                        <span class="status-dot"></span>
                                        <span data-en="Active" data-ar="مفعل">Active</span>
                                    </span>
                                <?php else: ?>
                                    <span class="status-badge pending">
                                        <span class="status-dot"></span>
                                        <span data-en="Pending" data-ar="معلق">Pending</span>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($judge['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </div>

    <!-- Footer -->
    <div class="footer-img">
        <img src="footer.png" alt="Footer">
    </div>

    <script>
        let currentLang = localStorage.getItem("expo_lang") || "en";

        function applyLanguage(lang) {
            const html = document.documentElement;
            html.lang = lang;
            html.dir = lang === "ar" ? "rtl" : "ltr";

            document.querySelectorAll("[data-en][data-ar]").forEach(el => {
                const text = el.getAttribute("data-" + lang);
                if (text) el.innerText = text;
            });

            document.querySelectorAll("[data-en-placeholder][data-ar-placeholder]").forEach(el => {
                const ph = el.getAttribute("data-" + lang + "-placeholder");
                if (ph) el.placeholder = ph;
            });

            const logo = document.getElementById("expoLogo");
            if (logo) {
                logo.src = lang === "ar" ? "expo2026_ar_white.png" : "expo2026_en_white.png";
            }

            localStorage.setItem("expo_lang", lang);
            currentLang = lang;
        }

        function toggleLang() {
            applyLanguage(currentLang === "en" ? "ar" : "en");
        }

        document.addEventListener("DOMContentLoaded", () => applyLanguage(currentLang));

        function showToast(message, type = "success") {
            const container = document.getElementById("toastContainer");
            const toast = document.createElement("div");
            toast.className = `toast ${type}`;
            toast.innerHTML = type === "success" ? `✓ ${message}` : `✕ ${message}`;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 5000);
        }

        const form = document.getElementById("inviteForm");
        const submitBtn = document.getElementById("submitBtn");
        const btnSpinner = document.getElementById("btnSpinner");
        const btnText = document.getElementById("btnText");

        form.addEventListener("submit", function(e) {
            let valid = true;
            const fields = [
                { id: "firstName", error: "firstNameError", check: v => v.length > 0 },
                { id: "lastName", error: "lastNameError", check: v => v.length > 0 },
                { id: "email", error: "emailError", check: v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) },
                { id: "phone", error: "phoneError", check: v => /^05\d{8}$/.test(v) }
            ];

            fields.forEach(field => {
                const input = document.getElementById(field.id);
                const error = document.getElementById(field.error);
                const isValid = field.check(input.value.trim());
                if (!isValid) {
                    input.classList.add("error");
                    error.style.display = "block";
                    valid = false;
                } else {
                    input.classList.remove("error");
                    error.style.display = "none";
                }
            });

            if (!valid) {
                e.preventDefault();
                showToast(currentLang === "ar" ? "يرجى تصحيح الأخطاء" : "Please fix the errors", "error");
                return;
            }

            submitBtn.disabled = true;
            btnSpinner.style.display = "block";
            btnText.innerText = currentLang === "ar" ? "جاري الإرسال..." : "Sending...";
        });

        ["firstName", "lastName", "email", "phone"].forEach(id => {
            const input = document.getElementById(id);
            input.addEventListener("input", () => {
                input.classList.remove("error");
                document.getElementById(id + "Error").style.display = "none";
            });
        });

        <?php if (isset($_SESSION['success_msg'])): ?>
            showToast("<?php echo addslashes($_SESSION['success_msg']); ?>", "success");
            <?php unset($_SESSION['success_msg']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_msg'])): ?>
            showToast("<?php echo addslashes($_SESSION['error_msg']); ?>", "error");
            <?php unset($_SESSION['error_msg']); ?>
        <?php endif; ?>
    </script>
</body>
</html>