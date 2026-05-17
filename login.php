<?php
declare(strict_types=1);
session_start();

header("Content-Type: application/json");

require 'db.php';

$input = file_get_contents("php://input");
$data  = json_decode($input, true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "No data received"]);
    exit();
}

$email    = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(["status" => "error", "message" => "Email and password are required"]);
    exit();
}

$stmt = $conn->prepare("
    SELECT id, firstName, lastName, password, role, email, is_active
    FROM users
    WHERE email = ?
");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Email not found"]);
    exit();
}

$user = $result->fetch_assoc();

// Block inactive accounts
if ((int)$user['is_active'] !== 1) {
    echo json_encode([
        "status" => "error",
        "message" => "Account not activated. Please check your email for the verification link."
    ]);
    exit();
}

if (!password_verify($password, $user['password'])) {
    echo json_encode(["status" => "error", "message" => "Incorrect password"]);
    exit();
}

session_regenerate_id(true);

$_SESSION['user_id'] = (int) $user['id'];
$_SESSION['role']    = $user['role'];
$_SESSION['email']   = $user['email'];
$_SESSION['name']    = $user['firstName'] . ' ' . $user['lastName'];

echo json_encode([
    "status" => "success",
    "message" => "Logged in successfully"
]);

$stmt->close();
$conn->close();
?>