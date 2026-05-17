<?php
declare(strict_types=1);

if (!isset($config)) {
    $config = require 'config.php';
}

$categories = $config['categories'];
$role       = $_SESSION['role'] ?? 'guest';
$lang       = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'ar');
$_SESSION['lang'] = $lang;
$isAr       = ($lang === 'ar');
?>

<div class="top-strip">
  <div class="logoL"><img src="IAU_logo_white.png" alt="IAU Logo"></div>

  <?php if ($role === 'guest') { ?>
    <a href="signup.php" class="seg s1">
      <img src="user_vector.png" alt="">
      <span id="t1" data-en="Sign-up" data-ar="تسجيل">Sign-up</span>
    </a>
  <?php } else { ?>
    <a href="logout.php" class="seg s1">
      <img src="user_vector.png" alt="">
      <span id="t1" data-en="Logout" data-ar="تسجيل الخروج">Logout</span>
    </a>
  <?php } ?>

  <?php 
  $i = 2;
  foreach ($categories as $slug => $cat): 
    $tid = 't' . $i;
    $label = $isAr ? $cat['title_ar'] : $cat['title_en'];
  ?>
    <a href="category.php?cat=<?= $slug ?>&lang=<?= $lang ?>" class="seg <?= $cat['nav_class'] ?>">
      <img src="<?= $cat['nav_icon'] ?>" alt="">
      <span id="<?= $tid ?>" data-en="<?= htmlspecialchars($cat['title_en']) ?>" data-ar="<?= htmlspecialchars($cat['title_ar']) ?>">
        <?= htmlspecialchars($label) ?>
      </span>
    </a>
  <?php 
    $i++;
  endforeach; 
  ?>

  <div class="logoR">
    <img src="expo2026_<?= $isAr ? 'ar' : 'en' ?>_white.png" id="expoLogo" alt="Expo Logo">
  </div>
</div>

<div class="subbar">
  <div class="menu-btn" onclick="toggleMenu(event)">
    <div></div><div></div><div></div>
  </div>
  <div class="lang-btn" onclick="toggleLang()" title="تغيير اللغة / Change Language">
    <img src="lang.png" alt="Language">
  </div>
</div>