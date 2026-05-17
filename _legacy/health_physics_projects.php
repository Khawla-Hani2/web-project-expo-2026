<!--Fatimah Al-Qurain -->
<?php include 'header.php'; ?>

<?php
// Database Connection
$host     = 'localhost';
$dbname   = 'expo2026';
$username = 'root';
$password = '';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Database connection error: " . $e->getMessage());
}

$stmt = $pdo->prepare("SELECT id, title, poster_pdf FROM projects WHERE track = 'Physics and Renewable Energy' ORDER BY created_at DESC ");
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
/*  Fonts */
@font-face {
  font-family: "ExpoFont";
  src: url("TheYearofHandicrafts-Regular.woff2") format("woff2");
  font-weight: 400;
  font-display: swap;
}
@font-face {
  font-family: "ExpoFont";
  src: url("TheYearofHandicrafts-Medium.woff2") format("woff2");
  font-weight: 500;
  font-display: swap;
}
@font-face {
  font-family: "ExpoFont";
  src: url("TheYearofHandicrafts-Bold.woff2") format("woff2");
  font-weight: 700;
  font-display: swap;
}

/* Main Page */
.track-page {
  min-height: 60vh;
  padding: 60px 40px 80px;
  text-align: center;
  direction: rtl;
}

/* The Subtitle "Projects Track" */
.track-subtitle {
  font-family: 'ExpoFont', 'Georgia', serif;
  font-weight: 700;
  font-size: 1.75rem;
  color: #6b1a3a;
  margin-bottom: 8px;
}

/* Page Title - Department Name */
.track-title {
  font-family: 'ExpoFont', 'Georgia', serif;
  font-weight: 700;
  font-size: 2.6rem;
  color: #6b1a3a;
  margin-bottom: 3rem;
}

/* Projects Grid - Two Columns */
.projects-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 28px;
  max-width: 1100px;
  margin: 0 auto;
}

/* Project Card */
.project-card {
  background: #fff;
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(107, 26, 58, 0.10);
  border: 1px solid #e8d5dd;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  text-align: right;
}

.project-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 8px 32px rgba(107, 26, 58, 0.18);
}

/* Card Header - Project Name + Icon  */
.card-header {
  background-color: #6b1a3a;
  padding: 18px 22px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
}

/* Project Name Text */
.card-header span {
  font-family: 'ExpoFont', 'Georgia', serif;
  font-weight: 700;
  font-size: 0.95rem;
  color: #fff;
  flex: 1;
  text-align: right;
  line-height: 1.5;
}

/*Header Icon Image */
.card-header img {
  width: 48px;
  height: 48px;
  object-fit: contain;
  flex-shrink: 0;
}

/* PDF Preview Area */
.pdf-viewer {
  width: 100%;
  height: 460px;
  border: none;
  display: block;
  background: #f5eff1;
}

/* No PDF Placeholder */
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

/*Card Footer - Download Button*/
.card-footer {
  padding: 14px 22px;
  background: #faf5f7;
  display: flex;
  align-items: center;
  justify-content: flex-start;
}

/* Download Button  */
.btn-download {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background-color: #6b1a3a;
  color: #fff;
  text-decoration: none;
  padding: 10px 22px;
  border-radius: 10px;
  font-family: 'ExpoFont', 'Georgia', serif;
  font-weight: 700;
  font-size: 0.9rem;
  transition: background-color 0.2s ease, transform 0.2s ease;
}

/* Download Button Hover Effect */
.btn-download:hover {
  background-color: #842a4e;
  transform: translateY(-2px);
}

/*  No Projects Message */
.no-projects {
  font-family: 'ExpoFont', 'Georgia', serif;
  font-size: 1.1rem;
  color: #9a6070;
  padding: 60px 20px;
  grid-column: 1 / -1;
}

/* Medium Screens - Tablets */
@media (max-width: 1024px) {
  .projects-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
  }
}

/* Small Screens - Mobile */
@media (max-width: 600px) {
  .track-title    { font-size: 1.8rem; }
  .track-subtitle { font-size: 1.1rem; }

  .projects-grid {
    grid-template-columns: 1fr;
  }

  .pdf-viewer,
  .pdf-placeholder {
    height: 340px;
  }
}
</style>

