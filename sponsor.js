let currentLang = "en";

const sponsors = [
  {
    nameEn: "Nouf Alsudairi Group",
    nameAr: "مجموعة نوف السديري",
    url: "https://alsudairinouf.com",
    image: "nouf.jpeg"
  }

  /*
  To add another sponsor later:

  ,
  {
    nameEn: "Sponsor English Name",
    nameAr: "اسم الراعي بالعربي",
    url: "https://example.com",
    image: "sponsor-image.png"
  }
  */
];

const text = {
  en: {
    title: "EXPO IAU 2026 – Sponsors Page",
    subtitle: "Meet the organizations supporting EXPO IAU 2026.",
    visit: "Visit Sponsor Website",
    footer: "Vice Deanship of Scientific Research and Innovation",

    t1: "Sign-up",
    t2: "Health",
    t3: "Economies",
    t4: "Sustainability",
    t5: "Energy",
    t6: "Education",
    t7: "Research",

    m1: "Home",
    m2: "About",
    m3: "Upcoming Event Details",
    m4: "Achievements",
    m5: "Event Schedule",
    m6: "Sponsors"
  },

  ar: {
    title: "معرض جامعة الإمام عبدالرحمن بن فيصل 2026 – صفحة الرعاة",
    subtitle: "تعرف على الجهات الداعمة لمعرض جامعة الإمام عبدالرحمن بن فيصل 2026.",
    visit: "زيارة موقع الراعي",
    footer: "وكالة البحث العلمي والابتكار",

    t1: "التسجيل",
    t2: "الصحة",
    t3: "الاقتصاد",
    t4: "الاستدامة",
    t5: "الطاقة",
    t6: "التعليم",
    t7: "الأبحاث",

    m1: "الرئيسية",
    m2: "عن المعرض",
    m3: "تفاصيل الفعالية القادمة",
    m4: "الإنجازات",
    m5: "جدول الفعاليات",
    m6: "الرعاة"
  }
};

const sponsorsContainer = document.getElementById("sponsorsContainer");

function renderSponsors() {
  sponsorsContainer.innerHTML = "";

  sponsors.forEach(function (sponsor) {
    const sponsorName = currentLang === "en" ? sponsor.nameEn : sponsor.nameAr;

    const sponsorCard = `
      <div class="sponsor-card">
        <img src="${sponsor.image}" alt="${sponsorName}" class="sponsor-logo">

        <h2>${sponsorName}</h2>

        <a class="sponsor-link" href="${sponsor.url}" target="_blank">
          ${text[currentLang].visit}
        </a>
      </div>
    `;

    sponsorsContainer.insertAdjacentHTML("beforeend", sponsorCard);
  });
}

function updateText() {
  document.documentElement.lang = currentLang;
  document.documentElement.dir = currentLang === "ar" ? "rtl" : "ltr";

  document.getElementById("expoLogo").src =
    currentLang === "ar" ? "expo2026_ar_white.png" : "expo2026_en_white.png";

  document.getElementById("pageTitle").textContent = text[currentLang].title;
  document.getElementById("pageSubtitle").textContent = text[currentLang].subtitle;
  document.getElementById("footerText").textContent = text[currentLang].footer;

  document.getElementById("t1").textContent = text[currentLang].t1;
  document.getElementById("t2").textContent = text[currentLang].t2;
  document.getElementById("t3").textContent = text[currentLang].t3;
  document.getElementById("t4").textContent = text[currentLang].t4;
  document.getElementById("t5").textContent = text[currentLang].t5;
  document.getElementById("t6").textContent = text[currentLang].t6;
  document.getElementById("t7").textContent = text[currentLang].t7;

  document.getElementById("m1").textContent = text[currentLang].m1;
  document.getElementById("m2").textContent = text[currentLang].m2;
  document.getElementById("m3").textContent = text[currentLang].m3;
  document.getElementById("m4").textContent = text[currentLang].m4;
  document.getElementById("m5").textContent = text[currentLang].m5;
  document.getElementById("m6").textContent = text[currentLang].m6;

  renderSponsors();
}

function toggleLang() {
  currentLang = currentLang === "en" ? "ar" : "en";
  updateText();
}

function toggleMenu(event) {
  event.stopPropagation();
  document.getElementById("menu").classList.toggle("open");
}

function outsideClick(event) {
  const menu = document.getElementById("menu");
  const menuBtn = document.querySelector(".menu-btn");

  if (!menu.contains(event.target) && !menuBtn.contains(event.target)) {
    menu.classList.remove("open");
  }
}

updateText();