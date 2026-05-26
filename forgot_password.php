<?php
session_start();
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "expo2026_new");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB error"]);
    exit();
}

$input = file_get_contents("php://input");
$data  = json_decode($input, true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "No data received"]);
    exit();
}

$action = $data['action'] ?? '';

// STEP 1:Email verification
if ($action === "check_email") {

    $email = $data['email'] ?? '';

    if (!$email) {
        echo json_encode(["status" => "error", "message" => "Email is required"]);
        exit();
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => $conn->error
    ]);
    exit();
}

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Email not found"]);
        exit();
    }

    $_SESSION['reset_email'] = $email;

    echo json_encode(["status" => "success"]);
    exit();
}
// STEP 2: Change password
if ($action === "reset_password") {

    $email    = $_SESSION['reset_email'] ?? '';
    $password = $data['password'] ?? '';

    if (!$email) {
        echo json_encode(["status" => "error", "message" => "Unauthorized request"]);
        exit();
    }

    if (!$password || strlen($password) < 6) {
        echo json_encode(["status" => "error", "message" => "Password too short"]);
        exit();
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashed, $email);

    if ($stmt->execute()) {
        unset($_SESSION['reset_email']);
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update password"]);
    }

    exit();
}

echo json_encode(["status" => "error", "message" => "Invalid action"]);
?>