<main class="track-page">

  <!-- Page Title -->
  <h2 class="track-subtitle" id="trackSubtitle">مـشـاريـع مـسـار صــحـة الإنـسـان</h2>
  <h1 class="track-title"    id="trackTitle">قـسـم الــفيزياء و الطـــاقة المــتـجددة </h1>

  <!-- Projects Grid -->
  <div class="projects-grid">

    <?php if (empty($projects)): ?>

      <!-- No Projects Message -->
      <p class="no-projects" id="noProjectsMsg">لا توجد مشاريع مضافة لهذا القسم حتى الآن</p>

    <?php else: ?>

      <?php foreach ($projects as $project): ?>

        <?php
          // Project name and PDF path from database
          $projectName = htmlspecialchars($project['title']      ?? 'مشروع بدون اسم');
          $pdfPath     = htmlspecialchars($project['poster_pdf'] ?? '');

          // Check if the file physically exists on the server
          $hasPdf = !empty($pdfPath) && file_exists($pdfPath);
        ?>

        <!-- Project Card -->
        <div class="project-card">

          <!-- Card Header: Project Name + Icon -->
          <div class="card-header">
            <span><?= $projectName ?></span>
            <img src="Human_Health.png" alt="<?= $projectName ?>">
          </div>

          <!-- PDF Preview -->
          <?php if ($hasPdf): ?>
            <iframe
              class="pdf-viewer"
              src="<?= $pdfPath ?>#toolbar=0&navpanes=0&scrollbar=1"
              title="معاينة <?= $projectName ?>"
              loading="lazy">
            </iframe>
          <?php else: ?>
            <div class="pdf-placeholder">
              <p id="noFilePlaceholder_<?= $project['id'] ?>">الملف غير متوفر حالياً</p>
            </div>
          <?php endif; ?>

          <!-- Card Footer: Download Button -->
          <div class="card-footer">
            <?php if ($hasPdf): ?>
              <a class="btn-download"
                 href="<?= $pdfPath ?>"
                 download="<?= $projectName ?>.pdf"
                 id="downloadBtn_<?= $project['id'] ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#fff" viewBox="0 0 24 24">
                  <path d="M19 9h-4V3H9v6H5l7 7 7-7zm-8 2V5h2v6h1.17L12 13.17 9.83 11H11zm-6 7h14v2H5z"/>
                </svg>
                <span id="downloadLabel_<?= $project['id'] ?>">تحميل الملف</span>
              </a>
            <?php else: ?>
              <span style="font-family:'ExpoFont','Georgia',serif; color:#9a6070; font-size:0.9rem;"
                    id="noFileLabel_<?= $project['id'] ?>">
                الملف غير متاح
              </span>
            <?php endif; ?>
          </div>

        </div><!-- end .project-card -->

      <?php endforeach; ?>

    <?php endif; ?>

  </div><!-- end .projects-grid -->

</main>

<script>
// Load saved language from localStorage
if (typeof lang === 'undefined') var lang = localStorage.getItem("lang") || "ar";

// Language toggle function
function toggleLang() {
  lang = lang === "ar" ? "en" : "ar";
  localStorage.setItem("lang", lang);
  document.documentElement.dir = lang === "ar" ? "rtl" : "ltr";
  document.querySelector('.track-page').style.direction = lang === "ar" ? "rtl" : "ltr";

  // Header logo
  document.getElementById("expoLogo").src =
    lang === "ar"
    ? "expo2026_ar_white.png"
    : "expo2026_en_white.png";

  // Header texts
  setText("t1", "تسجيل الدخول",      "Sign-up");
  setText("t2", "صحة الإنسان",        "Health");
  setText("t3", "اقتصاديات المستقبل", "Economies");
  setText("t4", "استدامة البيئة",      "Sustainability");
  setText("t5", "الطاقة والصناعة",     "Energy");
  setText("t6", "التعليم والقدرات",    "Education");
  setText("t7", "الأبحاث المنشورة",   "Research");

  // This page texts
  setText("trackSubtitle", "مـشـاريـع مـسـار صــحـة الإنـسـان", "Human Health Track Projects");
  setText("trackTitle",    "قـسـم الــلــغــة الإنــقــلــيــزيــة ", "Physics and Renewable Energy Department");
  setText("noProjectsMsg", "لا توجد مشاريع مضافة لهذا القسم حتى الآن", "No projects added yet");

  // Per-card texts
  <?php foreach ($projects as $project): ?>
    setText("downloadLabel_<?= $project['id'] ?>",    "تحميل الملف",            "Download File");
    setText("noFileLabel_<?= $project['id'] ?>",      "الملف غير متاح",         "File not available");
    setText("noFilePlaceholder_<?= $project['id'] ?>","الملف غير متوفر حالياً", "File not available");
  <?php endforeach; ?>
}

function setText(id, ar, en) {
  const el = document.getElementById(id);
  if (el) el.textContent = (lang === "ar" ? ar : en);
}

// Apply saved language on page load
if (lang === "en") toggleLang();
</script>

<?php include 'footer.php'; ?>
