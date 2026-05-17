<?php
/**
 * EXPO2026 — Unified Project Grid Page
 * URL: projects.php?cat=health&dept=physics-renewable
 */

session_start();
require_once 'db.php';
require_once 'config.php';
require_once 'header.php';

/* ---- Validate inputs ---- */
$catSlug  = sanitizeParam($_GET['cat'] ?? '');
$deptSlug = sanitizeParam($_GET['dept'] ?? '');

$category   = getCategory($catSlug);
$department = getDepartment($deptSlug);

if (!$category || !$department) {
    safeRedirect('index.php');
}

/* ---- Language ---- */
$lang = sanitizeParam($_GET['lang'] ?? '');
if (!in_array($lang, ['ar', 'en'])) {
    $lang = 'ar';
}

/* ---- Fetch projects from DB ---- */
$projects = [];
$fetchError = '';

try {
    if (isset($pdo)) {
        $stmt = $pdo->prepare(
            "SELECT id, title, poster_pdf
             FROM projects
             WHERE theme = ? AND track = ?
             ORDER BY created_at DESC"
        );
        $stmt->execute([$category['db_theme'], $department['db_track']]);
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif (isset($conn)) {
        $stmt = $conn->prepare(
            "SELECT id, title, poster_pdf
             FROM projects
             WHERE theme = ? AND track = ?
             ORDER BY created_at DESC"
        );
        $stmt->bind_param("ss", $category['db_theme'], $department['db_track']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $projects[] = $row;
        }
    } else {
        $fetchError = 'Database connection not available.';
    }
} catch (Exception $e) {
    $fetchError = 'Error fetching projects: ' . $e->getMessage();
}

/* ---- Breadcrumb / title text ---- */
$subtitleAr = 'مـشـاريـع مـسـار ' . $category['title_ar'];
$titleAr    = 'قـسـم ' . $department['title_ar'];
$subtitleEn = $category['title_en'] . ' Track Projects';
$titleEn    = $department['title_en'] . ' Department';
?>

<style>
:root{
  --cat-primary:<?php echo $category['color']; ?>;
  --cat-hover:<?php echo $category['hover']; ?>;
  --cat-shadow-10:rgba(<?php echo $category['shadow']; ?>, 0.10);
  --cat-shadow-18:rgba(<?php echo $category['shadow']; ?>, 0.18);
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
  line-height: 1.3;
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

.breadcrumb {
  margin-bottom: 1.5rem;
}
.breadcrumb a {
  font-family: 'ExpoFont', 'Georgia', serif;
  font-size: 0.95rem;
  color: var(--cat-primary);
  text-decoration: none;
  border: 2px solid var(--cat-primary);
  padding: 8px 20px;
  border-radius: 10px;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: background-color 0.2s ease, color 0.2s ease;
}
.breadcrumb a:hover {
  background-color: var(--cat-primary);
  color: #fff;
}

.projects-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 28px;
  max-width: 1100px;
  margin: 0 auto;
  padding: 0 10px;
}

.project-card {
  background: #fff;
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 4px 20px var(--cat-shadow-10);
  border: 1px solid #e8d5dd;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  text-align: right;
  display: flex;
  flex-direction: column;
}

.project-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 8px 32px var(--cat-shadow-18);
}

.card-header {
  background-color: var(--cat-primary);
  padding: 18px 22px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
}

.card-header span {
  font-family: 'ExpoFont', 'Georgia', serif;
  font-weight: 700;
  font-size: 0.95rem;
  color: #fff;
  flex: 1;
  text-align: right;
  line-height: 1.5;
  word-break: break-word;
}

.card-header img {
  width: 48px;
  height: 48px;
  object-fit: contain;
  flex-shrink: 0;
}

.pdf-viewer {
  width: 100%;
  height: 460px;
  border: none;
  display: block;
  background: #f5eff1;
}

.pdf-placeholder {
  height: 460px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 12px;
  background: #f5eff1;
  color: #9a6070;
  font-family: 'ExpoFont', 'Georgia', serif;
  font-size: 0.95rem;
}

.card-footer {
  padding: 14px 22px;
  background: #faf5f7;
  display: flex;
  align-items: center;
  justify-content: flex-start;
}

.btn-download {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background-color: var(--cat-primary);
  color: #fff;
  text-decoration: none;
  padding: 10px 22px;
  border-radius: 10px;
  font-family: 'ExpoFont', 'Georgia', serif;
  font-weight: 700;
  font-size: 0.9rem;
  transition: background-color 0.2s ease, transform 0.2s ease;
}

.btn-download:hover {
  background-color: var(--cat-hover);
  transform: translateY(-2px);
}

.no-projects {
  font-family: 'ExpoFont', 'Georgia', serif;
  font-size: 1.1rem;
  color: #9a6070;
  padding: 60px 20px;
  grid-column: 1 / -1;
}

