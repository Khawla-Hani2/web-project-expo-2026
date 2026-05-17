<?php
/**
 * EXPO2026 — Unified Category Landing Page
 * URL: category.php?cat=health
 */

session_start();
require_once 'db.php';
require_once 'config.php';
require_once 'header.php';

/* ---- Validate category ---- */
$catSlug = sanitizeParam($_GET['cat'] ?? '');
$category = getCategory($catSlug);
if (!$category) {
    safeRedirect('index.php');
}

/* ---- Language ---- */
$lang = sanitizeParam($_GET['lang'] ?? '');
if (!in_array($lang, ['ar', 'en'])) {
    $lang = 'ar';
}

$allDepts = getAllDepartments();
?>

<style>
:root{
  --cat-primary:<?php echo $category['color']; ?>;
  --cat-hover:<?php echo $category['hover']; ?>;
}

.track-page {
  min-height: 60vh;
  padding: 60px 40px 80px;
  text-align: center;
  direction: rtl;
  overflow-x: hidden;
}

.track-subtitle {
  font-family: 'ExpoFont', 'Georgia', serif;
  font-weight: 700;
  font-size: 1.75rem;
  color: var(--cat-primary);
  margin-bottom: 8px;
  word-break: keep-all;
}

.track-title {
  font-family: 'ExpoFont', 'Georgia', serif;
  font-weight: 700;
  font-size: 2.6rem;
  color: var(--cat-primary);
  margin-bottom: 3rem;
  word-break: keep-all;
  line-height: 1.3;
}

.dept-grid {
  display: flex;
  flex-wrap: nowrap;
  justify-content: center;
  gap: 28px;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 10px;
}

.dept-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 10px;
  width: 170px;
  height: 170px;
  background-color: var(--cat-primary);
  border-radius: 18px;
  padding: 16px 12px 14px;
  text-decoration: none;
  transition: transform 0.2s ease, background-color 0.2s ease;
  flex-shrink: 0;
  box-sizing: border-box;
}

.dept-card:hover {
  transform: translateY(-6px);
  background-color: var(--cat-hover);
}

.dept-card img {
  width: 60px;
  height: 60px;
  object-fit: contain;
  flex-shrink: 0;
}

.dept-card span {
  font-family: 'ExpoFont', 'Georgia', serif;
  font-weight: 700;
  font-size: 0.8rem;
  color: #fff;
  text-align: center;
  line-height: 1.5;
  word-break: break-word;
}

@media (max-width: 1024px) {
  .dept-grid {
    flex-wrap: wrap;
    gap: 20px;
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
  .track-page {
    padding: 30px 12px 50px;
  }
  .track-title {
    font-size: 1.6rem;
    margin-bottom: 2rem;
  }
  .track-subtitle {
    font-size: 1rem;
  }
  .dept-grid {
    gap: 12px;
    padding: 0 8px;
  }
  .dept-card {
    width: 100px;
    height: 100px;
    padding: 10px 6px 8px;
    gap: 6px;
    border-radius: 14px;
  }
  .dept-card img {
    width: 38px;
    height: 38px;
  }
  .dept-card span {
    font-size: 0.6rem;
    line-height: 1.3;
  }
}

@media (max-width: 360px) {
  .dept-grid {
    gap: 8px;
  }
  .dept-card {
    width: 90px;
    height: 90px;
    padding: 8px 4px 6px;
  }
  .dept-card img {
    width: 32px;
    height: 32px;
  }
  .dept-card span {
    font-size: 0.55rem;
  }
}
</style>

<<main class="track-page">
  <h2 class="track-subtitle" id="trackSubtitle">مــشـاريـع مـسـار</h2>
  <h1 class="track-title" id="trackTitle"><?php echo htmlspecialchars($category['title_ar']); ?></h1>

  <div class="dept-grid">
<?php
$deptNum = 1;
foreach ($allDepts as $dept):
    $deptTitle = ($lang === 'en') ? $dept['title_en'] : $dept['title_ar'];
?>
    <a href="projects.php?cat=<?php echo urlencode($catSlug); ?>&dept=<?php echo urlencode($dept['slug']); ?>&lang=<?php echo $lang; ?>"
       class="dept-card">
      <img src="<?php echo htmlspecialchars($category['image']); ?>"
           alt="<?php echo htmlspecialchars($deptTitle); ?>">
      <span id="dept<?php echo $deptNum; ?>"><?php echo htmlspecialchars($deptTitle); ?></span>
    </a>
<?php
    $deptNum++;
endforeach;
?>
  </div>
</main>

<script>
/* ---------- Page text updater — listens to main.js language toggle ---------- */
(function() {
  'use strict';

  var pageTexts = {
    "trackSubtitle": ["مــشـاريـع مـسـار", "Projects Track"],
    "trackTitle": [
      "<?php echo addslashes($category['title_ar']); ?>",
      "<?php echo addslashes($category['title_en']); ?>"
    ],
<?php
$deptNum = 1;
foreach ($allDepts as $dept):
?>
    "dept<?php echo $deptNum; ?>": [
      "<?php echo addslashes($dept['title_ar']); ?>",
      "<?php echo addslashes($dept['title_en']); ?>"
    ],
<?php
    $deptNum++;
endforeach;
?>
  };

  function applyPageLang(lang) {
    for (var id in pageTexts) {
      var el = document.getElementById(id);
      if (el) {
        el.textContent = (lang === "ar") ? pageTexts[id][0] : pageTexts[id][1];
      }
    }
    var tp = document.querySelector('.track-page');
    if (tp) tp.style.direction = (lang === "ar") ? "rtl" : "ltr";

    // Keep <html> attributes in sync with main.js / CSS [dir="..."] rules
    document.documentElement.dir  = (lang === "ar") ? "rtl" : "ltr";
    document.documentElement.lang = (lang === "ar") ? "ar" : "en";
  }

  function init() {
    window.addEventListener('expoLangChanged', function(e) {
      applyPageLang(e.detail.lang);
    });

    var savedLang = window.expoGetLang ? window.expoGetLang() : (localStorage.getItem("lang") || "ar");
    if (savedLang === "en") {
      applyPageLang("en");
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
</script>

<?php require_once 'footer.php'; ?>