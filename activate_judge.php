<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Check if passwords match
    if ($new_password !== $confirm_password) {
        die("Passwords do not match. Please go back and try again.");
    }

    // 2. Hash the new password securely
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    // 3. Update the database: Set password, make active, and DESTROY the token
    $stmt = $conn->prepare("UPDATE users SET password = ?, is_active = 1, verification_token = NULL WHERE verification_token = ? AND role = 'judge' AND is_active = 0");
    $stmt->bind_param("ss", $hashed_password, $token);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        // Success! Send them to the login page
        echo "<div style='text-align:center; margin-top:50px; font-family:sans-serif;'>";
        echo "<h2 style='color:green;'>Account Successfully Activated!</h2>";
        echo "<p>You can now log in to the EXPO IAU 2026 Portal.</p>";
        echo "<a href='login.html' style='padding:10px 20px; background:#6c1c43; color:white; text-decoration:none; border-radius:5px;'>Go to Login</a>";
        echo "</div>";
    } else {
        echo "<h2 style='text-align:center; color:red; margin-top:50px;'>Error activating account. The link may have expired.</h2>";
    }
    
    $stmt->close();
}
?>