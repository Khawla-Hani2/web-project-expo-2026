<?php
declare(strict_types=1);
session_start();

// Generate CSRF token if missing (MUST be before any output)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Auth Guard
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: Home.php');
    exit();
}

$student_id = (int) $_SESSION['user_id'];
require 'db.php';

// Check if student already owns a project
$owner_check = $conn->prepare("SELECT * FROM projects WHERE student_id = ?");
$owner_check->bind_param("i", $student_id);
$owner_check->execute();
$owner_result = $owner_check->get_result();
$existing_project = $owner_result->fetch_assoc();
$owner_check->close();

// Check if student is a member of another project
$member_check = $conn->prepare("
    SELECT pm.*, p.title as project_title 
    FROM project_members pm 
    JOIN projects p ON pm.project_id = p.id 
    WHERE pm.user_id = ?
");
$member_check->bind_param("i", $student_id);
$member_check->execute();
$member_result = $member_check->get_result();
$is_member_elsewhere = ($member_result->num_rows > 0);
$member_project = $is_member_elsewhere ? $member_result->fetch_assoc() : null;
$member_check->close();

// Determine mode
$can_create = !$existing_project && !$is_member_elsewhere;

// If owner, fetch members
$members = [];
if ($existing_project) {
    $m_stmt = $conn->prepare("SELECT full_name, email FROM project_members WHERE project_id = ? ORDER BY id ASC");
    $m_stmt->bind_param("i", $existing_project['id']);
    $m_stmt->execute();
    $m_res = $m_stmt->get_result();
    while ($row = $m_res->fetch_assoc()) {
        $members[] = $row;
    }
    $m_stmt->close();
}

$pageTitle = "Expo 2026 | Student Data";
?>
<?php include 'header.php'; ?>

<link rel="stylesheet" href="Studentdata.css">

<style>
    .alert-box {
      background: #fff3cd;
      border: 1px solid #ffc107;
      color: #856404;
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 20px;
      text-align: center;
    }
    .alert-box.member {
      background: #d1ecf1;
      border-color: #17a2b8;
      color: #0c5460;
    }
    .project-view-card {
      background: #fff;
      border: 1px solid #ddd;
      padding: 30px;
      border-radius: 16px;
      line-height: 1.8;
      max-width: 700px;
      margin: 0 auto;
    }
    .project-view-card h3 {
      color: #632949;
      margin-bottom: 15px;
      font-size: 24px;
    }
    .project-view-card .meta {
      color: #777;
      font-size: 0.9em;
      margin-top: 16px;
      border-top: 1px solid #eee;
      padding-top: 10px;
    }
    .member-tag {
      display: inline-block;
      background: #632949;
      color: #fff;
      padding: 4px 12px;
      border-radius: 16px;
      font-size: 13px;
      margin: 2px;
    }
    .poster-link {
      display: inline-block;
      margin-top: 15px;
      padding: 10px 20px;
      background: #836F24;
      color: #fff;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 600;
    }
    .poster-link:hover {
      background: #6b5a1e;
    }
    label {
      display: block;
      margin: 16px 0 6px;
      font-weight: 600;
      color: #4a4a4a;
    }
    input, textarea, select {
      width: 100%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      box-sizing: border-box;
      font-family: inherit;
      font-size: 14px;
    }
    input:focus, textarea:focus, select:focus {
      outline: none;
      border-color: #632949;
      box-shadow: 0 0 0 3px rgba(99, 41, 73, 0.1);
    }
    button[type="submit"] {
      margin-top: 20px;
      padding: 14px 24px;
      background: #632949;
      color: #fff;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      font-size: 16px;
      width: 100%;
    }
    button[type="submit"]:hover {
      background: #4a1e35;
    }
    .member-row-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
      margin-bottom: 12px;
    }
    @media (max-width: 600px) {
      .member-row-grid { grid-template-columns: 1fr; }
    }
    .member-row-grid label {
      margin-top: 0;
      font-size: 13px;
    }
    .member-row-grid input {
      padding: 10px;
    }
</style>

