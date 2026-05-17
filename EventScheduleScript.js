const SCHEDULE_I18N = {
  en: {
    pageTitle: "Event Schedule",
    scheduleKicker: "EXPO 2026",
    heroTitle: "Event Schedule",
    scheduleHeroText: "Explore the main events and important dates for EXPO 2026.",

    e1Date: "May 10, 2026 — 10:00 AM",
    e1Badge: "Opening",
    e1Title: "Opening Ceremony",
    e1Desc: "The official opening of EXPO 2026 with keynote speakers and special guests.",

    e2Date: "May 12, 2026 — 2:00 PM",
    e2Badge: "Showcase",
    e2Title: "Innovation Expo & Project Showcase",
    e2Desc: "Participants present their projects and ideas across the official EXPO tracks.",

    e3Date: "May 15, 2026 — 11:00 AM",
    e3Badge: "Judging",
    e3Title: "Judging & Evaluation Day",
    e3Desc: "Projects are reviewed and evaluated by the judging panel using consistent criteria.",

    e4Date: "May 20, 2026 — 5:00 PM",
    e4Badge: "Closing",
    e4Title: "Awards & Closing Ceremony",
    e4Desc: "Winners are announced and EXPO 2026 officially concludes."
  },

  ar: {
    pageTitle: "جدول الفعاليات",
    scheduleKicker: "إكسبو 2026",
    heroTitle: "جدول الفعاليات",
    scheduleHeroText: "تعرّف على أهم الفعاليات والمواعيد الخاصة بإكسبو 2026.",

    e1Date: "10 مايو 2026 — 10:00 ص",
    e1Badge: "الافتتاح",
    e1Title: "حفل الافتتاح",
    e1Desc: "الافتتاح الرسمي لإكسبو 2026 بحضور متحدثين رئيسيين وضيوف مميزين.",

    e2Date: "12 مايو 2026 — 2:00 م",
    e2Badge: "عرض المشاريع",
    e2Title: "معرض الابتكار وعرض المشاريع",
    e2Desc: "يقدم المشاركون مشاريعهم وأفكارهم ضمن المسارات الرسمية لإكسبو.",

    e3Date: "15 مايو 2026 — 11:00 ص",
    e3Badge: "التحكيم",
    e3Title: "يوم التحكيم والتقييم",
    e3Desc: "تُراجع المشاريع وتُقيَّم من قبل لجنة التحكيم وفق معايير موحدة.",

    e4Date: "20 مايو 2026 — 5:00 م",
    e4Badge: "الختام",
    e4Title: "حفل الجوائز والختام",
    e4Desc: "يتم إعلان الفائزين واختتام إكسبو 2026 رسميًا."
  }
};

function setScheduleText(id, value) {
  const el = document.getElementById(id);
  if (el && value !== undefined) el.textContent = value;
}

function applyScheduleLanguage(lang) {
  const safeLang = lang === "ar" ? "ar" : "en";
  const t = SCHEDULE_I18N[safeLang];

  document.documentElement.lang = safeLang;
  document.documentElement.dir = safeLang === "ar" ? "rtl" : "ltr";
  document.title = `Expo 2026 | ${t.pageTitle}`;

  Object.keys(t).forEach((key) => {
    if (key !== "pageTitle") setScheduleText(key, t[key]);
  });
}



function getPreferredExpoLanguage() {
  const urlLang = new URLSearchParams(window.location.search).get("lang");
  const savedLang = localStorage.getItem("lang") || localStorage.getItem("language") || localStorage.getItem("siteLang");
  const globalLang = typeof window.lang !== "undefined" ? window.lang : "";
  const htmlLang = document.documentElement.getAttribute("lang") || "";
  const selected = urlLang || savedLang || globalLang || htmlLang || "en";
  return selected === "ar" ? "ar" : "en";
}

function setupExpoLanguageSync(applyLanguageFunction) {
  let currentAppliedLanguage = "";

  const applyCurrentLanguage = (nextLang) => {
    const safeLang = nextLang === "ar" ? "ar" : "en";
    if (currentAppliedLanguage === safeLang && document.documentElement.lang === safeLang) return;
    currentAppliedLanguage = safeLang;
    applyLanguageFunction(safeLang);
  };

  applyCurrentLanguage(getPreferredExpoLanguage());

  window.addEventListener("storage", (event) => {
    if (["lang", "language", "siteLang"].includes(event.key)) {
      applyCurrentLanguage(getPreferredExpoLanguage());
    }
  });

  document.addEventListener("languageChanged", (event) => {
    applyCurrentLanguage(event.detail?.lang || getPreferredExpoLanguage());
  });

  window.addEventListener("expoLanguageChanged", (event) => {
    applyCurrentLanguage(event.detail?.lang || getPreferredExpoLanguage());
  });

  document.addEventListener("click", () => {
    setTimeout(() => applyCurrentLanguage(getPreferredExpoLanguage()), 0);
  });

  const htmlLanguageObserver = new MutationObserver(() => {
    const htmlLang = document.documentElement.getAttribute("lang");
    if (htmlLang === "ar" || htmlLang === "en") applyCurrentLanguage(htmlLang);
  });

  htmlLanguageObserver.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ["lang"]
  });
}


window.applyScheduleLanguage = applyScheduleLanguage;

document.addEventListener("DOMContentLoaded", () => {
  setupExpoLanguageSync(applyScheduleLanguage);
});