@media (max-width: 1024px) {
  .projects-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
  }
  .pdf-viewer,
  .pdf-placeholder {
    height: 380px;
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
  .projects-grid {
    grid-template-columns: 1fr;
    gap: 20px;
    padding: 0 8px;
  }
  .pdf-viewer,
  .pdf-placeholder {
    height: 340px;
  }
  .card-header {
    padding: 14px 16px;
  }
  .card-header span {
    font-size: 0.85rem;
  }
  .card-header img {
    width: 40px;
    height: 40px;
  }
  .card-footer {
    padding: 12px 16px;
  }
}

@media (max-width: 360px) {
  .track-title {
    font-size: 1.4rem;
  }
  .pdf-viewer,
  .pdf-placeholder {
    height: 280px;
  }
}
</style>

<<main class="track-page">
  <div class="breadcrumb">
    <a href="category.php?cat=<?php echo urlencode($catSlug); ?>&lang=<?php echo $lang; ?>" id="backLink">
      ← <?php echo ($lang === 'en') ? 'Back to ' . $category['title_en'] : 'العودة إلى ' . $category['title_ar']; ?>
    </a>
  </div>

  <h2 class="track-subtitle" id="trackSubtitle"><?php echo htmlspecialchars($subtitleAr); ?></h2>
  <h1 class="track-title"    id="trackTitle"><?php echo htmlspecialchars($titleAr); ?></h1>

  <div class="projects-grid">
<?php if ($fetchError): ?>
    <p class="no-projects" style="color:#c62828;"><?php echo htmlspecialchars($fetchError); ?></p>
<?php elseif (empty($projects)): ?>
    <p class="no-projects" id="noProjectsMsg">لا توجد مشاريع مضافة لهذا القسم حتى الآن</p>
<?php else: ?>
<?php foreach ($projects as $project): ?>
<?php
    $projectName = htmlspecialchars($project['title'] ?? 'مشروع بدون اسم');
    $pdfPath     = $project['poster_pdf'] ?? '';
    $hasPdf      = !empty($pdfPath) && file_exists($pdfPath);
?>
    <div class="project-card">
      <div class="card-header">
        <span><?php echo $projectName; ?></span>
        <img src="<?php echo htmlspecialchars($category['image']); ?>" alt="<?php echo $projectName; ?>">
      </div>
<?php if ($hasPdf): ?>
      <iframe class="pdf-viewer"
        src="<?php echo htmlspecialchars($pdfPath); ?>#toolbar=0&navpanes=0&scrollbar=1"
        title="معاينة <?php echo $projectName; ?>"
        loading="lazy">
      </iframe>
<?php else: ?>
      <div class="pdf-placeholder">
        <p id="noFilePlaceholder_<?php echo (int)$project['id']; ?>">الملف غير متوفر حالياً</p>
      </div>
<?php endif; ?>
      <div class="card-footer">
<?php if ($hasPdf): ?>
        <a class="btn-download"
           href="<?php echo htmlspecialchars($pdfPath); ?>"
           download="<?php echo $projectName; ?>.pdf"
           id="downloadBtn_<?php echo (int)$project['id']; ?>">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#fff" viewBox="0 0 24 24">
            <path d="M19 9h-4V3H9v6H5l7 7 7-7zm-8 2V5h2v6h1.17L12 13.17 9.83 11H11zm-6 7h14v2H5z"/>
          </svg>
          <span id="downloadLabel_<?php echo (int)$project['id']; ?>">تحميل الملف</span>
        </a>
<?php else: ?>
        <span style="font-family:'ExpoFont','Georgia',serif; color:#9a6070; font-size:0.9rem;"
              id="noFileLabel_<?php echo (int)$project['id']; ?>">
          الملف غير متاح
        </span>
<?php endif; ?>
      </div>
    </div>
<?php endforeach; ?>
<?php endif; ?>
  </div>
</main>

<script>
/* ---------- Page text updater — listens to main.js language toggle ---------- */
(function() {
  'use strict';

  var pageTexts = {
    "trackSubtitle": [
      "<?php echo addslashes($subtitleAr); ?>",
      "<?php echo addslashes($subtitleEn); ?>"
    ],
    "trackTitle": [
      "<?php echo addslashes($titleAr); ?>",
      "<?php echo addslashes($titleEn); ?>"
    ],
    "noProjectsMsg": [
      "لا توجد مشاريع مضافة لهذا القسم حتى الآن",
      "No projects added yet"
    ],
<?php foreach ($projects as $project): ?>
    "downloadLabel_<?php echo (int)$project['id']; ?>": ["تحميل الملف", "Download File"],
    "noFileLabel_<?php echo (int)$project['id']; ?>": ["الملف غير متاح", "File not available"],
    "noFilePlaceholder_<?php echo (int)$project['id']; ?>": ["الملف غير متوفر حالياً", "File not available"],
<?php endforeach; ?>
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

    // Sync root attributes so side-menu, footer, etc. respond instantly
    document.documentElement.dir  = (lang === "ar") ? "rtl" : "ltr";
    document.documentElement.lang = (lang === "ar") ? "ar" : "en";

    var back = document.getElementById('backLink');
    if (back) {
      if (lang === "en") {
        back.innerHTML = '← Back to <?php echo addslashes($category['title_en']); ?>';
      } else {
        back.innerHTML = '← العودة إلى <?php echo addslashes($category['title_ar']); ?>';
      }
    }
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