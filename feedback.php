<?php
include 'db.php';
$session_id = $_GET['session_id'] ?? $_POST['session_id'] ?? null;

$meetings_query = mysqli_query($conn, "
    SELECT js.id, js.session_name, p.title AS project_title, js.track 
    FROM judging_sessions js 
    LEFT JOIN projects p ON js.project_id = p.id 
    WHERE js.status='Upcoming'
");
$sessions_list = [];
while ($row = mysqli_fetch_assoc($meetings_query)) {
    $sessions_list[] = $row;
}

$project = null;
if ($session_id) {
    $res = mysqli_query($conn, "
        SELECT js.*, p.title AS project_title, p.id AS project_id 
        FROM judging_sessions js 
        LEFT JOIN projects p ON js.project_id = p.id 
        WHERE js.id = $session_id
    ");
    $project = mysqli_fetch_assoc($res);
}

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['final_score_hidden'])) {
    $score = $_POST['final_score_hidden'];
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $j_id = $_SESSION['user_id'];

    $project_id = $project['project_id'] ?? null;
    $project_name = $project['project_title'] ?? '';

    if (!$project_id) {
        echo "<script>alert('Invalid project selected'); window.location.href='judging.php';</script>";
        exit();
    }

    $sql = "
        INSERT INTO evaluations
        (session_id, judge_id, project_id, total_score, feedback)
        VALUES
        ('$session_id', '$j_id', '$project_id', '$score', '$notes')
    ";

    if (mysqli_query($conn, $sql)) {
        mysqli_query($conn, "UPDATE judging_sessions SET status='Completed' WHERE id=$session_id");
        echo "<script>alert('Submitted Successfully!'); window.location.href='judging.php';</script>"; 
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
    exit();
}

$pageTitle = "Project Feedback | EXPO 2026";
?>
<?php include 'header.php'; ?>

<style>
    @font-face{ 
        font-family:"ExpoFont"; 
        src:url("fonts/TheYearofHandicrafts-Regular.woff2") format("woff2"); 
    }
    :root { 
        --main:#632949; 
        --gold: #836F24; 
    }
    * { 
        box-sizing: border-box; 
        margin: 0; 
        padding: 0; 
    }
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
        max-width: 900px; 
        margin: 40px auto 50px; 
        padding: 0 20px; 
        width: 100%; 
    }
    .card { 
        background: #ffffff; 
        border-radius: 24px; 
        padding: 40px; 
        box-shadow: 0 10px 40px rgba(0,0,0,0.05); 
        border: 1px solid #eef0f2; 
    }
    
    /* RTL fixes */
    [dir="rtl"] .card h1,
    [dir="rtl"] .card .sub,
    [dir="rtl"] label,
    [dir="rtl"] .project-display strong,
    [dir="rtl"] .project-display span {
        text-align: right;
    }
    
    [dir="ltr"] .card h1,
    [dir="ltr"] .card .sub,
    [dir="ltr"] label,
    [dir="ltr"] .project-display strong,
    [dir="ltr"] .project-display span {
        text-align: left;
    }
    
    .card h1 {
        color: var(--main);
        font-size: 28px;
        margin-bottom: 8px;
    }
    .card .sub { 
        color: #666; 
        margin-bottom: 30px; 
        font-size: 15px;
    }
    
    .project-display { 
        background: #F9F9F9; 
        border-radius: 16px; 
        padding: 20px; 
        margin-bottom: 30px; 
    }
    [dir="rtl"] .project-display {
        border-right: 5px solid var(--gold);
        border-left: none;
    }
    [dir="ltr"] .project-display {
        border-left: 5px solid var(--gold);
        border-right: none;
    }
    .project-display strong { 
        color: var(--gold); 
        font-size: 12px; 
        text-transform: uppercase; 
        display: block;
    }
    .project-display span { 
        display: block; 
        font-size: 22px; 
        font-weight: bold; 
        margin-top: 5px; 
        color: var(--main); 
    }
    label { 
        display: block; 
        margin-top: 20px; 
        font-weight: 700; 
        color: var(--main); 
    }
    select, input, textarea { 
        width: 100%; 
        padding: 14px; 
        margin-top: 8px; 
        border-radius: 12px; 
        border: 1px solid #ddd; 
        background: #fcfcfc; 
        font-family: inherit; 
        font-size: 15px;
    }
    [dir="rtl"] select,
    [dir="rtl"] input,
    [dir="rtl"] textarea {
        text-align: right;
    }
    [dir="ltr"] select,
    [dir="ltr"] input,
    [dir="ltr"] textarea {
        text-align: left;
    }
    .final-score-box { 
        background: var(--main); 
        border-radius: 16px; 
        padding: 25px 35px; 
        margin: 30px 0; 
        display: flex; 
        justify-content: space-between; 
        align-items: center;
        box-shadow: 0 10px 30px rgba(99, 41, 73, 0.2);
    }
    [dir="rtl"] .final-score-box {
        flex-direction: row-reverse;
    }
    .final-score-box span#finalScoreDisplay { 
        font-size: 42px; 
        font-weight: 800; 
        color: #ffffff !important; 
        text-shadow: 0 2px 10px rgba(0,0,0,0.2); 
    }
    .final-score-box div, .final-score-box .final-outof {
        color: rgba(255, 255, 255, 0.85) !important; 
        font-weight: 600;
    }
    .final-score-box small {
        color: rgba(255, 255, 255, 0.6);
        display: block;
        margin-top: 4px;
    }
    [dir="rtl"] .final-score-box div:first-child {
        text-align: right;
    }
    [dir="ltr"] .final-score-box div:first-child {
        text-align: left;
    }
    .fixed-mode { 
        pointer-events: none; 
        opacity: 0.7; 
    }
    .rubric-card { 
        background: #fdfdfd; 
        border: 1px solid #eee; 
        border-radius: 16px; 
        padding: 25px; 
        margin-top: 25px; 
    }
    .rubric-row { 
        display: grid; 
        grid-template-columns: 1fr 120px; 
        gap: 20px; 
        align-items: center; 
        padding: 15px 0; 
        border-bottom: 1px solid #f0f0f0; 
    }
    [dir="rtl"] .rubric-row {
        direction: rtl;
    }
    [dir="rtl"] .rubric-row input {
        text-align: center;
    }
    .btn-submit { 
        background: var(--main); 
        color: #fff; 
        padding: 18px; 
        border: none; 
        border-radius: 14px; 
        cursor: pointer; 
        font-weight: 700; 
        width: 100%; 
        margin-top: 20px; 
        font-size: 18px; 
    }
    [dir="rtl"] .btn-submit {
        font-family: "ExpoFont", sans-serif;
    }
