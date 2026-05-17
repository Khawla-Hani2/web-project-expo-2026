const menu = document.getElementById("menu");
let lang = "en";

function toggleMenu(e){
  e.stopPropagation();
  menu.classList.toggle("open");
}

function outsideClick(e){
  if(!menu.contains(e.target) && !e.target.closest(".menu-btn")){
    menu.classList.remove("open");
  }
}

function applyFilters(){
  const track = document.getElementById("trackSelect").value.toLowerCase();
  const sort = document.getElementById("sortSelect").value;
  const search = document.getElementById("searchInput").value.toLowerCase();

  filterTable("rankingBody", track, search, sort);
  filterTable("winnersBody", track, search, sort);

  updateCounts();
}

function filterTable(tbodyId, track, search, sort){
  const tbody = document.getElementById(tbodyId);
  const rows = Array.from(tbody.querySelectorAll("tr"));

  rows.forEach(row => {
    const rowTrack = row.dataset.track.toLowerCase();
    const rowText = row.innerText.toLowerCase();

    const trackMatch = track === "all" || rowTrack === track;
    const searchMatch = rowText.includes(search);

    row.style.display = trackMatch && searchMatch ? "" : "none";
  });

  const visibleRows = rows.filter(row => row.style.display !== "none");

  visibleRows.sort((a, b) => {
    if(sort === "rankAsc"){
      return Number(a.dataset.rank) - Number(b.dataset.rank);
    }

    if(sort === "scoreDesc"){
      return Number(b.dataset.score) - Number(a.dataset.score);
    }

    if(sort === "nameAsc"){
      return a.dataset.project.localeCompare(b.dataset.project);
    }

    return 0;
  });

  visibleRows.forEach(row => tbody.appendChild(row));
}

function resetFilters(){
  document.getElementById("trackSelect").value = "all";
  document.getElementById("sortSelect").value = "rankAsc";
  document.getElementById("searchInput").value = "";
  applyFilters();
}

function countVisibleRows(tbodyId){
  const rows = document.querySelectorAll("#" + tbodyId + " tr");
  let count = 0;

  rows.forEach(row => {
    if(row.style.display !== "none"){
      count++;
    }
  });

  return count;
}

function updateCounts(){
  document.getElementById("rankingCount").textContent = countVisibleRows("rankingBody") + " Projects";
  document.getElementById("winnersCount").textContent = countVisibleRows("winnersBody") + " Winners";
}

function printResults(){
  window.print();
}

function toggleLang(){
  lang = lang === "en" ? "ar" : "en";

  document.documentElement.dir = lang === "ar" ? "rtl" : "ltr";
  document.documentElement.lang = lang;

  document.getElementById("expoLogo").src =
    lang === "ar" ? "expo2026_ar_white.png" : "expo2026_en_white.png";

  document.getElementById("t1").textContent = lang === "ar" ? "تسجيل الدخول" : "Sign-up";
  document.getElementById("t2").textContent = lang === "ar" ? "صحة الإنسان" : "Health";
  document.getElementById("t3").textContent = lang === "ar" ? "اقتصاديات المستقبل" : "Economies";
  document.getElementById("t4").textContent = lang === "ar" ? "الاستدامة" : "Sustainability";
  document.getElementById("t5").textContent = lang === "ar" ? "الطاقة" : "Energy";
  document.getElementById("t6").textContent = lang === "ar" ? "التعليم" : "Education";
  document.getElementById("t7").textContent = lang === "ar" ? "الأبحاث" : "Research";

  document.getElementById("m1").textContent = lang === "ar" ? "الرئيسية" : "Home";
  document.getElementById("m2").textContent = lang === "ar" ? "الإعلانات" : "Announcements";
  document.getElementById("m3").textContent = lang === "ar" ? "جدول الفعاليات" : "Event Schedule";
  document.getElementById("m4").textContent = lang === "ar" ? "الرعاة" : "Sponsors";

  document.getElementById("pageTitle").textContent =
    lang === "ar" ? "النتائج النهائية" : "Final Results";

  document.getElementById("pageSubtitle").textContent =
    lang === "ar"
      ? "اعرض الفائزين وترتيب المشاريع حسب المسار، وابحث عن شهادتك باستخدام رقم المستخدم والبريد الإلكتروني."
      : "View winners and rankings by track, and find your certificate using your user ID and email.";

  document.getElementById("certificateTitle").textContent =
    lang === "ar" ? "تحميل الشهادة" : "Certificate Download";

  document.getElementById("filterTitle").textContent =
    lang === "ar" ? "التصفية والبحث" : "Filters";

  document.getElementById("applyBtn").textContent =
    lang === "ar" ? "تطبيق" : "Apply";

  document.getElementById("resetBtn").textContent =
    lang === "ar" ? "إعادة تعيين" : "Reset";

  document.getElementById("printBtn").textContent =
    lang === "ar" ? "طباعة / PDF" : "Print / PDF";

  document.getElementById("footerText").textContent =
    lang === "ar"
      ? "وكالة البحث العلمي والابتكار"
      : "Vice Deanship of Scientific Research and Innovation";
}

document.addEventListener("DOMContentLoaded", function(){
  document.getElementById("applyBtn").addEventListener("click", applyFilters);
  document.getElementById("resetBtn").addEventListener("click", resetFilters);
  document.getElementById("printBtn").addEventListener("click", printResults);

  document.getElementById("searchInput").addEventListener("keyup", applyFilters);
  document.getElementById("trackSelect").addEventListener("change", applyFilters);
  document.getElementById("sortSelect").addEventListener("change", applyFilters);

  applyFilters();
  fetch("get_user_certificate.php")

.then(res => res.json())

.then(data => {

    if(data && data.certificate_file){

        const img = document.getElementById(
          "certificateTemplate"
        );

        img.src = data.certificate_file;

        img.style.display = "block";

    }

});
});