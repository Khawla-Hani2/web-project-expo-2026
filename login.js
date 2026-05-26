(function () {
  "use strict";

  let currentLang = localStorage.getItem("lang") || "ar";

  // ── APPLY LANGUAGE ──
  function applyPageLanguage(lang) {
    // Direction
    document.documentElement.dir = lang === "ar" ? "rtl" : "ltr";
    document.documentElement.lang = lang;

    // Text translation
    document.querySelectorAll("[data-en][data-ar]").forEach(function (el) {
      const text = el.getAttribute("data-" + lang);

      if (text !== null) {
        el.textContent = text;
      }
    });

    // Placeholder translation
    document
      .querySelectorAll("[data-en-placeholder][data-ar-placeholder]")
      .forEach(function (el) {
        const placeholder = el.getAttribute("data-" + lang + "-placeholder");

        if (placeholder !== null) {
          el.placeholder = placeholder;
        }
      });

    // Logo
    const logo = document.getElementById("expoLogo");

    if (logo) {
      logo.src =
        lang === "ar" ? "expo2026_ar_white.png" : "expo2026_en_white.png";
    }

    // Save language
    localStorage.setItem("lang", lang);

    currentLang = lang;
  }

  // ── TOGGLE LANGUAGE ──
  window.toggleLang = function () {
    const newLang = currentLang === "en" ? "ar" : "en";

    applyPageLanguage(newLang);
  };

  // ── MENU ──
  window.toggleMenu = function (e) {
    if (e) e.stopPropagation();
  };

  // ── INIT ──
  document.addEventListener("DOMContentLoaded", function () {
    applyPageLanguage(currentLang);
  });

  // ── LOGIN ──
  const loginForm = document.getElementById("loginForm");

  if (loginForm) {
    loginForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const email = document.getElementById("email").value.trim();

      const password = document.getElementById("password").value;

      const errorMsg = document.getElementById("errorMsg");

      if (errorMsg) {
        errorMsg.style.display = "none";
        errorMsg.textContent = "";
      }

      fetch("login.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          email: email,
          password: password,
        }),
        credentials: "include",
      })
        .then(function (response) {
          return response.json();
        })
        .then(function (data) {
          if (data.status === "success") {
            window.location.href = "Home.php";
          } else {
            if (errorMsg) {
              if (data.message === "Invalid email or password") {
                errorMsg.textContent =
                  currentLang === "ar"
                    ? "البريد الإلكتروني أو كلمة المرور غير صحيحة"
                    : "Invalid email or password";
              } else if (data.message === "Account not activated") {
                errorMsg.textContent =
                  currentLang === "ar"
                    ? "الحساب غير مفعل"
                    : "Account not activated";
              } else {
                errorMsg.textContent =
                  currentLang === "ar" ? "حدث خطأ" : "An error occurred";
              }

              errorMsg.style.display = "block";
            }
          }
        })
        .catch(function (error) {
          console.error(error);

          if (errorMsg) {
            errorMsg.textContent =
              currentLang === "ar" ? "خطأ في الاتصال" : "Connection error";

            errorMsg.style.display = "block";
          }
        });
    });
  }
})();
