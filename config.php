<?php
/**
 * EXPO2026 — Unified Configuration
 * Master mapping for categories and departments
 * Include with: require_once 'config.php';
 */

if (!defined('EXPO2026_CONFIG')) {
    define('EXPO2026_CONFIG', true);
}

/* ============================================================
   CATEGORY MAP
   slug => [db_theme, image, color, hover, shadow_rgb, titles]
   ============================================================ */
$CATEGORY_MAP = [
    'health' => [
        'slug'     => 'health',
        'db_theme' => 'Health',
        'image'    => 'Human_Health.png',
        'color'    => '#6b1a3a',
        'hover'    => '#842a4e',
        'shadow'   => '107, 26, 58',
        'title_ar' => 'صحة الإنسان',
        'title_en' => 'Human Health',
    ],
    'economies' => [
        'slug'     => 'economies',
        'db_theme' => 'Economies',
        'image'    => 'Future_Economies.png',
        'color'    => '#2c3e5e',
        'hover'    => '#3a4f75',
        'shadow'   => '44, 62, 94',
        'title_ar' => 'اقتصاديات المستقبل',
        'title_en' => 'Future Economics',
    ],
    'sustainability' => [
        'slug'     => 'sustainability',
        'db_theme' => 'Sustainability',
        'image'    => 'Environmental_Sustainability.png',
        'color'    => '#484F2A',
        'hover'    => '#586035',
        'shadow'   => '72, 79, 42',
        'title_ar' => 'استدامة البيئة',
        'title_en' => 'Sustainability',
    ],
    'energy' => [
        'slug'     => 'energy',
        'db_theme' => 'Energy',
        'image'    => 'Energy_and_Industry.png',
        'color'    => '#836F24',
        'hover'    => '#9a832b',
        'shadow'   => '131, 111, 36',
        'title_ar' => 'الطاقة والصناعة',
        'title_en' => 'Energy',
    ],
    'education' => [
        'slug'     => 'education',
        'db_theme' => 'Education',
        'image'    => 'Education_and_Development.png',
        'color'    => '#662515',
        'hover'    => '#782d1b',
        'shadow'   => '102, 37, 21',
        'title_ar' => 'التعليم وتنمية القدرات البشرية',
        'title_en' => 'Education',
    ],
    'research' => [
        'slug'     => 'research',
        'db_theme' => 'Research',
        'image'    => 'Research_Publications.png',
        'color'    => '#758090',
        'hover'    => '#a1a8b2',
        'shadow'   => '117, 128, 144',
        'title_ar' => 'الأبحاث العلمية المنشورة',
        'title_en' => 'Research',
    ],
];

/* Reverse lookup: db_theme => category data */
$CATEGORY_BY_THEME = [];
foreach ($CATEGORY_MAP as $slug => $data) {
    $CATEGORY_BY_THEME[$data['db_theme']] = $data;
}

/* ============================================================
   DEPARTMENT MAP
   slug => [db_track, titles]
   ============================================================ */
$DEPARTMENT_MAP = [
    'computer-science' => [
        'slug'     => 'computer-science',
        'db_track' => 'Computer Science',
        'title_ar' => 'علوم الحاسب الآلي',
        'title_en' => 'Computer Science',
    ],
    'english' => [
        'slug'     => 'english',
        'db_track' => 'English',
        'title_ar' => 'اللغة الإنقليزية',
        'title_en' => 'English',
    ],
    'kindergarten' => [
        'slug'     => 'kindergarten',
        'db_track' => 'Kindergarten',
        'title_ar' => 'الطفولة المبكرة',
        'title_en' => 'Kindergarten',
    ],
    'mathematics' => [
        'slug'     => 'mathematics',
        'db_track' => 'Mathematics',
        'title_ar' => 'الرياضيات',
        'title_en' => 'Mathematics',
    ],
    'physics-renewable' => [
        'slug'     => 'physics-renewable',
        'db_track' => 'Physics and Renewable Energy',
        'title_ar' => 'الفيزياء والطاقة المتجددة',
        'title_en' => 'Physics and Renewable Energy',
    ],
];

/* Reverse lookup: db_track => department data */
$DEPARTMENT_BY_TRACK = [];
foreach ($DEPARTMENT_MAP as $slug => $data) {
    $DEPARTMENT_BY_TRACK[$data['db_track']] = $data;
}

/* ============================================================
   HELPER FUNCTIONS
   ============================================================ */

/** Get category array by slug, or null */
function getCategory(string $slug): ?array {
    global $CATEGORY_MAP;
    return $CATEGORY_MAP[$slug] ?? null;
}

/** Get department array by slug, or null */
function getDepartment(string $slug): ?array {
    global $DEPARTMENT_MAP;
    return $DEPARTMENT_MAP[$slug] ?? null;
}

/** Get all departments as a zero-indexed array */
function getAllDepartments(): array {
    global $DEPARTMENT_MAP;
    return array_values($DEPARTMENT_MAP);
}

/** Safe HTTP redirect */
function safeRedirect(string $url): void {
    header("Location: $url");
    exit;
}

/** Sanitize GET/POST parameter */
function sanitizeParam(?string $input): string {
    if ($input === null) return '';
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
