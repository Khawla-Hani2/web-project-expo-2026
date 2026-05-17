<?php
$legacyDir = __DIR__ . '/_legacy';
if (!is_dir($legacyDir)) { echo "Nothing to restore."; exit; }

$files = glob($legacyDir . '/*.php');
$restored = 0;
foreach ($files as $file) {
    $name = basename($file);
    rename($file, __DIR__ . '/' . $name);
    $restored++;
}
echo "Restored $restored files from _legacy/.";
rmdir($legacyDir);
?>