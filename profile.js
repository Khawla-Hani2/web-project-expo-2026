// profile.js — Unified for header.php + main.js
// Handles image preview and page-specific translation

/* profile.js — Unified for header.php + main.js */
(function () {
  let currentLang = localStorage.getItem("lang") || "en";

  function applyProfileLanguage(lang) {
    document.querySelectorAll("[data-en][data-ar]").forEach(function (el) {
      const text = el.getAttribute("data-" + lang);
      if (text !== null) el.innerText = text;
    });
  }

  function init() {
    applyProfileLanguage(currentLang);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  document.addEventListener("expoLangChanged", function (e) {
    const newLang = e.detail && e.detail.lang ? e.detail.lang : "en";
    currentLang = newLang;
    applyProfileLanguage(newLang);
  });

  /* ── preserved image preview ── */
  window.previewImage = function (event) {
    var reader = new FileReader();
    reader.onload = function () {
      var output = document.getElementById("profilePreview");
      if (output) output.src = reader.result;
    };
    if (event.target.files && event.target.files[0]) {
      reader.readAsDataURL(event.target.files[0]);
    }
  };
})();
