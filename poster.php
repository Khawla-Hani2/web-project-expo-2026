<?php
declare(strict_types=1);
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'guest';

require 'db.php';

// Fetch the student's single project
$project = null;
if ($role === 'student') {
    $proj_stmt = $conn->prepare("SELECT id, title, poster_pdf, poster_ppt FROM projects WHERE student_id = ?");
    $proj_stmt->bind_param("i", $user_id);
    $proj_stmt->execute();
    $proj_res = $proj_stmt->get_result();
    $project = $proj_res->fetch_assoc();
    $proj_stmt->close();
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $role === 'student' && $project) {
    
    // CSRF check
    if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) || 
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid security token. Please refresh and try again.");
    }

    $project_id = (int) $project['id'];

    // Validate files
    $pdf = $_FILES['poster_pdf'] ?? null;
    $ppt = $_FILES['poster_ppt'] ?? null;

    if (!$pdf || $pdf['error'] !== UPLOAD_ERR_OK || !$ppt || $ppt['error'] !== UPLOAD_ERR_OK) {
        die("Both files are required.");
    }

    // Check extensions
    $pdf_ext = strtolower(pathinfo($pdf['name'], PATHINFO_EXTENSION));
    $ppt_ext = strtolower(pathinfo($ppt['name'], PATHINFO_EXTENSION));
    
    if ($pdf_ext !== 'pdf') {
        die("PDF file must have .pdf extension.");
    }
    
    if (!in_array($ppt_ext, ['ppt', 'pptx'], true)) {
        die("Presentation must be .ppt or .pptx");
    }

    // Verify PDF magic bytes
    $pdf_handle = fopen($pdf['tmp_name'], 'rb');
    $pdf_magic = fread($pdf_handle, 4);
    fclose($pdf_handle);
    if ($pdf_magic !== '%PDF') {
        die("Invalid PDF file.");
    }

    // Create upload folder
    $folder = __DIR__ . "/poster/";
    if (!is_dir($folder)) {
        mkdir($folder, 0755, true);
    }

    // Generate safe filenames
    $timestamp = time();
    $pdf_name = $timestamp . "_pdf_" . bin2hex(random_bytes(8)) . ".pdf";
    $ppt_name = $timestamp . "_ppt_" . bin2hex(random_bytes(8)) . "." . $ppt_ext;

    $pdf_path = $folder . $pdf_name;
    $ppt_path = $folder . $ppt_name;

    $pdf_db = "poster/" . $pdf_name;
    $ppt_db = "poster/" . $ppt_name;

    // Move files
    if (!move_uploaded_file($pdf['tmp_name'], $pdf_path) || 
        !move_uploaded_file($ppt['tmp_name'], $ppt_path)) {
        die("File upload failed. Please try again.");
    }

    // Save to database
    $update = $conn->prepare("UPDATE projects SET poster_pdf = ?, poster_ppt = ? WHERE id = ?");
    $update->bind_param("ssi", $pdf_db, $ppt_db, $project_id);
    $update->execute();
    $update->close();

    // Regenerate CSRF and redirect
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['upload_success'] = true;
    header("Location: poster.php");
    exit();
}

// Generate CSRF token for form
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

// Fetch projects for preview (student sees own, judge sees all)
if ($role === 'student') {
    $stmt = $conn->prepare("SELECT * FROM projects WHERE student_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM projects ORDER BY created_at DESC");
}

$pageTitle = "Expo 2026 | Poster";
?>
<?php include 'header.php'; ?>

<style>
    :root {
        --main-burgundy: #632949;
        --accent-gold: #836F24;
        --bg-light: #F5F5F5;
    }
    .poster-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
    }
    @media (max-width: 900px) {
        .poster-container { grid-template-columns: 1fr; }
    }
    .upload-section, .preview-section {
        background: #fff;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .page-title {
        color: var(--main-burgundy);
        font-size: 24px;
        margin-bottom: 20px;
        text-align: center;
    }
    .project-name {
        text-align: center;
        color: #632949;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        padding: 12px;
        background: #fdf7fa;
        border-radius: 8px;
    }
    .upload-title {
        display: block;
        margin: 16px 0 8px;
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }
    .file-upload {
        width: 100%;
        padding: 12px;
        border: 2px dashed #ccc;
        border-radius: 8px;
        cursor: pointer;
        transition: border-color 0.3s;
    }
    .file-upload:hover {
        border-color: var(--main-burgundy);
    }
    .upload-btn {
        width: 100%;
        padding: 14px;
        margin-top: 20px;
        background: var(--main-burgundy);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }
    .upload-btn:hover {
        background: #4a1e35;
        transform: translateY(-2px);
    }
    .upload-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    .pdf-preview-card {
        background: #fafafa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .preview-title {
        color: var(--main-burgundy);
        font-size: 18px;
        margin-bottom: 10px;
    }
    .preview-iframe {
        width: 100%;
        height: 300px;
        border: 1px solid #ddd;
        border-radius: 8px;
        margin: 10px 0;
    }
    .preview-btn {
        display: inline-block;
        padding: 10px 20px;
        background: var(--accent-gold);
        color: #fff;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        margin-right: 10px;
        margin-top: 5px;
    }
    html[dir="rtl"] .preview-btn {
        margin-right: 0;
        margin-left: 10px;
    }
    .missing-file {
        color: #999;
        font-style: italic;
        padding: 10px 0;
    }
    .success-toast {
        background: #d4edda;
        color: #155724;
        padding: 14px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: center;
        font-weight: 600;
    }
    .no-project {
        text-align: center;
        color: #999;
        padding: 40px 20px;
    }
    .no-project a {
        color: #632949;
        font-weight: 600;
    }
