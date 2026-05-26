<?php
$lang = $_GET['lang'] ?? 'ar';
$lang = ($lang === 'ar') ? 'ar' : 'en';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>" dir="<?= $lang === 'ar' ? 'rtl' : 'ltr' ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EXPO 2026 | <?= $lang === 'ar' ? 'جدول الفعاليات' : 'Event Schedule' ?></title>

  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="EventScheduleStyle.css" />
</head>
<body>

<?php include 'header.php'; ?>

<div class="page-container expo-schedule">
  <div class="schedule-page">

    <section class="schedule-hero">
      <h1 id="heroTitle" class="page-title">Event Schedule</h1>
      <p id="scheduleHeroText" class="schedule-hero-text">
        Explore the main events and important dates for EXPO 2026.
      </p>
    </section>

    <main class="schedule-container">
      <section class="alt-timeline" aria-label="EXPO 2026 Event Timeline">

        <div class="alt-item left">
          <div class="alt-marker" aria-hidden="true"></div>
          <div class="alt-connector" aria-hidden="true"></div>
          <div class="side">
            <article class="expo-card">
              <div class="expo-card-body">
                <div class="meta">
                  <div class="date" id="e1Date">May 10, 2026 — 10:00 AM</div>
                  <span class="badge gold" id="e1Badge">Opening</span>
                </div>
                <h3 class="title" id="e1Title">Opening Ceremony</h3>
                <p class="desc" id="e1Desc">The official opening of EXPO 2026 with keynote speakers and special guests.</p>
              </div>
            </article>
          </div>
        </div>

        <div class="alt-item right">
          <div class="alt-marker" aria-hidden="true"></div>
          <div class="alt-connector" aria-hidden="true"></div>
          <div class="side">
            <article class="expo-card">
              <div class="expo-card-body">
                <div class="meta">
                  <div class="date" id="e2Date">May 12, 2026 — 2:00 PM</div>
                  <span class="badge olive" id="e2Badge">Showcase</span>
                </div>
                <h3 class="title" id="e2Title">Innovation Expo & Project Showcase</h3>
                <p class="desc" id="e2Desc">Participants present their projects and ideas across the official EXPO tracks.</p>
              </div>
            </article>
          </div>
        </div>

        <div class="alt-item left">
          <div class="alt-marker" aria-hidden="true"></div>
          <div class="alt-connector" aria-hidden="true"></div>
          <div class="side">
            <article class="expo-card">
              <div class="expo-card-body">
                <div class="meta">
                  <div class="date" id="e3Date">May 15, 2026 — 11:00 AM</div>
                  <span class="badge navy" id="e3Badge">Judging</span>
                </div>
                <h3 class="title" id="e3Title">Judging & Evaluation Day</h3>
                <p class="desc" id="e3Desc">Projects are reviewed and evaluated by the judging panel using consistent criteria.</p>
              </div>
            </article>
          </div>
        </div>

        <div class="alt-item right">
          <div class="alt-marker" aria-hidden="true"></div>
          <div class="alt-connector" aria-hidden="true"></div>
          <div class="side">
            <article class="expo-card">
              <div class="expo-card-body">
                <div class="meta">
                  <div class="date" id="e4Date">May 20, 2026 — 5:00 PM</div>
                  <span class="badge plum" id="e4Badge">Closing</span>
                </div>
                <h3 class="title" id="e4Title">Awards & Closing Ceremony</h3>
                <p class="desc" id="e4Desc">Winners are announced and EXPO 2026 officially concludes.</p>
              </div>
            </article>
          </div>
        </div>

      </section>
    </main>
  </div>
</div>

<?php include 'partials/footer.php'; ?>

<script src="main.js" defer></script>
<script src="EventScheduleScript.js" defer></script>
</body>
</html>
