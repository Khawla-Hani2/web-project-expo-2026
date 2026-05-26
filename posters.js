// posters.js — Unified for header.php + main.js
// Translates page-specific text via data-en / data-ar attributes

/* posters.js — Unified for header.php + main.js */
(function () {
  let currentLang = localStorage.getItem("lang") || "ar";

  function applyPosterLanguage(lang) {
    document.querySelectorAll("[data-en][data-ar]").forEach(function (el) {
      const text = el.getAttribute("data-" + lang);
      if (text !== null) el.innerText = text;
    });
  }

  function init() {
    applyPosterLanguage(currentLang);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  document.addEventListener("expoLangChanged", function (e) {
    const newLang = e.detail && e.detail.lang ? e.detail.lang : "en";
    currentLang = newLang;
    applyPosterLanguage(newLang);
  });

  /* ── preserved file helpers ── */
  let state = { pdf: false, ppt: false };

  window.formatSize = function (bytes) {
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + " KB";
    return (bytes / (1024 * 1024)).toFixed(2) + " MB";
  };

  window.handleFile = function (type, input) {
    const file = input.files[0];
    if (!file) return;
    const nameEl = document.getElementById("name-" + type);
    const sizeEl = document.getElementById("size-" + type);
    const faceEl = document.getElementById("face-" + type);
    const previewEl = document.getElementById("preview-" + type);
    if (nameEl) nameEl.textContent = file.name;
    if (sizeEl) sizeEl.textContent = formatSize(file.size);
    if (faceEl) faceEl.hidden = true;
    if (previewEl) previewEl.hidden = false;
    state[type] = true;
    updateSubmit();
  };

  window.deleteFile = function (type) {
    const input = document.getElementById("input-" + type);
    const faceEl = document.getElementById("face-" + type);
    const previewEl = document.getElementById("preview-" + type);
    if (input) input.value = "";
    if (faceEl) faceEl.hidden = false;
    if (previewEl) previewEl.hidden = true;
    state[type] = false;
    updateSubmit();
  };

  window.updateSubmit = function () {
    const btn = document.getElementById("submit-btn");
    const hint = document.getElementById("submit-hint");
    if (!btn || !hint) return;
    const both = state.pdf && state.ppt;
    btn.disabled = !both;
    hint.style.display = both ? "none" : "block";
  };

  document.querySelectorAll(".drop-zone").forEach(function (zone) {
    zone.addEventListener("dragover", function (e) {
      e.preventDefault();
      zone.classList.add("dragover");
    });
    zone.addEventListener("dragleave", function () {
      zone.classList.remove("dragover");
    });
    zone.addEventListener("drop", function (e) {
      e.preventDefault();
      zone.classList.remove("dragover");
      const type = zone.id === "dz-pdf" ? "pdf" : "ppt";
      const input = document.getElementById("input-" + type);
      if (!input || !e.dataTransfer.files.length) return;
      const dt = new DataTransfer();
      dt.items.add(e.dataTransfer.files[0]);
      input.files = dt.files;
      handleFile(type, input);
    });
  });

  const uploadForm = document.getElementById("uploadForm");
  if (uploadForm) {
    uploadForm.addEventListener("submit", function () {
      const btn = document.getElementById("submitBtn");
      if (!btn) return;
      btn.disabled = true;
      btn.innerText = currentLang === "ar" ? "جاري الرفع..." : "Uploading...";
    });
  }
})();
