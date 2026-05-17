<?php
$lang = ($_GET['lang'] ?? 'en') === 'ar' ? 'ar' : 'en';
$isArabic = $lang === 'ar';

$conn = new mysqli('localhost', 'root', '', 'expo2026');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function tableHasColumn($conn, $table, $column) {
    $result = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $result && $result->num_rows > 0;
}

$hasArabicProjectColumns = tableHasColumn($conn, 'projects', 'project_name_ar')
    && tableHasColumn($conn, 'projects', 'supervisors_ar')
    && tableHasColumn($conn, 'projects', 'team_ar')
    && tableHasColumn($conn, 'projects', 'summary_ar');

function pickLangValue($row, $englishColumn, $arabicColumn, $isArabic) {
    if ($isArabic && isset($row[$arabicColumn]) && trim((string)$row[$arabicColumn]) !== '') {
        return $row[$arabicColumn];
    }
    return $row[$englishColumn] ?? '';
}

function renderProjectsByTrack($conn, $trackId, $isArabic, $hasArabicProjectColumns) {
    $columns = 'id, project_name, supervisors, team, summary';
    if ($hasArabicProjectColumns) {
        $columns .= ', project_name_ar, supervisors_ar, team_ar, summary_ar';
    }

    $sql = "SELECT $columns FROM projects WHERE track_id = ? ORDER BY id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $trackId);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result || $result->num_rows === 0) {
        echo '<p class="no-projects">' . ($isArabic ? 'لا توجد مشاريع بعد.' : 'No projects found.') . '</p>';
        $stmt->close();
        return;
    }

    $rank = 1;
    while ($row = $result->fetch_assoc()) {
        $projectName = pickLangValue($row, 'project_name', 'project_name_ar', $isArabic);
        $supervisors = pickLangValue($row, 'supervisors', 'supervisors_ar', $isArabic);
        $team = pickLangValue($row, 'team', 'team_ar', $isArabic);
        $summary = pickLangValue($row, 'summary', 'summary_ar', $isArabic);
        ?>
        <div class="project-card">
          <span class="project-rank"><?= $rank ?></span>
          <h4><?= e($projectName) ?></h4>

          <div class="project-meta">
            <p>
              <strong><?= $isArabic ? 'المشرفون' : 'Supervisors' ?></strong>
              <span><?= nl2br(e($supervisors)) ?></span>
            </p>

            <p>
              <strong><?= $isArabic ? 'أعضاء الفريق' : 'Team Members' ?></strong>
              <span><?= nl2br(e($team)) ?></span>
            </p>

            <p>
              <strong><?= $isArabic ? 'الملخص' : 'Summary' ?></strong>
              <span><?= nl2br(e($summary)) ?></span>
            </p>
          </div>
        </div>
        <?php
        $rank++;
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="<?= e($lang) ?>" dir="<?= $isArabic ? 'rtl' : 'ltr' ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EXPO 2026 | <?= $isArabic ? 'حول' : 'About' ?></title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="AboutStyle.css" />
</head>

<?php include 'header.php'; ?>

<body>

<div class="page-container expo-about">
  <div class="about-page">

    <section class="about-hero">
      <h1 id="heroTitle" class="page-title">EXPO 2026</h1>
    </section>

    <main class="expo-container">
      <div class="grid">
        <section class="expo-card span-12">
          <div class="card-header">
            <h2 class="card-title">
              <span class="icon">!</span>
              <span id="aboutTitle">About EXPO 2026</span>
            </h2>
          </div>

          <div class="card-body">
            <p id="aboutText1">The Graduation Projects Exhibition “Expo 2026” at the College of Science and Humanities in Jubail serves as a scientific and creative platform aimed at showcasing students’ research and innovative outputs.</p>
            <p id="aboutText2">The exhibition aligns with Saudi Vision 2030 by supporting scientific research and innovation, and by contributing to building a knowledge-based society capable of producing practical solutions.</p>
            <p id="aboutText3">The exhibition reflects the spirit of collaboration between students and faculty members through high-quality projects that demonstrate deep understanding and innovative solutions.</p>
          </div>
        </section>
      </div>

      <section class="expo-card span-12 expo2025-card">
        <div class="card-header">
          <h2 class="card-title">
            <span class="icon">25</span>
            <span id="expo2025Title">About EXPO 2025</span>
          </h2>
        </div>

        <div class="card-body">
          <p id="expo2025Text">EXPO 2025 was launched in alignment with Saudi Vision 2030 to support the transformation toward a knowledge-based society. It aimed to enhance scientific research, innovation, and creativity among students while providing a platform to present impactful graduation projects.</p>

          <div class="info-block">
            <h3 id="expo2025GoalsTitle">Goals</h3>
            <ul>
              <li id="goal1">Promote scientific research and innovation among students</li>
              <li id="goal2">Develop research and critical thinking skills</li>
              <li id="goal3">Support innovative ideas and entrepreneurial projects</li>
              <li id="goal4">Encourage sustainable development solutions</li>
              <li id="goal5">Prepare students for academic and professional environments</li>
            </ul>
          </div>

          <div class="info-block">
            <h3 id="expo2025TracksTitle">Tracks</h3>
            <ul class="tracks-list">
              <li id="trackHuman">Human Health</li>
              <li id="trackSustainability">Environmental Sustainability & Basic Needs</li>
              <li id="trackEducation">Education & Human Capacity Development</li>
              <li id="trackEnergy">Energy, Industry & Entrepreneurship</li>
              <li id="trackEconomy">Future Economies</li>
            </ul>
          </div>
        </div>
      </section>

      <h3 class="section-title" id="expo2025ResultsTitle">Previous Graduation Projects Exhibition “EXPO 2025” Projects</h3>

      <section class="track-section healthTrack">
        <div class="track-header"><span class="track-icon">+</span><h3 id="healthTrackTitle">Human Health</h3></div>
        <div class="cards-grid"><?php renderProjectsByTrack($conn, 1, $isArabic, $hasArabicProjectColumns); ?></div>
      </section>

      <section class="track-section sustainabilityTrack">
        <div class="track-header"><span class="track-icon">♻</span><h3 id="sustainabilityTrackTitle">Environmental Sustainability & Basic Needs</h3></div>
        <div class="cards-grid"><?php renderProjectsByTrack($conn, 2, $isArabic, $hasArabicProjectColumns); ?></div>
      </section>

      <section class="track-section educationTrack">
        <div class="track-header"><span class="track-icon">✦</span><h3 id="educationTrackTitle">Education & Human Capacity Development</h3></div>
        <div class="cards-grid"><?php renderProjectsByTrack($conn, 3, $isArabic, $hasArabicProjectColumns); ?></div>
      </section>

      <section class="track-section energyTrack">
        <div class="track-header"><span class="track-icon">⚡</span><h3 id="energyTrackTitle">Energy, Industry & Entrepreneurship</h3></div>
        <div class="cards-grid"><?php renderProjectsByTrack($conn, 4, $isArabic, $hasArabicProjectColumns); ?></div>
      </section>

      <section class="track-section economyTrack">
        <div class="track-header"><span class="track-icon">AI</span><h3 id="economyTrackTitle">Future Economies</h3></div>
        <div class="cards-grid"><?php renderProjectsByTrack($conn, 5, $isArabic, $hasArabicProjectColumns); ?></div>
      </section>
    </main>
  </div>
  
</div>

<?php include 'footer.php'; ?>

<script src="main.js"></script>
<script src="AboutScript.js"></script>
</body>
</html>
<?php $conn->close(); ?>