</style>

<<main>
    <section class="card" id="formCard">
        <h1 data-en="Give Feedback" data-ar="تقديم التقييم">Give Feedback</h1>
        <p class="sub" data-en="Pick a path first, then select the project to evaluate." data-ar="اختر المسار أولاً، ثم حدد المشروع للتقييم.">Pick a path first, then select the project to evaluate.</p>

        <div class="project-display">
            <strong data-en="Project Name" data-ar="اسم المشروع">Project Name</strong>
            <span id="projectNameDisplay"><?php echo htmlspecialchars($project['project_title'] ?? '—'); ?></span>
        </div>

        <form id="FeedbackForm" method="POST" action="feedback.php" onsubmit="handleFinalSubmit(event)">
            <input type="hidden" id="final_score_hidden" name="final_score_hidden">
            <input type="hidden" name="session_id" id="meeting_id_hidden" value="<?php echo htmlspecialchars($session_id ?? ''); ?>">
            
            <label id="lblPath" data-en="Path" data-ar="المسار">Path</label>
            <select id="pathSelect" name="path" required>
                <option value="" disabled selected id="pathPlaceholder" data-en="Select a path" data-ar="اختر المسار">Select a path</option>
                <option value="health" data-en="Health & Wellbeing" data-ar="الصحة وجودة الحياة">Health & Wellbeing</option>
                <option value="future" data-en="Future Economies" data-ar="اقتصاد المستقبل">Future Economies</option>
                <option value="environment" data-en="Environmental Sustainability" data-ar="الاستدامة والبيئة">Environmental Sustainability</option>
                <option value="energy" data-en="Energy & Industry" data-ar="الطاقة والصناعة">Energy & Industry</option>
                <option value="education" data-en="Education & Development" data-ar="التعليم والتطوير">Education & Development</option>
                <option value="research" data-en="Research & Innovation" data-ar="الأبحاث والابتكار">Research & Innovation</option>
            </select>

            <label data-en="Project" data-ar="المشروع">Project</label>
            <select id="projectSelect" name="project" required disabled>
                <option value="" disabled selected data-en="Choose a path first" data-ar="اختر المسار أولاً">Choose a path first</option>
            </select>

            <div id="rubricWrap"></div>

            <div class="final-score-box">
                <div>
                    <div data-en="Final Score" data-ar="الدرجة النهائية">Final Score</div>
                    <small data-en="Calculated based on criteria" data-ar="تحسب بناءً على المعايير">Calculated based on criteria</small>
                </div>
                <div><span id="finalScoreDisplay">—</span> <span class="final-outof">/ 10</span></div>
            </div>

            <label data-en="Notes" data-ar="ملاحظات">Notes</label>
            <textarea id="notes" name="notes" placeholder="Write your feedback here..." required data-en-placeholder="Write your feedback here..." data-ar-placeholder="اكتب ملاحظاتك هنا..."></textarea>

            <button type="submit" class="btn-submit" id="btnSubmit" data-en="Submit Evaluation" data-ar="إرسال التقييم">Submit Evaluation</button>
        </form>
    </section>
