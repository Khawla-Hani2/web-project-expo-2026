<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] != 'judge') {
    header("Location: index.php");
    exit();
}
if(isset($_COOKIE['user_email'])){
    echo $_COOKIE['user_email'];
}

include 'db.php';

$judge_id = (int) $_SESSION['user_id'];
$judge_res = mysqli_query($conn, "SELECT firstName, lastName FROM users WHERE id = $judge_id AND role = 'judge' ");
$judge_row = mysqli_fetch_assoc($judge_res);
$display_name = $judge_row ? $judge_row['firstName'] . ' ' . $judge_row['lastName'] : "Judge";

$res = mysqli_query($conn, " SELECT js.* FROM judging_sessions js JOIN session_judges sj ON js.id = sj.session_id WHERE sj.judge_id = $judge_id ORDER BY js.session_date ASC ");
$next_sql = "SELECT js.* FROM judging_sessions js JOIN session_judges sj ON js.id = sj.session_id WHERE sj.judge_id = $judge_id AND LOWER(js.status) != 'completed' ORDER BY js.session_date ASC, js.session_time ASC LIMIT 1 ";
$next_res = mysqli_query($conn, $next_sql);

if (!$next_res) {
    die(mysqli_error($conn));
}

$next_meeting = mysqli_fetch_assoc($next_res);
?>

<?php include 'header.php'; ?>

<style>
    @font-face { 
        font-family: "ExpoFont"; 
        src: url("fonts/TheYearofHandicrafts-Regular.woff2") format("woff2"); 
    }
    :root { 
        --main-burgundy: #632949; 
        --accent-gold: #836F24; 
        --bg-light: #F5F5F5; 
        --header-height: 110px; 
    }
    
    body { 
        margin: 0; 
        font-family: "ExpoFont", sans-serif; 
        background: var(--bg-light); 
        color: #333; 
        overflow-x: hidden; 
    }

    .main-content{
        margin-top:40px;
        padding:40px 30px;
        max-width:1200px;
        margin-inline:auto;
    }

    [dir="rtl"] .main-content {
        direction: rtl;
    }
    [dir="ltr"] .main-content {
        direction: ltr;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: 2.2fr 0.8fr;
        gap: 25px; 
        align-items: start; 
        margin-bottom: 50px;
    }

    @media (max-width: 768px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

    .card { 
        background: #fff; 
        border-radius: 24px; 
        padding: 30px; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
        margin-bottom: 30px; 
        border: 1px solid #eee; 
        height: fit-content; 
    }

    .next-assignment-card {
        border: 2px solid var(--accent-gold) !important;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .next-assignment-card h2 {
        color: var(--main-burgundy);
        margin: 15px 0;
    }
    
    .meetings-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        margin-bottom: 25px; 
    }
    [dir="rtl"] .meetings-header {
        flex-direction: row-reverse;
    }
    
    .status-filter { 
        padding: 10px; 
        border-radius: 10px; 
        border: 1px solid #ddd; 
        font-family: inherit; 
    }

    .meetings-table { 
        width: 100%; 
        border-collapse: collapse; 
        text-align: center; 
    }
    [dir="rtl"] .meetings-table {
        direction: rtl;
    }

    .meetings-table th, 
    .meetings-table td {
        color: #333 !important; 
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle; 
    }

    .meetings-table td b {
        color: var(--main-burgundy) !important;
    }

    .meetings-table th {
        color: var(--main-burgundy);
        border-bottom: 2px solid var(--accent-gold);
        font-weight: 700;
        font-size: 0.95rem;
    }

    .status, .btn-eval {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto; 
    }

    .meeting-row {
        background-color: #ffffff !important;
    }

    .status { 
        padding: 6px 14px; 
        border-radius: 20px; 
        font-size: 0.8rem; 
        font-weight: 800; 
        text-transform: uppercase; 
    }
    .upcoming { 
        background: rgba(71, 77, 255, 0.1); 
        color: #474dff; 
    }
    .completed { 
        background: rgba(46, 213, 115, 0.1); 
        color: #2ed573; 
    }

    .meeting-row.upcoming, 
    .meeting-row.completed, 
    .meeting-row {
        background-color: #ffffff !important; 
    }

    .meeting-row:hover {
        background-color: #fafafa !important; 
    }

    .btn-eval { 
        background: var(--main-burgundy); 
        color: white; 
        padding: 10px 20px; 
        border-radius: 12px; 
        text-decoration: none; 
        font-weight: bold; 
    }

    .main-footer {
        margin-top: auto; 
        width: 100%;
        z-index: 1000;
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
        padding: 15px 40px;
        font-size: 14px;
        font-weight: 600;
    }
    [dir="rtl"] .footer-bottom-bar {
        text-align: right;
    }
    [dir="ltr"] .footer-bottom-bar {
        text-align: left;
    }

    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        margin: 0;
    }
</style>

