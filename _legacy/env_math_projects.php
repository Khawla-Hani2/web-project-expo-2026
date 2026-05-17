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

$stmt = $pdo->prepare("
  SELECT id, title, poster_pdf
  FROM projects
  WHERE track = 'Mathematics (Statistics and Data Science Program)'
  ORDER BY created_at DESC
");
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
/* ===== Fonts ===== */
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

.track-subtitle {
  font-family: 'ExpoFont', 'Georgia', serif;
  font-weight: 700;
  font-size: 1.75rem;
  color: #484E2D;
  margin-bottom: 8px;
}

.track-title {
  font-family: 'ExpoFont', 'Georgia', serif;
  font-weight: 700;
  font-size: 2.6rem;
  color: #484E2D;
  margin-bottom: 3rem;
}

.projects-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 28px;
  max-width: 1100px;
  margin: 0 auto;
}

.project-card {
  background: #ffffff;
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(72, 78, 45, 0.10);
  border: 1px solid #ced5b8;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  text-align: right;
}

.project-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 8px 32px rgba(72, 78, 45, 0.18);
}

.card-header {
  background-color: #484E2D;
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
  color: #ffffff;
  flex: 1;
  text-align: right;
  line-height: 1.5;
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
  background: #f0f1ea;
}

.pdf-placeholder {
  height: 460px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 12px;
  background: #f0f1ea;
  color: #6b7050;
  font-family: 'ExpoFont', 'Georgia', serif;
  font-size: 0.95rem;
}

.card-footer {
  padding: 14px 22px;
  background: #f5f6f0;
  display: flex;
  align-items: center;
  justify-content: flex-start;
}

.btn-download {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background-color: #484E2D;
  color: #ffffff;
  text-decoration: none;
  padding: 10px 22px;
  border-radius: 10px;
  font-family: 'ExpoFont', 'Georgia', serif;
  font-weight: 700;
  font-size: 0.9rem;
  transition: background-color 0.2s ease, transform 0.2s ease;
}

.btn-download:hover {
  background-color: #5c6438;
  transform: translateY(-2px);
}

.no-projects {
  font-family: 'ExpoFont', 'Georgia', serif;
  font-size: 1.1rem;
  color: #6b7050;
  padding: 60px 20px;
  grid-column: 1 / -1;
}

@media (max-width: 1024px) {
  .projects-grid { grid-template-columns: repeat(2, 1fr); gap: 20px; }
}

@media (max-width: 600px) {
  .track-title    { font-size: 1.8rem; }
  .track-subtitle { font-size: 1.1rem; }
  .projects-grid  { grid-template-columns: 1fr; }
  .pdf-viewer, .pdf-placeholder { height: 340px; }
}
</style>

<main class="track-page">

  <h2 class="track-subtitle" id="trackSubtitle">مـشـاريـع مـسـار اسـتـدامـة الـبـيـئـة والاحـتـيـاجـات الأسـاسـيـة</h2>
  <h1 class="track-title"    id="trackTitle">قـسـم الـريـاضـيـات </h1>

  <div class="projects-grid">

    <?php if (empty($projects)): ?>
      <p class="no-projects" id="noProjectsMsg">لا توجد مشاريع مضافة لهذا القسم حتى الآن</p>
    <?php else: ?>
      <?php foreach ($projects as $project): ?>
        <?php
          $projectName = htmlspecialchars($project['title']      ?? 'مشروع بدون اسم');
          $pdfPath     = htmlspecialchars($project['poster_pdf'] ?? '');
          $hasPdf      = !empty($pdfPath) && file_exists($pdfPath);
        ?>
        <div class="project-card">
          <div class="card-header">
            <span><?= $projectName ?></span>
            <img src="assets/img/Environmental_Sustainability.png" alt="<?= $projectName ?>">
          </div>
          <?php if ($hasPdf): ?>
            <iframe class="pdf-viewer" src="<?= $pdfPath ?>#toolbar=0&navpanes=0&scrollbar=1" title="معاينة <?= $projectName ?>" loading="lazy"></iframe>
          <?php else: ?>
            <div class="pdf-placeholder">
              <p id="noFilePlaceholder_<?= $project['id'] ?>">الملف غير متوفر حالياً</p>
            </div>
          <?php endif; ?>
          <div class="card-footer">
            <?php if ($hasPdf): ?>
              <a class="btn-download" href="<?= $pdfPath ?>" download="<?= $projectName ?>.pdf" id="downloadBtn_<?= $project['id'] ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#fff" viewBox="0 0 24 24">
                  <path d="M19 9h-4V3H9v6H5l7 7 7-7zm-8 2V5h2v6h1.17L12 13.17 9.83 11H11zm-6 7h14v2H5z"/>
                </svg>
                <span id="downloadLabel_<?= $project['id'] ?>">تحميل الملف</span>
              </a>
            <?php else: ?>
              <span style="font-family:'ExpoFont','Georgia',serif; color:#6b7050; font-size:0.9rem;" id="noFileLabel_<?= $project['id'] ?>">الملف غير متاح</span>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

  </div>
</main>

<script>
if (typeof lang === 'undefined') var lang = localStorage.getItem("lang") || "en";

function toggleLang() {
  lang = lang === "ar" ? "en" : "ar";
  localStorage.setItem("lang", lang);
  document.documentElement.dir = lang === "ar" ? "rtl" : "ltr";
  document.querySelector('.track-page').style.direction = lang === "ar" ? "rtl" : "ltr";
  document.getElementById("expoLogo").src = lang === "ar" ? "expo2026_ar_white.png" : "expo2026_en_white.png";
  setText("t1", "تسجيل الدخول",      "Sign-up");
  setText("t2", "صحة الإنسان",        "Health");
  setText("t3", "اقتصاديات المستقبل", "Economies");
  setText("t4", "استدامة البيئة",      "Sustainability");
  setText("t5", "الطاقة والصناعة",     "Energy");
  setText("t6", "التعليم والقدرات",    "Education");
  setText("t7", "الأبحاث المنشورة",   "Research");
  setText("trackSubtitle", "مـشـاريـع مـسـار اسـتـدامـة الـبـيـئـة والاحـتـيـاجـات الأسـاسـيـة", "Environmental Sustainability Track Projects");
  setText("trackTitle",    "قـسـم الـريـاضـيـات ",                                        "Mathematics Department");
  setText("noProjectsMsg", "لا توجد مشاريع مضافة لهذا القسم حتى الآن",                            "No projects added yet");
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

if (lang === "en") toggleLang();
</script>

<?php include 'footer.php'; ?>
