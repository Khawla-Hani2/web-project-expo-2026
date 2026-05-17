<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.html");
    exit();
}
if(isset($_COOKIE['user_email'])){
    echo $_COOKIE['user_email'];
}

require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

include 'db.php';

function sendEmailNotification($toEmail, $subject , $messageText) {
    $mail = new PHPMailer(true);

    try {
      $mail ->isSMTP();
      $mail ->Host = 'smtp.gmail.com';
      $mail ->SMTPAuth = true;
      $mail ->Username = 'sabuoshbah@gmail.com';
      $mail ->Password = 'uyhs gbbs tthi fexd';
      $mail ->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail ->Port = 587;

      $mail ->setFrom('sabuoshbah@gmail.com', 'Expo 2026');
      $mail ->addAddress($toEmail);

      $mail ->isHTML(true);
      $mail ->CharSet = 'UTF-8';
      $mail ->Subject = $subject;
      $mail ->Body = $messageText;

      $mail ->send();
      return true;
    }
    catch (Exception $e){
      return false;
    }
} 

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $del_sql = "DELETE FROM judging_sessions WHERE id = $id";;
    if (mysqli_query($conn, $del_sql)) {
        echo "<script>alert('The Meeting was Successfully Deleted!'); window.location.href='admin-judging.php';</script>";
    }
}

