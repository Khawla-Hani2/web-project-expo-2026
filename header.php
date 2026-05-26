<?php
/**
 * EXPO2026 — Unified Header
 * Loads main.js ONCE per request via $_SERVER guard.
 * Dispatches expoLangChanged via localStorage monkey-patch.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$role = $_SESSION['role'] ?? 'guest';

// --- Bulletproof guard: $_SERVER persists for the entire HTTP request ---
if (!isset($_SERVER['_EXPO_MAIN_JS_LOADED'])) {
    $_SERVER['_EXPO_MAIN_JS_LOADED'] = false;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="style.css">
<style>
  a.seg { text-decoration: none; }
  a.logoL, a.logoR { text-decoration: none; }
  .logoL img, .logoR img { width: 80px; }
</style>
</head>

<body>

<!-- ===== TOP NAVIGATION BAR ===== -->
<div class="top-strip">
  <div class="logoL"><img src="IAU_logo_white.png" alt="IAU"></div>

  <?php if ($role === 'guest') { ?>
    <a href="Signup.html" class="seg s1"><img src="user_vector.png" alt=""><span id="t1">Sign-up</span></a>
  <?php } else { ?>
    <a href="logout.php" class="seg s1"><img src="user_vector.png" alt=""><span id="t1-logout">Logout</span></a>
  <?php } ?>

  <a href="category.php?cat=health" class="seg s2"><img src="Human_Health.png" alt=""><span id="t2">Health</span></a>
  <a href="category.php?cat=economies" class="seg s3"><img src="Future_Economies.png" alt=""><span id="t3">Economies</span></a>
  <a href="category.php?cat=sustainability" class="seg s4"><img src="Environmental_Sustainability.png" alt=""><span id="t4">Sustainability</span></a>
  <a href="category.php?cat=energy" class="seg s5"><img src="Energy_and_Industry.png" alt=""><span id="t5">Energy</span></a>
  <a href="category.php?cat=education" class="seg s6"><img src="Education_and_Development.png" alt=""><span id="t6">Education</span></a>
  <a href="category.php?cat=research" class="seg s7"><img src="Research_Publications.png" alt=""><span id="t7">Research</span></a>

  <div class="logoR">
    <img src="expo2026_ar_white.png" id="expoLogo" alt="EXPO 2026">
  </div>
</div>

<!-- ===== SUBBAR: Menu + Language ===== -->
<div class="subbar">
  <div class="menu-btn" onclick="toggleMenu(event)">
    <div></div><div></div><div></div>
  </div>
  <div class="lang-btn" onclick="toggleLang()">
    <img src="lang.png" alt="Language">
  </div>
</div>

<!-- ===== SIDE MENU ===== -->
<div class="side-menu" id="menu">
  <div class="close-x" onclick="toggleMenu(event)">×</div>
  <a href="Home.php" id="m1">الرئيسية</a>
  <a href="About.php" id="m2">عن الموقع</a>
  <a href="UPCOMINGEVENTDETAILS.html" id="m3">تفاصيل الفعالية القادمة</a>
  <a href="achievements.html" id="m4">الانجازات</a>
  <a href="EventSchedule.php" id="m5">جدول الفعاليات</a>
  <a href="sponsor.html" id="m6">الرعاة</a>
</div>

<?php if (!$_SERVER['_EXPO_MAIN_JS_LOADED']): ?>
<!-- main.js must run AFTER menu, logo, and buttons exist in the DOM -->
<script src="main.js"></script>
<script>
(function() {
  'use strict';

  // Helper so category.php / projects.php can read current language
  window.expoGetLang = function() {
    return localStorage.getItem("lang") || "ar";
  };

  // Monkey-patch localStorage so every language change broadcasts
  // expoLangChanged. This works with main.js, Home.php inline scripts,
  // or any other code that calls localStorage.setItem('lang', ...).
  var origSetItem = localStorage.setItem.bind(localStorage);
  localStorage.setItem = function(key, value) {
    origSetItem(key, value);
    if (key === 'lang') {
      window.dispatchEvent(new CustomEvent('expoLangChanged', {
        detail: { lang: value }
      }));
    }
  };
})();
</script>
<?php $_SERVER['_EXPO_MAIN_JS_LOADED'] = true; ?>
<?php endif; ?>