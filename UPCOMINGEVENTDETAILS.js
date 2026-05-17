const PASSWORD = "expo2026";

const adminBtn = document.getElementById("adminBtn");
const exitBtn = document.getElementById("exitBtn");
const adminPanel = document.getElementById("adminPanel");
const form = document.getElementById("adminForm");
const menu = document.getElementById("menu");

let lang = "en";

adminBtn.onclick = function () {
  const input = prompt(lang === "ar" ? "أدخل كلمة مرور المسؤول:" : "Enter Admin Password:");

  if (input === PASSWORD) {
    adminPanel.classList.remove("hidden");
    adminBtn.classList.add("hidden");
    exitBtn.classList.remove("hidden");
  } else {
    alert(lang === "ar" ? "كلمة المرور غير صحيحة" : "Wrong Password");
  }
};

exitBtn.onclick = function () {
  adminPanel.classList.add("hidden");
  adminBtn.classList.remove("hidden");
  exitBtn.classList.add("hidden");
};

form.onsubmit = function (e) {
  e.preventDefault();

  document.getElementById("viewTitle").innerText = document.getElementById("titleInput").value;
  document.getElementById("viewName").innerText = document.getElementById("nameInput").value;
  document.getElementById("viewRole").innerText = document.getElementById("roleInput").value;
  document.getElementById("viewEmail").innerText = document.getElementById("emailInput").value;

  document.getElementById("viewDate").innerText = document.getElementById("dateInput").value;
  document.getElementById("viewStart").innerText = document.getElementById("startInput").value;
  document.getElementById("viewEnd").innerText = document.getElementById("endInput").value;
  document.getElementById("viewHall").innerText = document.getElementById("hallInput").value;

  const link = document.getElementById("linkInput").value;
  document.getElementById("viewLink").href = link;

  alert(lang === "ar" ? "تم حفظ التغييرات" : "Changes Saved");
};

function toggleMenu(e) {
  e.stopPropagation();
  menu.classList.toggle("open");
}

function outsideClick(e) {
  if (!menu.contains(e.target) && !e.target.closest(".menu-btn")) {
    menu.classList.remove("open");
  }
}

