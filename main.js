document.addEventListener("click", outsideClick);

const menu = document.getElementById("menu");

/* اللغة الافتراضية */
let lang = localStorage.getItem("lang") || "ar";

/* تطبيق اللغة عند فتح الصفحة */
applyLanguage();

function toggleMenu(e) {
  e.stopPropagation();
  menu.classList.toggle("open");
}

function outsideClick(e) {
  if (menu && !menu.contains(e.target) && !e.target.closest(".menu-btn")) {
    menu.classList.remove("open");
  }
}

function toggleLang() {
  const wasOpen = menu.classList.contains("open");

  menu.style.transition = "none";
  menu.classList.remove("open");

  lang = lang === "en" ? "ar" : "en";

  /* حفظ اللغة */
  localStorage.setItem("lang", lang);

  applyLanguage();

  requestAnimationFrame(() => {
    menu.style.transition = "transform .35s ease";
    if (wasOpen) menu.classList.add("open");
  });
}

function applyLanguage() {
  /* اتجاه الصفحة */
  document.documentElement.dir = lang === "en" ? "ltr" : "rtl";

  /* لغة الصفحة */
  document.documentElement.lang = lang;

  /* اللوقو */
  document.getElementById("expoLogo").src =
    lang === "en" ? "expo2026_en_white.png" : "expo2026_ar_white.png";

  /* Header */
  setText("t1", "Sign-up", "تسجيل الدخول");
  setText("t2", "Health", "صحة الإنسان");
  setText("t3", "Economies", "اقتصاديات المستقبل");
  setText("t4", "Sustainability", "استدامة البيئة");
  setText("t5", "Energy", "الطاقة والصناعة");
  setText("t6", "Education", "التعليم والقدرات");
  setText("t7", "Research", "الأبحاث المنشورة");

  /* Main */
  setText("title", "Welcome to Expo 2026", "مرحبًا بكم في اكسبو ٢٠٢٦");
  setText("sub", "Time left until the expo..", "باقي على المعرض..");

  /* Countdown labels */
  setText("dlabel", "Days", "يوم");
  setText("hlabel", "Hours", "ساعة");
  setText("mlabel", "Minutes", "دقيقة");
  setText("slabel", "Seconds", "ثانية");

  /* Menu */
  setText("m1", "Home", "الرئيسية");
  setText("m2", "About", "عن الموقع");
  setText("m3", "UPCOMINGEVENTDETAILS", "تفاصيل الفالية القادمة");
  setText("m4", "achievements", "الانجازات");
  setText("m5", "EventSchedule", "جدول الفعاليات");
  setText("m6", "sponsor", "الرعاة");

  /* Footer */
  setText(
    "footerText",
    "Vice Deanship of Scientific Research and Innovation",
    "وكالة الكلية للبحث العلمي والابتكار",
  );
}

function setText(id, en, ar) {
  const el = document.getElementById(id);
  if (el) {
    el.textContent = lang === "en" ? en : ar;
  }
}

/* countdown */
const target = new Date("July 22, 2026 08:00:00").getTime();

setInterval(() => {
  const now = new Date().getTime();
  const diff = target - now;

  set("days", Math.floor(diff / (1000 * 60 * 60 * 24)));
  set("hours", Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)));
  set("mins", Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60)));
  set("secs", Math.floor((diff % (1000 * 60)) / 1000));
}, 1000);

function set(id, val) {
  const el = document.getElementById(id);
  if (el) el.textContent = val;
}
