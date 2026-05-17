<?php
/**
 * EXPO2026 — Home Dashboard
 * Includes header.php which loads main.js ONCE.
 * Listens to expoLangChanged event to update Home-specific texts.
 */

session_start();
$role = $_SESSION['role'] ?? 'guest';

// --- ADMIN TOGGLE FOR FEEDBACK & RESULTS ---
$admin_allows_display = false;

require_once 'header.php';
?>

<link rel="stylesheet" href="Home.css">

<!-- SEARCH -->
<div class="search-box">
  <input type="text" id="searchInput" placeholder="بحث...">
</div>

<!-- CARDS -->
<div class="cards-container">

  <a href="Profile.php" class="card-link">
    <div class="card"><div class="card-body"><h3 data-en="Profile" data-ar="الملف الشخصي">الملف الشخصي</h3></div></div>
  </a>

  <?php if ($role === 'judge') { ?>
  <a href="judging.php" class="card-link">
    <div class="card"><div class="card-body"><h3 data-en="Judging" data-ar="التحكيم">التحكيم</h3></div></div>
  </a>
  <?php } ?>

  <?php if ($role === 'Admin') { ?>
  <a href="admin-judging.php" class="card-link">
    <div class="card"><div class="card-body"><h3 data-en="Admin Judging" data-ar="إدارة التحكيم">إدارة التحكيم</h3></div></div>
  </a>
  <a href="invite_judge_page.php" class="card-link">
    <div class="card"><div class="card-body"><h3 data-en="Invite Judge" data-ar="دعوة محكم">دعوة محكم</h3></div></div>
  </a>
  <?php } ?>

  <?php if ($role === 'student') { ?>
  <a href="poster.php" class="card-link">
    <div class="card"><div class="card-body"><h3 data-en="Poster" data-ar="البوستر">البوستر</h3></div></div>
  </a>
  <a href="Studentdata.php" class="card-link">
    <div class="card"><div class="card-body"><h3 data-en="Student Data" data-ar="بيانات الطلاب">بيانات الطلاب</h3></div></div>
  </a>
  <?php } ?>

  <?php if ($role === 'judge' || $role === 'Admin') { ?>
  <a href="feedback.php" class="card-link">
    <div class="card"><div class="card-body"><h3 data-en="Feedback" data-ar="التقييم">التقييم</h3></div></div>
  </a>
  <?php } ?>

  <?php if ($admin_allows_display === true) { ?>
  <a href="FINALRESULT.php" class="card-link">
    <div class="card"><div class="card-body"><h3 data-en="Final Result" data-ar="النتائج النهائية">النتائج النهائية</h3></div></div>
  </a>
  <?php } ?>

  <a href="mailto:cedfj.sr@iau.edu.sa" class="card-link">
    <div class="card"><div class="card-body"><h3 data-en="Contact" data-ar="تواصل">تواصل</h3></div></div>
  </a>

</div>

<!-- JS -->
<script src="Home.js"></script>

<!-- FOOTER -->
<div class="footer-img">
  <img src="footer.png" alt="Footer">
  <div class="footer-text" id="footerText">
    Vice Deanship of Scientific Research and Innovation
  </div>
</div>

<?php require_once 'footer.php'; ?>

<script>
(function() {
  'use strict';

  var currentLang = localStorage.getItem("lang") || "ar";
  document.documentElement.dir = (currentLang === "ar") ? "rtl" : "ltr";
  document.documentElement.lang = (currentLang === "ar") ? "ar" : "en";

  var headerTexts = {
    "t1": ["تسجيل الدخول", "Sign-up"],
    "t1-logout": ["تسجيل الخروج", "Logout"],
    "t2": ["صحة الإنسان", "Health"],
    "t3": ["اقتصاديات المستقبل", "Economies"],
    "t4": ["استدامة البيئة", "Sustainability"],
    "t5": ["الطاقة والصناعة", "Energy"],
    "t6": ["التعليم والقدرات", "Education"],
    "t7": ["الأبحاث المنشورة", "Research"],
  };

  var searchPlaceholder = {
    "ar": "بحث...",
    "en": "Search..."
  };

  function applyHeaderLang(lang) {
    currentLang = lang;
    document.documentElement.dir = (lang === "ar") ? "rtl" : "ltr";
    document.documentElement.lang = (lang === "ar") ? "ar" : "en";

    var logo = document.getElementById("expoLogo");
    if (logo) {
      logo.src = (lang === "ar") ? "expo2026_ar_white.png" : "expo2026_en_white.png";
    }

    for (var id in headerTexts) {
      var el = document.getElementById(id);
      if (el) el.textContent = (lang === "ar") ? headerTexts[id][0] : headerTexts[id][1];
    }

    // Update search placeholder
    var searchInput = document.getElementById("searchInput");
    if (searchInput) {
      searchInput.placeholder = searchPlaceholder[lang] || "Search...";
    }
  }

  function applyCardsLang(lang) {
    // Update all cards with data-en / data-ar
    var cards = document.querySelectorAll('.card-body h3[data-en][data-ar]');
    cards.forEach(function(h3) {
      h3.textContent = (lang === "ar") ? h3.getAttribute('data-ar') : h3.getAttribute('data-en');
    });
  }

  function applyAllLang(lang) {
    applyHeaderLang(lang);
    applyCardsLang(lang);
  }

  // Listen to the expoLangChanged event dispatched by header.php's localStorage patch
  window.addEventListener('expoLangChanged', function(e) {
    applyAllLang(e.detail.lang);
  });

  // Also listen to storage events for cross-tab sync
  window.addEventListener('storage', function(e) {
    if (e.key === 'lang' && e.newValue !== currentLang) {
      applyAllLang(e.newValue);
    }
  });

  window.expoGetLang = function() { return currentLang; };

  // Initial apply
  if (currentLang === "en") {
    applyAllLang("en");
  }
})();
</script>