<<main class="main-content">
    <div style="text-align: center; margin-bottom: 45px;">
        <h1 id="welcomeTxt" style="color:var(--main-burgundy); font-size: 2.8rem; margin-bottom: 10px;" data-en="Welcome <?php echo $display_name; ?>" data-ar="أهلاً <?php echo $display_name; ?>">Welcome <?php echo $display_name; ?></h1>
        <p id="subTxt" style="color: #666; font-size: 1.2rem;" data-en="Manage your judging meetings and project evaluations." data-ar="قم بإدارة اجتماعات التحكيم وتقييم المشاريع.">Manage your judging meetings and project evaluations.</p>
    </div>

    <div class="dashboard-grid">
        <div class="card">
            <div class="meetings-header">
                <h3 id="myMeetTxt" style="margin:0; font-size: 1.6rem; color: var(--main-burgundy);" data-en="My Meetings" data-ar="اجتماعاتي">My Meetings</h3>
                <select id="statusFilter" class="status-filter" onchange="filterMeetings()">
                    <option value="all" data-en="All" data-ar="الكل">All</option>
                    <option value="upcoming" data-en="Upcoming" data-ar="قادم">Upcoming</option>
                    <option value="completed" data-en="Completed" data-ar="مكتمل">Completed</option>
                    <option value="cancelled" data-en="Cancelled" data-ar="ملغي">Cancelled</option>
                </select>
            </div>
            <table class="meetings-table">
                <thead>
                    <tr>
                        <th data-en="Project" data-ar="المشروع">Project</th>
                        <th data-en="Date" data-ar="التاريخ">Date</th>
                        <th data-en="Status" data-ar="الحالة">Status</th>
                        <th data-en="Action" data-ar="الإجراء">Action</th>
                    </tr>
                </thead>
                <tbody id="meetingsBody">
                    <?php
                    $res = mysqli_query($conn, " SELECT js.* FROM judging_sessions js JOIN session_judges sj ON js.id = sj.session_id WHERE sj.judge_id = $judge_id ORDER BY js.session_date ASC ");
                    while($row = mysqli_fetch_assoc($res)) {
                        $statusClass = strtolower($row['status']);
                        $statusAr = ($row['status'] === 'Upcoming') ? 'قادم' : (($row['status'] === 'Completed') ? 'مكتمل' : 'ملغي');
                        echo "<tr class='meeting-row $statusClass'>
                                <td><b>{$row['project_name']}</b></td>
                                <td>{$row['session_date']}</td>
                                <td><span class='status $statusClass' data-en='{$row['status']}' data-ar='$statusAr'>{$row['status']}</span></td>
                                <td><a href='feedback.php?session_id={$row['id']}' class='btn-eval' data-en='Evaluate' data-ar='تقييم'>Evaluate</a></td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="card next-assignment-card">
            <h4 style="color: var(--accent-gold); margin-top: 0;" data-en="Next Assignment" data-ar="الاجتماع القادمة">Next Meeting</h4>
            <?php if($next_meeting): ?>
                <h2 style="margin: 15px 0; color: var(--main-burgundy);"><?php echo $next_meeting['project_name']; ?></h2>
                <p style="color: #888; font-size: 0.9rem;"><i class="far fa-calendar-alt"></i> <?php echo $next_meeting['session_date']; ?> | <?php echo $next_meeting['session_time']; ?></p>
                <a href="<?php echo $next_meeting['zoom_link']; ?>" target="_blank" style="display:block; background: var(--main-burgundy); color:white; text-align:center; padding:15px; border-radius:12px; text-decoration:none; font-weight:bold; margin-top:25px;" data-en="Join Now" data-ar="انضم الآن">Join Now</a>
            <?php else: ?>
                <p style="color: #ccc; margin-top: 20px;" data-en="No Pending Meetings" data-ar="لا توجد اجتماعات معلقة">No Pending Meetings</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<<footer class="main-footer">
    <div class="footer-pattern"></div>
    <div class="footer-bottom-bar">
        <p>© Vice Deanship of Scientific Research and Innovation</p>
    </div>
</footer>

<?php include 'footer.php'; ?>

<script>
    function filterMeetings() {
        const val = document.getElementById('statusFilter').value;
        document.querySelectorAll('.meeting-row').forEach(row => {
            row.style.display = (val === 'all' || row.classList.contains(val)) ? '' : 'none';
        });
    }

    // Language handling - listen to expoLangChanged from header.php
    (function() {
        let currentLang = localStorage.getItem("lang") || "ar";

        function applyPageLanguage(lang) {
            document.documentElement.dir = (lang === "ar") ? "rtl" : "ltr";
            document.documentElement.lang = (lang === "ar") ? "ar" : "en";

            // Update data-en/data-ar elements
            document.querySelectorAll("[data-en][data-ar]").forEach(function(el) {
                const text = el.getAttribute("data-" + lang);
                if (text !== null) el.textContent = text;
            });

            // Update select options
            document.querySelectorAll("option[data-en][data-ar]").forEach(function(opt) {
                const text = opt.getAttribute("data-" + lang);
                if (text !== null) opt.textContent = text;
            });

            // Update welcome text (has PHP embedded)
            const welcomeTxt = document.getElementById('welcomeTxt');
            if (welcomeTxt) {
                welcomeTxt.textContent = lang === 'ar' 
                    ? "أهلاً <?php echo $display_name; ?>" 
                    : "Welcome <?php echo $display_name; ?>";
            }
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
</script>