</main>

<?php include 'footer.php'; ?>

<script>
    const dbSessions = <?php echo json_encode($sessions_list); ?>;

    const RUBRICS = {
        health: [
            {label_en:"Health Relevance", label_ar:"الارتباط بالصحة", weight:30},
            {label_en:"Safety & Ethics", label_ar:"السلامة والأخلاقيات", weight:20},
            {label_en:"Feasibility", label_ar:"قابليّة التطبيق", weight:25},
            {label_en:"Presentation", label_ar:"طريقة العرض", weight:25}
        ],
        future: [
            {label_en:"Market Potential", label_ar:"إمكانات السوق", weight:30},
            {label_en:"Innovation", label_ar:"الابتكار", weight:20},
            {label_en:"Viability", label_ar:"الجدوى الاقتصادية", weight:25},
            {label_en:"Presentation", label_ar:"طريقة العرض", weight:25}
        ],
        environment: [
            {label_en:"Impact", label_ar:"الأثر البيئي", weight:30},
            {label_en:"Innovation", label_ar:"الابتكار", weight:20},
            {label_en:"Feasibility", label_ar:"قابليّة التطبيق", weight:25},
            {label_en:"Presentation", label_ar:"طريقة العرض", weight:25}
        ],
        energy: [
            {label_en:"Technical Quality", label_ar:"الجودة التقنية", weight:30},
            {label_en:"Innovation", label_ar:"الابتكار", weight:20},
            {label_en:"Security", label_ar:"الأمن السيبراني", weight:25},
            {label_en:"Presentation", label_ar:"طريقة العرض", weight:25}
        ],
        education: [
            {label_en:"Impact", label_ar:"الأثر التعليمي", weight:30},
            {label_en:"Innovation", label_ar:"الابتكار", weight:20},
            {label_en:"Feasibility", label_ar:"قابليّة التطبيق", weight:25},
            {label_en:"Presentation", label_ar:"طريقة العرض", weight:25}
        ],
        research: [
            {label_en:"Methodology", label_ar:"المنهجية العلمية", weight:30},
            {label_en:"Novelty", label_ar:"الحداثة والأصالة", weight:20},
            {label_en:"Evidence", label_ar:"الأدلة والنتائج", weight:25},
            {label_en:"Clarity", label_ar:"وضوح العرض", weight:25}
        ]
    };

    let currentLang = localStorage.getItem("lang") || "ar";

    function applyPageLanguage(lang) {
        currentLang = lang;
        document.documentElement.dir = (lang === "ar") ? "rtl" : "ltr";
        document.documentElement.lang = (lang === "ar") ? "ar" : "en";

        // Update all data-en/data-ar elements
        document.querySelectorAll("[data-en][data-ar]").forEach(function(el) {
            const text = el.getAttribute("data-" + lang);
            if (text !== null) el.textContent = text;
        });

        // Update placeholders
        document.querySelectorAll("[data-en-placeholder][data-ar-placeholder]").forEach(function(el) {
            const ph = el.getAttribute("data-" + lang + "-placeholder");
            if (ph !== null) el.placeholder = ph;
        });

        // Update select options
        document.querySelectorAll("option[data-en][data-ar]").forEach(function(opt) {
            const text = opt.getAttribute("data-" + lang);
            if (text !== null) opt.textContent = text;
        });

        // Re-render rubric if path selected
        const pathSelect = document.getElementById("pathSelect");
        if (pathSelect && pathSelect.value) {
            renderRubric(pathSelect.value);
        }
    }

    // Listen to expoLangChanged from header.php
    window.addEventListener("expoLangChanged", function(e) {
        const lang = (e.detail && e.detail.lang) ? e.detail.lang : "ar";
        applyPageLanguage(lang);
    });

    // Also listen to storage for cross-tab sync
    window.addEventListener("storage", function(e) {
        if (e.key === "lang" && e.newValue !== currentLang) {
            applyPageLanguage(e.newValue);
        }
    });

    // Initial apply
    applyPageLanguage(currentLang);

    const pathSelect = document.getElementById("pathSelect");
    const projectSelect = document.getElementById("projectSelect");
    const projectNameDisplay = document.getElementById("projectNameDisplay");
    const rubricWrap = document.getElementById("rubricWrap");
    const finalScoreDisplay = document.getElementById("finalScoreDisplay");

    pathSelect.addEventListener("change", () => {
        const track = pathSelect.value;
        projectSelect.disabled = false;
        const defaultText = currentLang === 'ar' ? 'اختر المشروع' : 'Select Project';
        projectSelect.innerHTML = `<option value="" disabled selected>${defaultText}</option>`;
        
        const filtered = dbSessions.filter(m => m.track === track);
        filtered.forEach(m => {
            const opt = document.createElement("option");
            opt.value = m.id;
            opt.textContent = m.project_title || m.session_name;
            projectSelect.appendChild(opt);
        });
        renderRubric(track);
    });

    projectSelect.addEventListener("change", () => {
        projectNameDisplay.textContent = projectSelect.options[projectSelect.selectedIndex].text;
        document.getElementById("meeting_id_hidden").value = projectSelect.value;
    });

    function renderRubric(track) {
        rubricWrap.innerHTML = "";
        if (!RUBRICS[track]) return;
        
        const isRtl = document.documentElement.dir === 'rtl';
        const card = document.createElement("div");
        card.className = "rubric-card";
        
        RUBRICS[track].forEach(item => {
            const label = isRtl ? item.label_ar : item.label_en;
            const row = document.createElement("div");
            row.className = "rubric-row";
            row.innerHTML = `<div><b>${label}</b> (${item.weight}%)</div>
                <input class="rubric-score" type="number" min="0" max="10" step="0.5" data-weight="${item.weight}" required oninput="calculateScore()">`;
            card.appendChild(row);
        });
        rubricWrap.appendChild(card);
    }

    function calculateScore() {
        let total = 0;
        document.querySelectorAll(".rubric-score").forEach(inp => {
            total += (parseFloat(inp.value) || 0) * (parseFloat(inp.dataset.weight) / 100);
        });
        finalScoreDisplay.textContent = total.toFixed(2);
        document.getElementById("final_score_hidden").value = total.toFixed(2);
    }

    function handleFinalSubmit(e) {
        const btn = document.getElementById("btnSubmit");
        document.getElementById("formCard").classList.add("fixed-mode");
        btn.disabled = true;
        btn.textContent = currentLang === 'ar' ? "جاري الإرسال..." : "Submitting...";
    }
</script>