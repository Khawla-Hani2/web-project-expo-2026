<?php
declare(strict_types=1);
session_start();

require 'db.php';

if (!isset($_GET['token']) || empty($_GET['token'])) {
    die("No verification token provided.");
}

$token = $_GET['token'];

$stmt = $conn->prepare("SELECT id, firstName, lastName, email, role, is_active FROM users WHERE verification_token = ? LIMIT 1");
if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div style='text-align:center; margin-top:50px; font-family:sans-serif;'>";
    echo "<h2 style='color:red;'>Invalid Verification Link</h2>";
    echo "<p>This link may have expired or has already been used.</p>";
    echo "<p><a href='login.html' style='color:#632949;'>Go to Login</a></p>";
    echo "</div>";
    exit();
}

$user = $result->fetch_assoc();

if ((int)$user['is_active'] === 1) {
    echo "<div style='text-align:center; margin-top:50px; font-family:sans-serif;'>";
    echo "<h2>This account is already active!</h2>";
    echo "<p><a href='login.html' style='color:#632949; font-weight:bold;'>Click here to Log In</a></p>";
    echo "</div>";
    exit();
}

// Activate and burn the token
$updateStmt = $conn->prepare("UPDATE users SET is_active = 1, verification_token = NULL WHERE id = ?");
$updateStmt->bind_param("i", $user['id']);
$updateStmt->execute();
$updateStmt->close();

// Create session only (no cookies needed)
session_regenerate_id(true);
$_SESSION['user_id'] = (int) $user['id'];
$_SESSION['name']    = $user['firstName'] . " " . $user['lastName'];
$_SESSION['email']   = $user['email'];
$_SESSION['role']    = $user['role'];

header("Location: Home.php?activated=true");
exit();
?>