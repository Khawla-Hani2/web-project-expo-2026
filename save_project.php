<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json');
ini_set('display_errors', 0);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'student') {
        throw new Exception("Access denied.");
    }

    $student_id = (int) $_SESSION['user_id'];
    require 'db.php';

    if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) || 
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        throw new Exception("Invalid security token.");
    }

    $owner_check = $conn->prepare("SELECT id FROM projects WHERE student_id = ?");
    $owner_check->bind_param("i", $student_id);
    $owner_check->execute();
    $owner_check->store_result();
    if ($owner_check->num_rows > 0) {
        $owner_check->close();
        throw new Exception("You have already submitted a project.");
    }
    $owner_check->close();

    $member_check = $conn->prepare("SELECT id FROM project_members WHERE user_id = ?");
    $member_check->bind_param("i", $student_id);
    $member_check->execute();
    $member_check->store_result();
    if ($member_check->num_rows > 0) {
        $member_check->close();
        throw new Exception("You are already a member of another project.");
    }
    $member_check->close();

    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $supervisor  = trim($_POST['supervisor'] ?? '');
    $phone       = trim($_POST['phone'] ?? '');
    $theme       = $_POST['theme'] ?? '';
    $track       = $_POST['track'] ?? '';

    $allowedThemes = ['Health','Economies','Sustainability','Energy','Education','Research'];
    $allowedTracks = ['Computer Science','Physics and Renewable Energy','Mathematics (Statistics and Data Science Program)','English Language','Early Childhood'];

    if ($title === '' || !in_array($theme, $allowedThemes, true) || !in_array($track, $allowedTracks, true)) {
        throw new Exception("Invalid or missing required fields.");
    }

    if (!preg_match('/^05\d{8}$/', $phone)) {
        throw new Exception("Phone must start with 05 followed by 8 digits.");
    }

    $conn->begin_transaction();

    $stmt = $conn->prepare("
        INSERT INTO projects (student_id, title, description, supervisor, phone, theme, track) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("issssss", $student_id, $title, $description, $supervisor, $phone, $theme, $track);
    $stmt->execute();
    $project_id = $stmt->insert_id;
    $stmt->close();

    if (!empty($_POST['member_name']) && is_array($_POST['member_name'])) {
        $memberStmt = $conn->prepare("
            INSERT INTO project_members (project_id, full_name, email, user_id) 
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($_POST['member_name'] as $i => $name) {
            $name = trim($name);
            $email = isset($_POST['member_email'][$i]) ? trim($_POST['member_email'][$i]) : '';
            
            if ($name === '') continue;

            $user_id = null;
            if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $uCheck = $conn->prepare("SELECT id FROM users WHERE email = ?");
                $uCheck->bind_param("s", $email);
                $uCheck->execute();
                $uRes = $uCheck->get_result();
                if ($uRow = $uRes->fetch_assoc()) {
                    $user_id = (int) $uRow['id'];
                }
                $uCheck->close();
            }

            $memberStmt->bind_param("issi", $project_id, $name, $email, $user_id);
            $memberStmt->execute();
        }
        $memberStmt->close();
    }

    $conn->commit();
    
    unset($_SESSION['csrf_token']);

    echo json_encode([
        "status" => "success",
        "message" => "Project submitted successfully!",
        "redirect" => "poster.php"
    ]);
    exit();

} catch (Exception $e) {
    if (isset($conn) && $conn->connect_errno === 0) {
        $conn->rollback();
    }
    error_log("Project save failed: " . $e->getMessage());
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
    exit();
}
?>