(function () {
  "use strict";

  let currentLang = localStorage.getItem("lang") || "ar";

  function applyPageLanguage(lang) {
    document.documentElement.dir = lang === "ar" ? "rtl" : "ltr";
    document.documentElement.lang = lang;

    document.querySelectorAll("[data-en][data-ar]").forEach(function (el) {
      const text = el.getAttribute("data-" + lang);
      if (text !== null) el.textContent = text;
    });

    document
      .querySelectorAll("[data-en-placeholder][data-ar-placeholder]")
      .forEach(function (el) {
        const ph = el.getAttribute("data-" + lang + "-placeholder");
        if (ph !== null) el.placeholder = ph;
      });

    document
      .querySelectorAll("option[data-en][data-ar]")
      .forEach(function (opt) {
        const text = opt.getAttribute("data-" + lang);
        if (text !== null) opt.textContent = text;
      });

    const logo = document.getElementById("expoLogo");
    if (logo) {
      logo.src =
        lang === "ar" ? "expo2026_ar_white.png" : "expo2026_en_white.png";
    }

    localStorage.setItem("lang", lang);
    currentLang = lang;
  }

  window.toggleLang = function () {
    applyPageLanguage(currentLang === "en" ? "ar" : "en");
  };

  window.toggleMenu = function (e) {
    if (e) e.stopPropagation();
    console.log("Menu toggle — no side menu on signup page");
  };

  function init() {
    applyPageLanguage(currentLang);
  }
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  // Signup form handler
  const signupForm = document.getElementById("signupForm");
  if (signupForm) {
    signupForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const phone = document.getElementById("phone").value.trim();
      const role = document.getElementById("role").value;

      if (!role) {
        alert(
          currentLang === "ar" ? "يرجى اختيار الدور" : "Please select a role",
        );
        return;
      }

      if (!/^05\d{8}$/.test(phone)) {
        alert(
          currentLang === "ar"
            ? "رقم الجوال يجب أن يبدأ بـ 05 ويتبعه 8 أرقام"
            : "Phone must start with 05 followed by 8 digits",
        );
        return;
      }

      const data = {
        firstName: document.getElementById("firstName").value.trim(),
        lastName: document.getElementById("lastName").value.trim(),
        email: document.getElementById("email").value.trim(),
        password: document.getElementById("password").value,
        role: role,
        phone_number: phone,
      };

      fetch("signup.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
        credentials: "include",
      })
        .then(function (response) {
          return response.json();
        })
        .then(function (data) {
          if (data.status === "success") {
            alert(
              currentLang === "ar"
                ? "تم التسجيل بنجاح! يرجى التحقق من بريدك الإلكتروني لتفعيل حسابك."
                : "Registered successfully! Please check your email to activate your account.",
            );
            window.location.href = "waiting.html";
          } else if (
            data.message &&
            data.message.toLowerCase().includes("email")
          ) {
            alert(
              currentLang === "ar"
                ? "هذا البريد الإلكتروني مسجل مسبقاً"
                : "This email already exists!",
            );
          } else {
            alert(
              data.message ||
                (currentLang === "ar" ? "حدث خطأ" : "An error occurred"),
            );
          }
        })
        .catch(function (error) {
          console.error("Error:", error);
          alert(
            currentLang === "ar"
              ? "خطأ في الاتصال"
              : "Connection error. Please try again.",
          );
        });
    });
  }
})();