if (isset($_POST['save_meeting'])) {
  $project_id = $_POST['project_id'];
$project_query = mysqli_query($conn, " SELECT title, track, theme FROM projects WHERE id = '$project_id'");
$project_data = mysqli_fetch_assoc($project_query);
$project = $project_data['title'];
$path = $project_data['track'];
$department = $project_data['theme'];
    $date     = $_POST['meeting_date'];
    $time     = $_POST['meeting_time'];
    $path     = $_POST['path']; 
    $judge_id = $_POST['judge_id']; 
    $link     = mysqli_real_escape_string($conn, $_POST['meeting_link']);
    $status   = $_POST['status'];
    
if (!empty($_POST['update_id'])) {
    $id = (int) $_POST['update_id'];
    $sql = " UPDATE judging_sessions SET project_name='$project', zoom_link='$link', session_date='$date', session_time='$time', status='$status' WHERE id=$id ";
    $msg = 'The Data was Successfully Updated!';
} else {
    $sql = "INSERT INTO judging_sessions (session_name, project_name, department, track, zoom_link, session_date, session_time, status)
VALUES
('$project','$project','$department','$path','$link','$date','$time','$status')";
    $msg = 'The Meeting was Successfully Added!';}
if (mysqli_query($conn, $sql)) {

if (!empty($_POST['update_id'])) {
mysqli_query($conn, "UPDATE judging_sessions SET project_name='$project', department='$department', track='$path', zoom_link='$link', 
session_date='$date',session_time='$time',status='$status' WHERE id=$id");}
if (empty($_POST['update_id'])) {
$session_id = mysqli_insert_id($conn);
mysqli_query($conn, "INSERT INTO session_judges (session_id, judge_id, is_primary) VALUES ($session_id, $judge_id, 1)");
}  
        $judge_query = mysqli_query($conn, "SELECT firstName, lastName, email FROM users WHERE id = '$judge_id'");
        if ($judge_query && mysqli_num_rows($judge_query) > 0) {
        $judge_data = mysqli_fetch_assoc($judge_query);
        $judge_name = $judge_data['firstName'] . ' ' . $judge_data['lastName'];
        $target_email = $judge_data['email'];}

        $subject = "Official Invitation: Project Evaluation - Expo 2026";
        $email_body = "
<div dir='rtl' style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; background-color: #fcfcfc; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;'>

    <div style='background-color: #7b135d; padding: 25px; text-align: center; border-bottom: 4px solid #8c732a;'>
        <h2 style='color: #ffffff !important; margin: 0; font-size: 20px; letter-spacing: 1px; font-weight: bold;'>EXPO 2026 | إكسبو 2026</h2>
        <p style='color: #ffffff !important; margin: 5px 0 0; font-size: 12px; opacity: 0.9;'>جامعة الإمام عبد الرحمن بن فيصل</p>
    </div>
    
    <div style='padding: 40px 30px; text-align: right;'>
        <h3 style='color: #7b135d; font-size: 22px; margin-top: 0;'>مرحباً. </h3>
        <p style='color: #444; font-size: 16px; line-height: 1.6;'>يسعدنا إبلاغكم بتعيينكم لتقييم مشروع جديد ضمن فعاليات معرض مشاريع التخرج.</p>

        <div style='background-color: #ffffff; border-right: 5px solid #7b135d; border: 1px solid #eee; border-right-width: 5px; padding: 20px; margin: 25px 0; border-radius: 4px;'>
            <p style='margin: 10px 0; color: #333;'><strong style='color: #8c732a;'>اسم المشروع:</strong> $project</p>
            <p style='margin: 10px 0; color: #333;'><strong style='color: #8c732a;'>التاريخ:</strong> $date</p>
            <p style='margin: 10px 0; color: #333;'><strong style='color: #8c732a;'>الوقت:</strong> $time</p>
        </div>

        <div style='text-align: center; margin: 35px 0;'>
            <a href='http://localhost/Expo26/Judging/judging.php' 
               style='background-color: #7b135d; color: #ffffff !important; padding: 15px 35px; text-decoration: none; border-radius: 4px; font-weight: bold; display: inline-block; font-size: 16px;'>
               الانتقال لتقييم المشروع
            </a>
        </div>
    </div>

    <div style='width: 100%; line-height: 0;'>
        <img src='expo/assets/img/footer.png' alt='Pattern' style='width: 100%; height: auto; display: block;'>
    </div>
    
    <div style='background-color: #7b135d; padding: 15px; text-align: center;'>
        <p style='color: #ffffff !important; margin: 0; font-size: 11px;'>© وكالة الكلية للبحث العلمي والابتكار</p>
    </div>
</div>";

            sendEmailNotification($target_email, $subject, $email_body);
        }
         echo "<script>alert('$msg'); window.location.href='admin-judging.php';</script>";
    }

$edit_id = ""; $e_name = ""; $e_date = ""; $e_time = ""; $e_link = ""; $e_status = ""; $e_path = ""; $e_judge = "";
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $res = mysqli_query($conn, "SELECT js.*, sj.judge_id FROM judging_sessions js LEFT JOIN session_judges sj ON js.id = sj.session_id WHERE js.id = $edit_id");
    if (mysqli_num_rows($res) > 0) {
        $row_e = mysqli_fetch_assoc($res);
$e_name = $row_e['project_name']; $e_date = $row_e['session_date']; $e_time = $row_e['session_time']; 
$e_link = $row_e['zoom_link']; $e_status = $row_e['status']; $e_judge = $row_e['judge_id'];}
}

$pageTitle = "Admin Dashboard";
?>
<?php include 'header.php'; ?>

<style>
    @font-face{ font-family:"ExpoFont"; src:url("fonts/TheYearofHandicrafts-Regular.woff2") format("woff2"); }
    
    :root { 
      --main-burgundy: #632949; 
      --accent-gold: #836F24; 
      --bg-light: #F5F5F5; 
      --header-height: 110px; 
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body { 
      font-family: "ExpoFont", sans-serif; 
      background: #ffffff; 
      color: #333; 
      display: flex; 
      flex-direction: column; 
      min-height: 100vh; 
      overflow-x: hidden; 
    }

    main { 
      flex: 1; 
      max-width: 1100px; 
      margin: 40px auto; 
      padding: 0 20px; 
      width: 100%; 
    }
    .card { 
      background: #ffffff; 
      border-radius: 24px; 
      padding: 30px; 
      box-shadow: 0 10px 40px rgba(0,0,0,0.05); 
      margin-bottom: 30px; 
      border: 1px solid #eef0f2; 
    }
    .card h2 { 
      color: var(--main-burgundy); 
      margin-bottom: 20px; 
      border-bottom: 1px solid #f0f0f0; 
      padding-bottom: 10px; 
    }

    /* RTL fixes - CRITICAL: labels must have colon on correct side */
    [dir="rtl"] .card h2,
    [dir="rtl"] label,
    [dir="rtl"] .form-row > div {
        text-align: right;
    }
    [dir="ltr"] .card h2,
    [dir="ltr"] label,
    [dir="ltr"] .form-row > div {
        text-align: left;
    }

    /* Label colon positioning for RTL */
    [dir="rtl"] label {
        direction: rtl;
    }
    [dir="ltr"] label {
        direction: ltr;
    }

    label { 
      display: block; 
      margin-top: 15px; 
      font-weight: 700; 
      color: var(--main-burgundy); 
    }
    input, select { 
      width: 100%; 
      padding: 12px; 
      margin-top: 5px; 
      border-radius: 12px; 
      border: 1px solid #ddd; 
      background: #fcfcfc; 
      font-family: inherit; 
      font-size: 15px;
    }
    [dir="rtl"] input,
    [dir="rtl"] select {
        text-align: right;
        direction: rtl;
    }
    [dir="ltr"] input,
    [dir="ltr"] select {
        text-align: left;
        direction: ltr;
    }
    .form-row {
        display: flex; 
        gap: 20px;
    }
    .form-row > div {
        flex: 1;
    }
    [dir="rtl"] .form-row {
        flex-direction: row-reverse;
    }
    button.primary { 
      background: var(--main-burgundy); 
      color: #fff; 
      padding: 15px; 
      border: none; 
      border-radius: 12px; 
      cursor: pointer; 
      font-weight: 700; 
      width: 100%; 
      margin-top: 20px; 
      font-size: 16px;
    }
    [dir="rtl"] button.primary {
        font-family: "ExpoFont", sans-serif;
    }

    /* Table RTL fixes */
    table { 
      width: 100%; 
      border-collapse: collapse; 
    }
    [dir="rtl"] table {
        text-align: right;
        direction: rtl;
    }
    [dir="ltr"] table {
        text-align: left;
        direction: ltr;
    }
    th { 
      color: var(--main-burgundy); 
      padding: 15px; 
      border-bottom: 2px solid var(--accent-gold); 
      background: #fafafa; 
    }
    td { 
      padding: 15px; 
      border-bottom: 1px solid #f9f9f9; 
      vertical-align: middle; 
    }
    .action-edit { 
      color: var(--accent-gold); 
      font-weight: bold; 
      text-decoration: none; 
      margin-right: 15px; 
    }
    [dir="rtl"] .action-edit {
        margin-right: 0;
        margin-left: 15px;
    }
    .action-delete { 
      color: #ff4f6d; 
      font-weight: bold; 
      text-decoration: none; 
    }

    .main-footer { 
      width: 100%; 
      margin-top: auto; 
      position: relative; 
      line-height: 0; 
    }
    .footer-pattern { 
      width: 100%; 
      height: 100px; 
      background-image: url('footer.png'); 
      background-size: cover; 
      background-position: center; 
      background-repeat: no-repeat; 
    }
    .footer-bottom-bar { 
      background-color: var(--main-burgundy); 
      color: #ffffff; 
      padding: 20px 40px; 
      font-size: 14px; 
      font-weight: 700; 
      line-height: normal; 
    }
    [dir="ltr"] .footer-bottom-bar { 
      text-align: left; 
    }
    [dir="rtl"] .footer-bottom-bar { 
      text-align: right; 
    }
</style>

<<main>
    <section class="card">
      <h2 data-en="<?php echo $edit_id ? 'Edit Meeting' : 'Add New Meeting'; ?>" data-ar="<?php echo $edit_id ? 'تعديل اجتماع' : 'إضافة اجتماع جديد'; ?>">
        <?php echo $edit_id ? 'Edit Meeting' : 'Add New Meeting'; ?>
      </h2>
      <form action="admin-judging.php" method="POST">
        <input type="hidden" name="update_id" value="<?php echo $edit_id; ?>">
        
        <!-- FIXED: Separate label text from colon for proper RTL -->
        <label>
            <span data-en="Project" data-ar="المشروع">Project</span>:
        </label>

        <select name="project_id" required>
        <option value="">Select Project</option>
        <?php
        $projects = mysqli_query($conn, "SELECT id, title FROM projects ORDER BY title ASC ");
        while($p = mysqli_fetch_assoc($projects)){
        $selected = ($e_name == $p['title']) ? "selected" : "";?>
        <option value="<?php echo $p['id']; ?>" <?php echo $selected; ?>>
        <?php echo $p['title']; ?>
        </option>
        <?php } ?>

        </select>
        
        <div class="form-row">
          <div>
            <label>
                <span data-en="Date" data-ar="التاريخ">Date</span>:
            </label>
            <input type="date" name="meeting_date" value="<?php echo $e_date; ?>" required />
          </div>
          <div>
            <label>
                <span data-en="Time" data-ar="الوقت">Time</span>:
            </label>
            <input type="time" name="meeting_time" value="<?php echo $e_time; ?>" required />
          </div>
        </div>
        
        <label>
            <span data-en="Path" data-ar="المسار">Path</span>:
        </label>
        <select name="path" required>
            <option value="health" <?php if($e_path=='health') echo 'selected'; ?> data-en="Health & Wellbeing" data-ar="الصحة وجودة الحياة">Health & Wellbeing</option>
            <option value="future" <?php if($e_path=='future') echo 'selected'; ?> data-en="Future Investments" data-ar="استثمارات المستقبل">Future Investments</option>
            <option value="environment" <?php if($e_path=='environment') echo 'selected'; ?> data-en="Sustainability & Environment" data-ar="الاستدامة والبيئة">Sustainability & Environment</option>
            <option value="energy" <?php if($e_path=='energy') echo 'selected'; ?> data-en="Energy & Industry" data-ar="الطاقة والصناعة">Energy & Industry</option>
            <option value="education" <?php if($e_path=='education') echo 'selected'; ?> data-en="Education & Development" data-ar="التعليم والتطوير">Education & Development</option>
            <option value="research" <?php if($e_path=='research') echo 'selected'; ?> data-en="Research & Innovation" data-ar="الأبحاث والابتكار">Research & Innovation</option>
        </select>

        <label>
            <span data-en="Choose the judge" data-ar="اختر المحكم">Choose the judge</span>:
        </label>
        <select name="judge_id" required>
            <?php
            $judges = mysqli_query($conn, "SELECT id, firstName, lastName FROM users WHERE role='judge'");
           while($j = mysqli_fetch_assoc($judges)) { 
            $full_name = $j['firstName'] . ' ' . $j['lastName'];
            $sel = ($e_judge == $j['id']) ? "selected" : "";
           echo "<option value='".$j['id']."' $sel>$full_name</option>";} ?>
        </select>

        <label>
            <span data-en="Meeting link" data-ar="رابط الاجتماع">Meeting link</span>:
        </label>
        <input type="url" name="meeting_link" value="<?php echo $e_link; ?>" required/>

        <label>
            <span data-en="Status" data-ar="الحالة">Status</span>:
        </label>
        <select name="status">
            <option value="Upcoming" <?php if($e_status=='Upcoming') echo 'selected'; ?> data-en="Upcoming" data-ar="قادم">Upcoming</option>
            <option value="Completed" <?php if($e_status=='Completed') echo 'selected'; ?> data-en="Completed" data-ar="مكتمل">Completed</option>
        </select>

        <button class="primary" type="submit" name="save_meeting" data-en="<?php echo $edit_id ? 'Update Meeting' : 'Add Meeting'; ?>" data-ar="<?php echo $edit_id ? 'تحديث الاجتماع' : 'إضافة الاجتماع'; ?>">
            <?php echo $edit_id ? 'Update Meeting' : 'Add Meeting'; ?>
        </button>
      </form>
    </section>

    <section class="card">
      <h2 data-en="All Meetings" data-ar="جميع الاجتماعات">All Meetings</h2>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th data-en="Project" data-ar="المشروع">Project</th>
              <th data-en="Date" data-ar="التاريخ">Date</th>
              <th data-en="Status" data-ar="الحالة">Status</th>
              <th data-en="Actions" data-ar="الإجراءات">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $result = mysqli_query($conn, "SELECT * FROM judging_sessions ORDER BY id DESC");
            while($row = mysqli_fetch_assoc($result)) {
                $statusEn = htmlspecialchars($row['status']);
                $statusAr = ($row['status'] === 'Upcoming') ? 'قادم' : 'مكتمل';
                echo "<tr><td><b>{$row['project_name']}</b></td><td>{$row['session_date']}</td>
                  <td><span data-en=\"$statusEn\" data-ar=\"$statusAr\">$statusEn</span></td>
                  <td><a href='admin-judging.php?edit_id={$row['id']}' class='action-edit' data-en='Edit' data-ar='تعديل'>Edit</a>
                  <a href='admin-judging.php?delete_id={$row['id']}' class='action-delete' onclick='return confirmDelete()' data-en='Delete' data-ar='حذف'>Delete</a></td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </section>
  
    <section class="card">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #f0f0f0; padding-bottom: 10px;">
        <h2 style="margin: 0; border: none;" data-en="Final Evaluation Results" data-ar="نتائج التقييم النهائية">Final Evaluation Results</h2>
        <button class="action-edit" onclick="exportToExcel()" style="background: var(--bg-light); padding: 8px 15px; border-radius: 10px; cursor: pointer; border: 1px solid #ddd;">
           <i class="fas fa-file-excel"></i> <span data-en="Download Excel" data-ar="تحميل إكسل">Download Excel</span>
        </button>
      </div>

      <div class="table-wrap">
        <table id="resultsTable">
          <thead>
            <tr>
              <th data-en="Project Name" data-ar="اسم المشروع">Project Name</th>
              <th data-en="Judge" data-ar="المحكم">Judge</th>
              <th data-en="Total Score" data-ar="الدرجة الكلية">Total Score</th>
              <th data-en="Feedback" data-ar="الملاحظات">Feedback</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // FIXED: Use p.title instead of js.project_name (column doesn't exist)
            $sql_final = "SELECT p.title AS project_title, u.firstName, u.lastName, e.total_score, e.feedback 
                          FROM evaluations e
                          JOIN judging_sessions js ON e.session_id = js.id 
                          JOIN projects p ON js.project_id = p.id 
                          JOIN users u ON e.judge_id = u.id 
                          ORDER BY e.total_score DESC";
            $res_final = mysqli_query($conn, $sql_final);
            if ($res_final) {
                while($f = mysqli_fetch_assoc($res_final)) {
                    echo "<tr>
                      <td><b>{$f['project_title']}</b></td>
                      <td>{$f['firstName']} {$f['lastName']}</td>
                      <td style='color:var(--accent-gold); font-weight:bold;'>{$f['total_score']}</td>
                      <td style='font-size:13px; color:#666;'>{$f['feedback']}</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No evaluations yet</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>

<?php include 'footer.php'; ?>

<script>
(function() {
    let currentLang = localStorage.getItem("lang") || "ar";

    function applyPageLanguage(lang) {
        document.documentElement.dir = (lang === "ar") ? "rtl" : "ltr";
        document.documentElement.lang = (lang === "ar") ? "ar" : "en";

        // Update spans inside labels (the text before colon)
        document.querySelectorAll("label span[data-en][data-ar]").forEach(function(el) {
            const text = el.getAttribute("data-" + lang);
            if (text !== null) el.textContent = text;
        });

        // Update other data-en/data-ar elements
        document.querySelectorAll("[data-en][data-ar]").forEach(function(el) {
            // Skip label spans already handled above
            if (el.parentElement && el.parentElement.tagName === 'LABEL') return;
            const text = el.getAttribute("data-" + lang);
            if (text !== null) el.textContent = text;
        });

        document.querySelectorAll("option[data-en][data-ar]").forEach(function(opt) {
            const text = opt.getAttribute("data-" + lang);
            if (text !== null) opt.textContent = text;
        });
    }

    function init() { applyPageLanguage(currentLang); }
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init);
    } else {
        init();
    }

    window.addEventListener("expoLangChanged", function(e) {
        const lang = (e.detail && e.detail.lang) ? e.detail.lang : "ar";
        currentLang = lang;
        applyPageLanguage(lang);
    });
})();

function confirmDelete() {
    const lang = localStorage.getItem("lang") || "ar";
    return confirm(lang === "ar" ? "هل أنت متأكد من الحذف؟" : "Are you sure you want to delete?");
}
</script>