function toggleLang() {
  lang = lang === "en" ? "ar" : "en";

  document.documentElement.lang = lang;
  document.documentElement.dir = lang === "ar" ? "rtl" : "ltr";

  document.getElementById("expoLogo").src =
    lang === "ar" ? "expo2026_ar_white.png" : "expo2026_en_white.png";

  document.getElementById("t1").textContent = lang === "ar" ? "التسجيل" : "Sign-up";
  document.getElementById("t2").textContent = lang === "ar" ? "الصحة" : "Health";
  document.getElementById("t3").textContent = lang === "ar" ? "الاقتصاد" : "Economies";
  document.getElementById("t4").textContent = lang === "ar" ? "الاستدامة" : "Sustainability";
  document.getElementById("t5").textContent = lang === "ar" ? "الطاقة" : "Energy";
  document.getElementById("t6").textContent = lang === "ar" ? "التعليم" : "Education";
  document.getElementById("t7").textContent = lang === "ar" ? "الأبحاث" : "Research";

  document.getElementById("m1").textContent = lang === "ar" ? "الرئيسية" : "Home";
  document.getElementById("m2").textContent = lang === "ar" ? "عن المعرض" : "About";
  document.getElementById("m3").textContent = lang === "ar" ? "تفاصيل الفعالية القادمة" : "Upcoming Event Details";
  document.getElementById("m4").textContent = lang === "ar" ? "الإنجازات" : "Achievements";
  document.getElementById("m5").textContent = lang === "ar" ? "جدول الفعاليات" : "Event Schedule";
  document.getElementById("m6").textContent = lang === "ar" ? "الرعاة" : "Sponsors";

  document.getElementById("pageTitle").textContent =
    lang === "ar" ? "تفاصيل الفعالية القادمة" : "Upcoming Event Details";

  document.getElementById("pageSubtitle").textContent =
    lang === "ar"
      ? "عرض معلومات الفعالية، بيانات المتحدث، الجدول، القاعة، ورابط الاجتماع."
      : "View event information, speaker details, schedule, hall, and meeting link.";

  document.getElementById("sectionTitle1").textContent =
    lang === "ar" ? "معلومات الفعالية" : "Event Information";

  document.getElementById("sectionTitle2").textContent =
    lang === "ar" ? "تحكم المسؤول" : "Admin Controls";

  document.getElementById("blockTitle1").textContent =
    lang === "ar" ? "عنوان الفعالية" : "Event Title";

  document.getElementById("blockTitle2").textContent =
    lang === "ar" ? "رابط الفعالية" : "Event Link";

  document.getElementById("blockTitle3").textContent =
    lang === "ar" ? "معلومات المتحدث" : "Speaker Information";

  document.getElementById("blockTitle4").textContent =
    lang === "ar" ? "التاريخ والوقت" : "Date & Time";

  document.getElementById("speakerNameLabel").textContent =
    lang === "ar" ? "الاسم:" : "Name:";

  document.getElementById("speakerRoleLabel").textContent =
    lang === "ar" ? "المنصب:" : "Position:";

  document.getElementById("speakerEmailLabel").textContent =
    lang === "ar" ? "البريد الإلكتروني:" : "Email:";

  document.getElementById("dateLabel").textContent =
    lang === "ar" ? "التاريخ:" : "Date:";

  document.getElementById("startLabel").textContent =
    lang === "ar" ? "البداية:" : "Start:";

  document.getElementById("endLabel").textContent =
    lang === "ar" ? "النهاية:" : "End:";

  document.getElementById("hallLabel").textContent =
    lang === "ar" ? "القاعة:" : "Hall:";

  document.getElementById("viewLink").textContent =
    lang === "ar" ? "الانضمام للفعالية" : "Join Online Event";

  document.getElementById("legend1").textContent =
    lang === "ar" ? "تعديل معلومات الفعالية" : "Edit Event Information";

  document.getElementById("legend2").textContent =
    lang === "ar" ? "تعديل التاريخ والوقت" : "Edit Date & Time";

  document.getElementById("legend3").textContent =
    lang === "ar" ? "تحديث رابط الاجتماع" : "Update Meeting Link";

  document.getElementById("label1").textContent =
    lang === "ar" ? "عنوان الفعالية" : "Event Title";

  document.getElementById("label2").textContent =
    lang === "ar" ? "اسم المتحدث" : "Speaker Name";

  document.getElementById("label3").textContent =
    lang === "ar" ? "منصب المتحدث" : "Speaker Position";

  document.getElementById("label4").textContent =
    lang === "ar" ? "بريد المتحدث" : "Speaker Email";

  document.getElementById("label5").textContent =
    lang === "ar" ? "التاريخ" : "Date";

  document.getElementById("label6").textContent =
    lang === "ar" ? "وقت البداية" : "Start Time";

  document.getElementById("label7").textContent =
    lang === "ar" ? "وقت النهاية" : "End Time";

  document.getElementById("label8").textContent =
    lang === "ar" ? "القاعة" : "Hall";

  document.getElementById("label9").textContent =
    lang === "ar" ? "رابط الاجتماع" : "Meeting URL";

  document.getElementById("saveBtn").textContent =
    lang === "ar" ? "حفظ التغييرات" : "Save Changes";

  document.getElementById("resetBtn").textContent =
    lang === "ar" ? "إعادة تعيين" : "Reset";

  document.getElementById("adminBtn").textContent =
    lang === "ar" ? "وضع المسؤول" : "Admin Mode";

  document.getElementById("exitBtn").textContent =
    lang === "ar" ? "الخروج من وضع المسؤول" : "Exit Admin";

  document.getElementById("footerText").textContent =
    lang === "ar"
      ? "وكالة البحث العلمي والابتكار"
      : "Vice Deanship of Scientific Research and Innovation";

      
      function outsideClick(event) {
  const menu = document.getElementById("menu");
  const menuBtn = document.querySelector(".menu-btn");

  if (!menu.contains(event.target) && !menuBtn.contains(event.target)) {
    menu.classList.remove("open");
  }
}
}