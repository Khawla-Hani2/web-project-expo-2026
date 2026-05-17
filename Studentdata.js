// Studentdata.js — Unified for header.php + main.js

(function () {
  "use strict";

  let currentLang = localStorage.getItem("lang") || "en";

  // ===============================
  // Language System
  // ===============================
  function applyStudentLanguage(lang) {
    // Text
    document.querySelectorAll("[data-en][data-ar]").forEach(function (el) {
      const text = el.getAttribute("data-" + lang);
      if (text !== null) {
        el.innerText = text;
      }
    });

    // Placeholders
    document
      .querySelectorAll("[data-en-placeholder][data-ar-placeholder]")
      .forEach(function (el) {
        const ph = el.getAttribute("data-" + lang + "-placeholder");
        if (ph !== null) {
          el.placeholder = ph;
        }
      });

    // Select options
    document
      .querySelectorAll("option[data-en][data-ar]")
      .forEach(function (opt) {
        const text = opt.getAttribute("data-" + lang);
        if (text !== null) {
          opt.innerText = text;
        }
      });
  }

  function init() {
    applyStudentLanguage(currentLang);
  }

  // ===============================
  // Initial Load
  // ===============================
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  // ===============================
  // Language Change Listener
  // ===============================
  document.addEventListener("expoLangChanged", function (e) {
    const lang = e.detail && e.detail.lang ? e.detail.lang : "en";

    currentLang = lang;

    applyStudentLanguage(lang);
  });

  // ===============================
  // Duplicate Email Check
  // ===============================
  window.checkDuplicateEmail = function (input) {
    const emails = document.querySelectorAll('input[name="member_email[]"]');

    let values = [];

    emails.forEach(function (e) {
      if (e.value.trim()) {
        values.push(e.value.trim().toLowerCase());
      }
    });

    const duplicates = values.filter(function (item, index) {
      return values.indexOf(item) !== index;
    });

    if (duplicates.includes(input.value.trim().toLowerCase())) {
      alert(
        currentLang === "ar"
          ? "هذا البريد مستخدم بالفعل!"
          : "This email is already used!",
      );

      input.value = "";
      input.focus();
    }
  };

  // ===============================
  // Generate Members
  // ===============================
  window.generateMembers = function () {
    const countInput = document.getElementById("memberCount");

    const container = document.getElementById("membersContainer");

    const count = parseInt(countInput.value, 10) || 0;

    container.innerHTML = "";

    if (count < 1 || count > 10) {
      return;
    }

    for (let i = 0; i < count; i++) {
      const row = document.createElement("div");
      row.className = "member-row-grid";

      // ================= NAME =================

      const nameGroup = document.createElement("div");

      const nameLabel = document.createElement("label");

      nameLabel.setAttribute("data-en", "Member " + (i + 1) + " Name");

      nameLabel.setAttribute("data-ar", "اسم العضو " + (i + 1));

      nameLabel.textContent =
        currentLang === "ar"
          ? "اسم العضو " + (i + 1)
          : "Member " + (i + 1) + " Name";

      const nameInput = document.createElement("input");

      nameInput.type = "text";
      nameInput.name = "member_name[]";

      nameInput.placeholder =
        currentLang === "ar" ? "الاسم الكامل" : "Full Name";

      nameInput.setAttribute("data-en-placeholder", "Full Name");

      nameInput.setAttribute("data-ar-placeholder", "الاسم الكامل");

      nameInput.required = true;

      nameGroup.appendChild(nameLabel);
      nameGroup.appendChild(nameInput);

      // ================= EMAIL =================

      const emailGroup = document.createElement("div");

      const emailLabel = document.createElement("label");

      emailLabel.setAttribute("data-en", "Member " + (i + 1) + " Email");

      emailLabel.setAttribute("data-ar", "بريد العضو " + (i + 1));

      emailLabel.textContent =
        currentLang === "ar"
          ? "بريد العضو " + (i + 1)
          : "Member " + (i + 1) + " Email";

      const emailInput = document.createElement("input");

      emailInput.type = "email";
      emailInput.name = "member_email[]";

      emailInput.placeholder = "email@example.com";

      emailInput.setAttribute("data-en-placeholder", "email@example.com");

      emailInput.setAttribute("data-ar-placeholder", "email@example.com");

      emailInput.required = true;

      emailInput.addEventListener("blur", function () {
        checkDuplicateEmail(this);
      });

      emailGroup.appendChild(emailLabel);
      emailGroup.appendChild(emailInput);

      // ================= APPEND =================

      row.appendChild(nameGroup);
      row.appendChild(emailGroup);

      container.appendChild(row);
    }

    applyStudentLanguage(currentLang);
  };

  // ===============================
  // AJAX Submit
  // ===============================
  document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("projectForm");

    if (!form) return;

    form.addEventListener("submit", function (e) {
      e.preventDefault();

      const count = parseInt(document.getElementById("memberCount").value) || 0;

      if (count <= 0) {
        alert(
          currentLang === "ar"
            ? "يرجى إدخال عدد الأعضاء"
            : "Please enter number of members",
        );

        return;
      }

      const inputs = document.querySelectorAll("#membersContainer input");

      for (let input of inputs) {
        if (!input.value.trim()) {
          alert(
            currentLang === "ar"
              ? "يرجى ملء جميع حقول الأعضاء"
              : "Please fill all member fields",
          );

          return;
        }
      }

      const formData = new FormData(form);

      fetch("save_project.php", {
        method: "POST",
        body: formData,
      })
        .then(function (res) {
          return res.json();
        })

        .then(function (data) {
          alert(data.message);

          if (data.status === "success") {
            window.location.href = data.redirect || "poster.php";
          }
        })

        .catch(function (err) {
          console.error(err);

          alert(
            currentLang === "ar"
              ? "فشل إرسال المشروع"
              : "Failed to send project",
          );
        });
    });
  });
})();
