<!--Fatimah Al-Qurain-->
<?php include 'header.php'; ?>
<style>
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
 

.track-page {
  min-height: 60vh;
  padding: 60px 40px 80px;
  text-align: center;
  direction: rtl;
}
.track-subtitle {
  font-family: 'ExpoFont', 'Georgia', serif;
  font-weight: 700;
  font-size: 1.75rem;
  color: #6b1a3a;
  margin-bottom: 8px;
}
 
.track-title {
  font-family: 'ExpoFont', 'Georgia', serif;
  font-weight: 700;
  font-size: 2.6rem;
  color: #6b1a3a;
  margin-bottom: 3rem;
}
 
.dept-grid {
  display: flex;
  flex-wrap: nowrap;
  justify-content: center;
  gap: 28px;
  max-width: 1200px;
  margin: 0 auto;
}

.dept-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 10px;
  width: 170px;  
  height: 170px; 
  background-color: #6b1a3a;
  border-radius: 18px;
  padding: 16px 12px 14px;
  text-decoration: none;
  transition: transform 0.2s ease, background-color 0.2s ease;
  flex-shrink: 1;
}
 
.dept-card:hover {
  transform: translateY(-6px);
  background-color: #842a4e;
}
 
.dept-card img {
  width: 60px; 
  height: 60px; 
  object-fit: contain;
}

.dept-card span {
  font-family: 'ExpoFont', 'Georgia', serif;
  font-weight: 700;
  font-size: 0.8rem; 
  color: #fff;
  text-align: center;
  line-height: 1.5;
}

@media (max-width: 1024px) {
  .dept-grid {
    flex-wrap: wrap;
  }
 
  .dept-card {
    width: 140px; 
    height: 140px; 
  }
 
  .dept-card img {
    width: 55px; 
    height: 55px;
  }
}

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
 
  <h2 class="track-subtitle" id="trackSubtitle">مــشـاريـع مـسـار</h2>
  <h1 class="track-title" id="trackTitle">صـــــحــــــة الإنـــــســــــان</h1>
 
  <div class="dept-grid">
    <a href="health_computer_projects.php" class="dept-card">
      <img src="Human_Health.png" alt="قسم علوم الحاسب الآلي">
      <span id="dept1">قسم علوم الحاسب الآلي</span>
    </a>

    <a href="health_physics_projects.php" class="dept-card">
      <img src="Human_Health.png" alt="قسم الفيزياء والطاقة المتجددة">
      <span id="dept2">قسم الفيزياء والطاقة المتجددة</span>
    </a>

    <a href="health_english_projects.php" class="dept-card">
      <img src="Human_Health.png" alt="قسم اللغة الإنقليزية">
      <span id="dept3">قسم اللغة الإنقليزية</span>
    </a>
 
    <a href="health_math_projects.php" class="dept-card">
      <img src="Human_Health.png" alt="قسم الرياضيات">
      <span id="dept4">قسم الرياضيات</span>
    </a>
 
    <a href="health_kindergarden_projects.php" class="dept-card">
      <img src="Human_Health.png" alt="قسم الطفولة المبكرة">
      <span id="dept5">قسم الطفولة المبكرة</span>
    </a>
 
  </div>
 
</main>
 
<script>
if (typeof lang === 'undefined') var lang = localStorage.getItem("lang") || "ar";
 
function toggleLang() {
  lang = lang === "ar" ? "en" : "ar";
  localStorage.setItem("lang", lang); 
  document.documentElement.dir = lang === "ar" ? "rtl" : "ltr";
  document.querySelector('.track-page').style.direction = lang === "ar" ? "rtl" : "ltr";
 
  document.getElementById("expoLogo").src =
    lang === "ar"
    ? "expo2026_ar_white.png"
    : "expo2026_en_white.png";
 
  setText("t1", "تسجيل الدخول", "Sign-up");
  setText("t2", "صحة الإنسان", "Health");
  setText("t3", "اقتصاديات المستقبل", "Economies");
  setText("t4", "استدامة البيئة", "Sustainability");
  setText("t5", "الطاقة والصناعة", "Energy");
  setText("t6", "التعليم والقدرات", "Education");
  setText("t7", "الأبحاث المنشورة", "Research");
 
  setText("trackSubtitle", "مــشـاريـع مـسـار", "Projects Track");
  setText("trackTitle", "صــحــة الإنــســان", "Human Health");
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
 
<?php include 'footer.php'; ?>