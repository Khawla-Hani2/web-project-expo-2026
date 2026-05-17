<?php
declare(strict_types=1);
require 'db.php';

// GET request: Show the setup form
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $token = $_GET['token'] ?? '';

    if (!$token) {
        die("<h2 style='text-align:center; color:red; margin-top:50px;'>Invalid invitation link.</h2>");
    }

    // Verify token exists and belongs to a pending judge
    $stmt = $conn->prepare("SELECT firstName, lastName, email FROM users WHERE verification_token = ? AND role = 'judge' AND is_active = 0");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("<h2 style='text-align:center; color:red; margin-top:50px;'>This invitation link has expired or already been used.</h2>");
    }

    $user = $result->fetch_assoc();
    $stmt->close();
    ?>
    <!DOCTYPE html>
    <html lang="en" dir="ltr">
    <head>
      <meta charset="UTF-8">
      <title>Setup Judge Account | EXPO IAU 2026</title>
      <style>
        body { margin:0; padding:0; background:#f5f5f5; font-family: Arial, sans-serif; display:flex; justify-content:center; align-items:center; min-height:100vh; }
        .container { background:#fff; padding:40px; border-radius:16px; box-shadow: 0 8px 30px rgba(0,0,0,0.1); max-width:400px; width:90%; }
        h2 { color:#632949; margin-bottom:20px; }
        label { display:block; margin-top:15px; font-weight:600; color:#333; }
        input { width:100%; padding:12px; margin-top:5px; border:1px solid #ddd; border-radius:8px; box-sizing:border-box; }
        button { width:100%; padding:14px; margin-top:20px; background:#632949; color:#fff; border:none; border-radius:8px; font-weight:bold; cursor:pointer; }
        button:hover { background:#4a1e35; }
        .info { background:#fdf7fa; padding:15px; border-radius:8px; margin-bottom:20px; font-size:14px; color:#555; }
      </style>
    </head>
    <body>
      <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($user['firstName']); ?>!</h2>
        <div class="info">
          <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?><br>
          Please create a secure password to activate your judge account.
        </div>
        <form action="setup_judge.php" method="POST">
          <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
          
          <label for="new_password">Password</label>
          <input type="password" id="new_password" name="new_password" required minlength="6">
          
          <label for="confirm_password">Confirm Password</label>
          <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
          
          <button type="submit">Activate Account</button>
        </form>
      </div>
    </body>
    </html>
    <?php
    exit();
}

// POST request: Process password setup
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST['token'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!$token || !$new_password || !$confirm_password) {
        die("<h2 style='text-align:center; color:red; margin-top:50px;'>All fields are required.</h2>");
    }

    if ($new_password !== $confirm_password) {
        die("<h2 style='text-align:center; color:red; margin-top:50px;'>Passwords do not match. <a href='javascript:history.back()'>Go back</a></h2>");
    }

    if (strlen($new_password) < 6) {
        die("<h2 style='text-align:center; color:red; margin-top:50px;'>Password must be at least 6 characters.</h2>");
    }

    $hashed = password_hash($new_password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        UPDATE users 
        SET password = ?, is_active = 1, verification_token = NULL 
        WHERE verification_token = ? AND role = 'judge' AND is_active = 0
    ");
    $stmt->bind_param("ss", $hashed, $token);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<div style='text-align:center; margin-top:50px; font-family:sans-serif;'>";
        echo "<h2 style='color:green;'>Account Successfully Activated!</h2>";
        echo "<p>You can now log in to the EXPO IAU 2026 Portal.</p>";
        echo "<a href='login.html' style='padding:12px 24px; background:#632949; color:white; text-decoration:none; border-radius:8px; font-weight:bold;'>Go to Login</a>";
        echo "</div>";
    } else {
        echo "<h2 style='text-align:center; color:red; margin-top:50px;'>Error activating account. The link may have expired or already been used.</h2>";
    }

    $stmt->close();
    $conn->close();
}
?>