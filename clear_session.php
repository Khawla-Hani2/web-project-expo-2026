<?php
session_start();
session_destroy();
echo "Session cleared. <a href='Home.php'>Go to Home</a>";
?>