</style>

<div class="poster-container">

    <!-- UPLOAD SECTION -->
    <div class="upload-section">
        
        <?php if (isset($_SESSION['upload_success'])): ?>
            <div class="success-toast" data-en="Files uploaded successfully!" data-ar="تم رفع الملفات بنجاح!">Files uploaded successfully!</div>
            <?php unset($_SESSION['upload_success']); ?>
        <?php endif; ?>

        <?php if ($role !== "student"): ?>
            <div class="no-project">
                <p data-en="Only students can upload files." data-ar="فقط الطلاب يمكنهم رفع الملفات.">Only students can upload files.</p>
            </div>
        
        <?php elseif (!$project): ?>
            <div class="no-project">
                <p data-en="You don't have a project yet." data-ar="ليس لديك مشروع بعد.">You don't have a project yet.</p>
                <a href="Studentdata.php" data-en="Create Project →" data-ar="إنشاء مشروع →">Create Project →</a>
            </div>
        
        <?php else: ?>
            <!-- Student with project: show upload form -->
            <form method="POST" enctype="multipart/form-data" id="uploadForm">
                <h1 class="page-title" data-en="Upload Project Files" data-ar="رفع ملفات المشروع">Upload Project Files</h1>
                
                <!-- Show project name automatically -->
                <div class="project-name">
                    <?php echo htmlspecialchars($project['title']); ?>
                </div>
                
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf); ?>">

                <label class="upload-title" data-en="Upload Poster PDF" data-ar="رفع البوستر PDF">Upload Poster PDF</label>
                <input type="file" name="poster_pdf" accept=".pdf" class="file-upload" required>

                <label class="upload-title" data-en="Upload Presentation PPT" data-ar="رفع العرض التقديمي">Upload Presentation PPT</label>
                <input type="file" name="poster_ppt" accept=".ppt,.pptx" class="file-upload" required>

                <button type="submit" class="upload-btn" id="submitBtn" data-en="Upload Files" data-ar="رفع الملفات">Upload Files</button>
            </form>
        <?php endif; ?>

    </div>

    <!-- PREVIEW SECTION -->
    <div class="preview-section">
        <h2 class="page-title" data-en="Project Previews" data-ar="معاينة المشاريع">Project Previews</h2>

        <?php 
        $hasProjects = false;
        while ($row = $result->fetch_assoc()): 
            $hasProjects = true;
        ?>
            <div class="pdf-preview-card">
                <h3 class="preview-title"><?php echo htmlspecialchars($row['title']); ?></h3>

                <?php if ($role === "judge"): ?>
                    <p><strong data-en="Leader:" data-ar="القائد:">Leader:</strong> <?php echo htmlspecialchars($row['leader_email'] ?? 'N/A'); ?></p>
                    <p><strong data-en="Track:" data-ar="المسار:">Track:</strong> <?php echo htmlspecialchars($row['track']); ?></p>
                <?php endif; ?>

                <!-- PDF Preview -->
                <?php if (!empty($row['poster_pdf'])): ?>
                    <iframe class="preview-iframe" src="<?php echo htmlspecialchars($row['poster_pdf']); ?>"></iframe>
                    <br>
                    <a href="<?php echo htmlspecialchars($row['poster_pdf']); ?>" target="_blank" class="preview-btn" data-en="Open PDF" data-ar="فتح PDF">Open PDF</a>
                <?php else: ?>
                    <p class="missing-file" data-en="No PDF uploaded" data-ar="لم يتم رفع PDF">No PDF uploaded</p>
                <?php endif; ?>

                <!-- PPT Download -->
                <?php if (!empty($row['poster_ppt'])): ?>
                    <a href="<?php echo htmlspecialchars($row['poster_ppt']); ?>" target="_blank" class="preview-btn" data-en="Download PPT" data-ar="تحميل PPT">Download PPT</a>
                <?php else: ?>
                    <p class="missing-file" data-en="No PPT uploaded" data-ar="لم يتم رفع PPT">No PPT uploaded</p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>

        <?php if (!$hasProjects): ?>
            <p class="no-project" data-en="No projects to display." data-ar="لا توجد مشاريع للعرض.">No projects to display.</p>
        <?php endif; ?>

    </div>

</div>

<?php include 'footer.php'; ?>
<script src="posters.js"></script>