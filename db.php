<?php
$conn = mysqli_connect("localhost", "root", "", "expo2026_new");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>