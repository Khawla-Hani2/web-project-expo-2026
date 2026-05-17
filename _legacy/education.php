<!--Fatimah Al-Qurain -->
<?php include 'header.php'; ?>
<style>
/* ===== Fonts ===== */
@font-face{
  font-family:"ExpoFont";
  src:url("TheYearofHandicrafts-Regular.woff2") format("woff2");
  font-weight:400;
  font-display:swap;
}
@font-face{
  font-family:"ExpoFont";
  src:url("TheYearofHandicrafts-Medium.woff2") format("woff2");
  font-weight:500;
  font-display:swap;
}
@font-face{
  font-family:"ExpoFont";
  src:url("TheYearofHandicrafts-Bold.woff2") format("woff2");
  font-weight:700;
  font-display:swap;
}
 
/* ===== Main Page ===== */
.track-page {
  min-height: 60vh;
  padding: 60px 40px 80px;
  text-align: center;
  direction: rtl;
}
 
/* ===== The Subtitle "Projects Track" ===== */
.track-subtitle {
  font-family: 'ExpoFont', 'Georgia', serif;
  font-weight: 700;
  font-size: 1.75rem;
  color: #662515;
  margin-bottom: 8px;
}
 
/* ===== Page Title ===== */
.track-title {
  font-family: 'ExpoFont', 'Georgia', serif;
  font-weight: 700;
  font-size: 2.6rem; /* Title font size */
  color: #662515;
  margin-bottom: 3rem;
}
 
/* ===== Cards Grid ===== */
.dept-grid {
  display: flex;
  flex-wrap: nowrap;
  justify-content: center;
  gap: 28px; /* Gap between cards */
  max-width: 1200px;
  margin: 0 auto;
}
 
/* ===== The Cards ===== */
.dept-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 10px;
  width: 170px;  /* Card width */
  height: 170px; /* Card height */
  background-color: #662515;
  border-radius: 18px;
  padding: 16px 12px 14px;
  text-decoration: none;
  transition: transform 0.2s ease, background-color 0.2s ease;
  flex-shrink: 1;
}
 
/* ===== Card Hover Effect ===== */
.dept-card:hover {
  transform: translateY(-6px);
  background-color: #782d1b;
}
 
/* ===== Card Icon Image ===== */
.dept-card img {
  width: 60px;  /* Image width */
  height: 60px; /* Image height */
  object-fit: contain;
}
 
/* ===== Department Name Text ===== */
.dept-card span {
  font-family: 'ExpoFont', 'Georgia', serif;
  font-weight: 700;
  font-size: 0.8rem; /* Card text font size */
  color: #fff;
  text-align: center;
  line-height: 1.5;
}
 
/* ===== Medium Screens - Tablets ===== */
@media (max-width: 1024px) {
  .dept-grid {
    flex-wrap: wrap;
  }
 
  .dept-card {
    width: 140px;  /* Card width on tablet */
    height: 140px; /* Card height on tablet */
  }
 
  .dept-card img {
    width: 55px;  /* Image size on tablet */
    height: 55px;
  }
}
 
/* ===== Small Screens - Mobile ===== */
@media (max-width: 600px) {
  .track-title {
    font-size: 1.8rem; 
  }
 
  .track-subtitle {
    font-size: 1.1rem; 
  }
 
  .dept-card {
    width: 120px;  
    height: 120px; 
  }
 
  .dept-card img {
    width: 45px; 
    height: 45px;
  }
 
  .dept-card span {
    font-size: 0.72rem; 
  }
}
</style>
 
<main class="track-page">
 
  <!-- Page Title -->
  <h2 class="track-subtitle" id="trackSubtitle">مــشـاريـع مـسـار</h2>
  <h1 class="track-title" id="trackTitle"> الــتــعــلــيــم وتــنــمــيــة الــقــدرات الــبــشــريــة </h1>
 
  <!-- Department Cards -->
  <div class="dept-grid">
 
    <a href="edu_computer_projects.php" class="dept-card">
      <img src="Education_and_Development.png" alt="قسم علوم الحاسب الآلي">
      <span id="dept1">قسم علوم الحاسب الآلي</span>
    </a>
 
    <a href="edu_physics_projects.php" class="dept-card">
      <img src="Education_and_Development.png" alt="قسم الفيزياء والطاقة المتجددة">
      <span id="dept2">قسم الفيزياء والطاقة المتجددة</span>
    </a>
 
    <a href="edu_english_projects.php" class="dept-card">
      <img src="Education_and_Development.png" alt="قسم اللغة الإنقليزية">
      <span id="dept3">قسم اللغة الإنقليزية</span>
    </a>
 
    <a href="edu_math_projects.php" class="dept-card">
      <img src="Education_and_Development.png" alt="قسم الرياضيات">
      <span id="dept4">قسم الرياضيات</span>
    </a>
 
    <a href="edu_kindergarden_projects.php" class="dept-card">
      <img src="Education_and_Development.png" alt="قسم الطفولة المبكرة">
      <span id="dept5">قسم الطفولة المبكرة</span>
    </a>
 
  </div>
 
</main>
 
<script>
// Load saved language from localStorage
if (typeof lang === 'undefined') var lang = localStorage.getItem("lang") || "en";
 
// Language toggle function
function toggleLang() {
  lang = lang === "ar" ? "en" : "ar";
  localStorage.setItem("lang", lang); // Save language
  document.documentElement.dir = lang === "ar" ? "rtl" : "ltr";
  document.querySelector('.track-page').style.direction = lang === "ar" ? "rtl" : "ltr";
 
  // Header logo
  document.getElementById("expoLogo").src =
    lang === "ar"
    ? "expo2026_ar_white.png"
    : "expo2026_en_white.png";
 
  // Header texts
  setText("t1", "تسجيل الدخول", "Sign-up");
  setText("t2", "صحة الإنسان", "Health");
  setText("t3", "اقتصاديات المستقبل", "Economies");
  setText("t4", "استدامة البيئة", "Sustainability");
  setText("t5", "الطاقة والصناعة", "Energy");
  setText("t6", "التعليم والقدرات", "Education");
  setText("t7", "الأبحاث المنشورة", "Research");

  // This page texts
  setText("trackSubtitle", "مــشـاريـع مـسـار", "Projects Track");
  setText("trackTitle", " الــتــعــلــيــم وتــنــمــيــة الــقــدرات الــبــشــريــة ", "Education and Human Development");
  setText("dept1", "قسم علوم الحاسب الآلي", "Computer Science Department");
  setText("dept2", "قسم الفيزياء والطاقة المتجددة", "Physics Department");
  setText("dept3", "قسم اللغة الإنقليزية", "English Language Department");
  setText("dept4", "قسم الرياضيات", "Mathematics Department");
  setText("dept5", "قسم الطفولة المبكرة", "Kindergarten Department");
}
 
function setText(id, ar, en) {
  const el = document.getElementById(id);
  if (el) el.textContent = (lang === "ar" ? ar : en);
}
 
// Apply saved language on page load
if (lang === "en") toggleLang();
</script>
 
<?php include 'partials/footer.php'; ?>