<div class="main-content" style="padding: 40px 20px;">

    <?php if (!$can_create && $is_member_elsewhere): ?>
      <!-- MEMBER OF ANOTHER PROJECT -->
      <div class="alert-box member">
        <h3 data-en="You are a team member" data-ar="أنت عضو في فريق">You are a team member</h3>
        <p data-en="You are already registered as a member on the project:" data-ar="أنت مسجل بالفعل كعضو في المشروع:">
          You are already registered as a member on the project:
        </p>
        <strong style="font-size: 18px; color: #632949;"><?php echo htmlspecialchars($member_project['project_title']); ?></strong>
        <p style="margin-top: 10px; font-size: 13px;" data-en="Team members cannot create separate projects." data-ar="لا يمكن للأعضاء إنشاء مشاريع منفصلة.">
          Team members cannot create separate projects.
        </p>
      </div>

    <?php elseif (!$can_create && $existing_project): ?>
      <!-- ALREADY OWNS A PROJECT -->
      <div class="project-view-card">
        <h3 data-en="Your Project" data-ar="مشروعك">Your Project</h3>
        <p><strong data-en="Title:" data-ar="العنوان:">Title:</strong> <?php echo htmlspecialchars($existing_project['title']); ?></p>
        <p><strong data-en="Description:" data-ar="الوصف:">Description:</strong> <?php echo nl2br(htmlspecialchars($existing_project['description'] ?? '')); ?></p>
        <p><strong data-en="Supervisor:" data-ar="المشرف:">Supervisor:</strong> <?php echo htmlspecialchars($existing_project['supervisor'] ?? 'N/A'); ?></p>
        <p><strong data-en="Theme:" data-ar="الموضوع:">Theme:</strong> <?php echo htmlspecialchars($existing_project['theme']); ?></p>
        <p><strong data-en="Department:" data-ar="القسم:">Department:</strong> <?php echo htmlspecialchars($existing_project['track']); ?></p>
        
        <?php if (!empty($members)): ?>
          <p><strong data-en="Members:" data-ar="الأعضاء:">Members:</strong></p>
          <?php foreach ($members as $m): ?>
            <span class="member-tag"><?php echo htmlspecialchars($m['full_name']); ?></span>
          <?php endforeach; ?>
        <?php endif; ?>

        <div class="meta">
          <span data-en="Submitted:" data-ar="تم التقديم:">Submitted:</span> <?php echo $existing_project['created_at']; ?>
        </div>

        <a href="poster.php" class="poster-link" data-en="Upload Poster →" data-ar="رفع البوستر →">Upload Poster →</a>
      </div>

    <?php else: ?>
      <!-- CREATE FORM -->
      <h2 style="text-align: center; color: #632949; margin-bottom: 30px;" data-en="Project Data" data-ar="بيانات المشروع">Project Data</h2>

      <form id="projectForm" style="max-width: 700px; margin: 0 auto;">
        <!-- CSRF TOKEN: CRITICAL -->
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

        <label data-en="Project Title" data-ar="عنوان المشروع">Project Title</label>
        <input type="text" name="title" placeholder="Project Title" required data-en-placeholder="Project Title" data-ar-placeholder="عنوان المشروع">

        <label data-en="Project Description" data-ar="وصف المشروع">Project Description</label>
        <textarea name="description" rows="4" placeholder="Project Description" data-en-placeholder="Project Description" data-ar-placeholder="وصف المشروع"></textarea>

        <label data-en="Supervisor Name" data-ar="اسم المشرف">Supervisor Name</label>
        <input type="text" name="supervisor" placeholder="Supervisor Name" data-en-placeholder="Supervisor Name" data-ar-placeholder="اسم المشرف">

        <label data-en="Phone Number" data-ar="رقم الجوال">Phone Number</label>
        <input type="tel" name="phone" placeholder="05XXXXXXXX" pattern="^05\d{8}$" required data-en-placeholder="05XXXXXXXX" data-ar-placeholder="05XXXXXXXX">

        <label data-en="Project Theme" data-ar="موضوع المشروع">Project Theme</label>
        <select name="theme" required>
          <option value="" data-en="Select Theme" data-ar="اختر الموضوع">Select Theme</option>
          <option value="Health" data-en="Health" data-ar="الصحة">Health</option>
          <option value="Economies" data-en="Economies" data-ar="الاقتصاد">Economies</option>
          <option value="Sustainability" data-en="Sustainability" data-ar="الاستدامة">Sustainability</option>
          <option value="Energy" data-en="Energy" data-ar="الطاقة">Energy</option>
          <option value="Education" data-en="Education" data-ar="التعليم">Education</option>
          <option value="Research" data-en="Research" data-ar="البحث">Research</option>
        </select>

        <label data-en="Department" data-ar="القسم">Department</label>
        <select name="track" required>
          <option value="Computer Science" data-en="Computer Science" data-ar="علوم الحاسب">Computer Science</option>
          <option value="Physics and Renewable Energy" data-en="Physics and Renewable Energy" data-ar="الفيزياء والطاقة المتجددة">Physics and Renewable Energy</option>
          <option value="Mathematics (Statistics and Data Science Program)" data-en="Mathematics" data-ar="الرياضيات">Mathematics</option>
          <option value="English Language" data-en="English Language" data-ar="اللغة الإنجليزية">English Language</option>
          <option value="Early Childhood" data-en="Kindergarten" data-ar="رياض الأطفال">Kindergarten</option>
        </select>

        <label data-en="Number of Members" data-ar="عدد الأعضاء">Number of Members</label>
        <input type="number" id="memberCount" name="memberCount" placeholder="Including yourself" min="1" max="10" oninput="generateMembers()" data-en-placeholder="Including yourself" data-ar-placeholder="بما فيهم نفسك">

        <div id="membersContainer"></div>

        <button type="submit" id="submitBtn" data-en="Submit Project" data-ar="تقديم المشروع">Submit Project</button>
      </form>
    <?php endif; ?>

</div>

<?php include 'footer.php'; ?>
<script src="Studentdata.js"></script>