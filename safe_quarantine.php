<?php
declare(strict_types=1);

$legacyDir = __DIR__ . '/_legacy';
if (!is_dir($legacyDir)) {
    mkdir($legacyDir, 0755, true);
    echo "<p>Created <code>_legacy/</code> folder.</p>";
}

$toMove = [
    // ── Category landing pages (6 files) ──
    'human_health.php',
    'future_economics.php',
    'sustainability.php',
    'energy.php',
    'education.php',
    'researches.php',

    // ── Health department projects ──
    'health_computer_projects.php',
    'health_english_projects.php',
    'health_kindergarden_projects.php',
    'health_math_projects.php',
    'health_physics_projects.php',

    // ── Economies (eco_*) department projects ──
    'eco_computer_projects.php',
    'eco_english_projects.php',
    'eco_kindergarden_projects.php',
    'eco_math_projects.php',
    'eco_physics_projects.php',

    // ── Education (edu_*) department projects ──
    'edu_computer_projects.php',
    'edu_english_projects.php',
    'edu_kindergarden_projects.php',
    'edu_math_projects.php',
    'edu_physics_projects.php',

    // ── Energy department projects ──
    'energy_computer_projects.php',
    'energy_english_projects.php',
    'energy_kindergarden_projects.php',
    'energy_math_projects.php',
    'energy_physics_projects.php',

    // ── Sustainability (env_*) department projects ──
    'env_computer_projects.php',
    'env_english_projects.php',
    'env_kindergarden_projects.php',
    'env_math_projects.php',
    'env_physics_projects.php',

    // ── Research department projects ──
    'research_computer_projects.php',
    'research_english_projects.php',
    'research_kindergarden_projects.php',
    'research_math_projects.php',
    'research_physics_projects.php',
];

$moved = 0;
$missing = 0;

echo "<h2>Quarantine Report</h2><ul>";

foreach ($toMove as $file) {
    $src = __DIR__ . '/' . $file;
    $dst = $legacyDir . '/' . $file;
    
    if (file_exists($src)) {
        if (rename($src, $dst)) {
            echo "<li>✅ Moved <strong>$file</strong> → <code>_legacy/$file</code></li>";
            $moved++;
        } else {
            echo "<li>❌ Failed to move <strong>$file</strong> (check permissions)</li>";
        }
    } else {
        echo "<li>⚠️ Not found: <strong>$file</strong> (already removed?)</li>";
        $missing++;
    }
}

echo "</ul>";
echo "<p><strong>$moved</strong> files quarantined. <strong>$missing</strong> already gone.</p>";
echo "<p>If the site breaks, drag files back from <code>_legacy/</code> to the root folder.</p>";
echo "<p><strong>Next:</strong> Delete this script (<code>safe_quarantine.php</code>) after reading this page.</p>";