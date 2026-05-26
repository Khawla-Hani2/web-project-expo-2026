// Hawraa Waleed Al ibrahim
let verifiedEmail = "";
let lang = "ar";

// STEP 1: Email verification
function checkEmail() {
  const email    = document.getElementById("email").value.trim();
  const errorMsg = document.getElementById("errorMsg");

  errorMsg.style.display = "none";

  if (!email) {
    errorMsg.textContent = lang === "ar" ? "أدخل البريد الإلكتروني" : "Please enter your email";
    errorMsg.style.display = "block";
    return;
  }

fetch("forgot_password.php", {
  method: "POST",
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify({ action: "check_email", email })
})

  .then(res => res.json())
  .then(data => {
    if (data.status === "success") {
      verifiedEmail = email;
      document.getElementById("step1").style.display = "none";
      document.getElementById("step2").style.display = "block";
    } else {
      errorMsg.textContent = data.message;
      errorMsg.style.display = "block";
    }
  })
  .catch(() => {
    errorMsg.textContent = lang === "ar" ? "خطأ في الاتصال" : "Connection error";
    errorMsg.style.display = "block";
  });
}

// STEP 2: change Password
function resetPassword() {
  const newPass     = document.getElementById("newPassword").value;
  const confirmPass = document.getElementById("confirmPassword").value;
  const errorMsg2   = document.getElementById("errorMsg2");

  errorMsg2.style.display = "none";

  if (!newPass || !confirmPass) {
    errorMsg2.textContent = lang === "ar" ? "أدخل كلمة المرور" : "Please fill all fields";
    errorMsg2.style.display = "block";
    return;
  }

  if (newPass !== confirmPass) {
    errorMsg2.textContent = lang === "ar" ? "كلمتا المرور غير متطابقتين" : "Passwords do not match";
    errorMsg2.style.display = "block";
    return;
  }

  if (newPass.length < 6) {
    errorMsg2.textContent = lang === "ar" ? "كلمة المرور قصيرة جداً (6 أحرف على الأقل)" : "Password too short (min 6 characters)";
    errorMsg2.style.display = "block";
    return;
  }

fetch("forgot_password.php", {
  method: "POST",
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify({
    action: "reset_password",
    password: newPass
  }),
  credentials: "include"
})
  .then(res => res.json())
  .then(data => {
    if (data.status === "success") {
      alert(lang === "ar" ? "تم تغيير كلمة المرور بنجاح" : "Password changed successfully");
      window.location.href = "login.html";
    } else {
      errorMsg2.textContent = data.message;
      errorMsg2.style.display = "block";
    }
  })
  .catch(() => {
    errorMsg2.textContent = lang === "ar" ? "خطأ في الاتصال" : "Connection error";
    errorMsg2.style.display = "block";
  });
}

function toggleLang() {
  const html = document.documentElement;

  if (lang === "en") {
    lang = "ar";
    html.lang = "ar";
    html.dir  = "rtl";

    document.getElementById("pageTitle").textContent  = "نسيت كلمة المرور";
    document.getElementById("pageTitle2").textContent = "تعيين كلمة مرور جديدة";
    document.getElementById("labelEmail").textContent   = "البريد الإلكتروني";
    document.getElementById("labelNew").textContent     = "كلمة المرور الجديدة";
    document.getElementById("labelConfirm").textContent = "تأكيد كلمة المرور";
    document.getElementById("nextBtn").textContent      = "التالي";
    document.getElementById("resetBtn").textContent     = "تغيير كلمة المرور";
    document.getElementById("backBtn").textContent      = "العودة لتسجيل الدخول";

    document.getElementById("t1").textContent = "تسجيل الدخول";
    document.getElementById("t2").textContent = "صحة الإنسان";
    document.getElementById("t3").textContent = "اقتصاديات المستقبل";
    document.getElementById("t4").textContent = "استدامة البيئة";
    document.getElementById("t5").textContent = "الطاقة والصناعة";
    document.getElementById("t6").textContent = "التعليم والقدرات";
    document.getElementById("t7").textContent = "الأبحاث المنشورة";

    document.getElementById("expoLogo").src = "expo2026_ar_white.png";

  } else {
    lang = "en";
    html.lang = "en";
    html.dir  = "ltr";

    document.getElementById("pageTitle").textContent  = "Forgot Password";
    document.getElementById("pageTitle2").textContent = "Reset Password";
    document.getElementById("labelEmail").textContent   = "Email";
    document.getElementById("labelNew").textContent     = "New Password";
    document.getElementById("labelConfirm").textContent = "Confirm Password";
    document.getElementById("nextBtn").textContent      = "Next";
    document.getElementById("resetBtn").textContent     = "Reset Password";
    document.getElementById("backBtn").textContent      = "Back to Login";

    document.getElementById("t1").textContent = "Sign-up";
    document.getElementById("t2").textContent = "Health";
    document.getElementById("t3").textContent = "Economies";
    document.getElementById("t4").textContent = "Sustainability";
    document.getElementById("t5").textContent = "Energy";
    document.getElementById("t6").textContent = "Education";
    document.getElementById("t7").textContent = "Research";

    document.getElementById("expoLogo").src = "expo2026_en_white.png";
  }
}
