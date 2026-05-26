// ==================== LANGUAGE TRANSLATIONS ====================
const translations = {
    en: {
        title: "Visitor Registration",
        subtitle: "Please fill in your details to confirm your attendance at EXPO IAU 2026",
        fullName: "Full Name *",
        email: "Email *",
        phone: "Phone Number *",
        organization: "Organization (Optional)",
        position: "Position (Optional)",
        registerBtn: "Register",
        successTitle: "Registration Confirmed!",
        successMessage: "Thank you for registering. You will receive a confirmation email shortly.",
        continueBtn: "Continue to Website",
        footer: "Vice Deanship of Scientific Research and Innovation",
        placeholderName: "Enter your full name",
        placeholderEmail: "your@email.com",
        placeholderPhone: "+966 XXXXXXXX",
        placeholderOrg: "Company / University",
        placeholderPosition: "Job title",
        t1: "Sign-up", t2: "Health", t3: "Economies", t4: "Sustainability",
        t5: "Energy", t6: "Education", t7: "Research",
        errorName: "Please enter your full name",
        errorEmail: "Please enter a valid email address",
        errorPhone: "Please enter your phone number",
        errorConnection: "Connection error. Please try again.",
        errorServer: "Server error. Please try again."
    },
    ar: {
        title: "تسجيل الزائر",
        subtitle: "يرجى ملء بياناتك لتأكيد حضورك في إكسبو 2026",
        fullName: "الاسم الكامل *",
        email: "البريد الإلكتروني *",
        phone: "رقم الجوال *",
        organization: "جهة العمل (اختياري)",
        position: "المنصب (اختياري)",
        registerBtn: "تسجيل",
        successTitle: "تم تأكيد التسجيل!",
        successMessage: "شكراً لتسجيلك. ستصلك رسالة تأكيد عبر البريد الإلكتروني قريباً.",
        continueBtn: "متابعة إلى الموقع",
        footer: "وكالة البحث والابتكار العلمي",
        placeholderName: "أدخل اسمك الكامل",
        placeholderEmail: "بريدك@example.com",
        placeholderPhone: "+966 5xxxxxxxx",
        placeholderOrg: "شركة / جامعة",
        placeholderPosition: "المسمى الوظيفي",
        t1: "تسجيل الدخول", t2: "صحة الإنسان", t3: "اقتصاديات المستقبل", t4: "استدامة البيئة",
        t5: "الطاقة والصناعة", t6: "التعليم والقدرات", t7: "الأبحاث المنشورة",
        errorName: "يرجى إدخال الاسم الكامل",
        errorEmail: "يرجى إدخال بريد إلكتروني صحيح",
        errorPhone: "يرجى إدخال رقم الجوال",
        errorConnection: "خطأ في الاتصال. يرجى المحاولة مرة أخرى",
        errorServer: "خطأ في الخادم. يرجى المحاولة مرة أخرى"
    }
};

let currentLang = localStorage.getItem('visitorLang') || 'en';

function applyLanguage() {
    const t = translations[currentLang];
    document.getElementById('titleText').innerText = t.title;
    document.getElementById('subtitleText').innerText = t.subtitle;
    document.getElementById('labelFullName').innerHTML = t.fullName;
    document.getElementById('labelEmail').innerHTML = t.email;
    document.getElementById('labelPhone').innerHTML = t.phone;
    document.getElementById('labelOrganization').innerText = t.organization;
    document.getElementById('labelPosition').innerText = t.position;
    document.getElementById('registerBtnText').innerText = t.registerBtn;
    document.getElementById('successTitle').innerText = t.successTitle;
    document.getElementById('successMessage').innerText = t.successMessage;
    document.getElementById('continueBtnText').innerText = t.continueBtn;
    document.getElementById('footerText').innerText = t.footer;
    document.getElementById('fullName').placeholder = t.placeholderName;
    document.getElementById('email').placeholder = t.placeholderEmail;
    document.getElementById('phone').placeholder = t.placeholderPhone;
    document.getElementById('organization').placeholder = t.placeholderOrg;
    document.getElementById('position').placeholder = t.placeholderPosition;
    
    document.getElementById('t1').innerText = t.t1;
    document.getElementById('t2').innerText = t.t2;
    document.getElementById('t3').innerText = t.t3;
    document.getElementById('t4').innerText = t.t4;
    document.getElementById('t5').innerText = t.t5;
    document.getElementById('t6').innerText = t.t6;
    document.getElementById('t7').innerText = t.t7;
    
    window.errorMessages = {
        name: t.errorName,
        email: t.errorEmail,
        phone: t.errorPhone,
        connection: t.errorConnection,
        server: t.errorServer
    };
    
    const logo = document.getElementById('expoLogo');
    if (currentLang === 'ar') logo.src = 'expo2026_ar_white.png';
    else logo.src = 'expo2026_en_white.png';
    
    const html = document.documentElement;
    html.lang = currentLang;
    html.dir = currentLang === 'ar' ? 'rtl' : 'ltr';
}

window.toggleLang = function() {
    currentLang = currentLang === 'ar' ? 'ar' : 'en';
    localStorage.setItem('visitorLang', currentLang);
    applyLanguage();
};

// ==================== VALIDATION FUNCTIONS ====================
function hideAllErrors() {
    const errors = ['errorName', 'errorEmail', 'errorPhone', 'generalError'];
    errors.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'none';
    });
}

function showFieldError(fieldId, message) {
    const errorEl = document.getElementById(fieldId);
    if (errorEl) {
        errorEl.innerText = message;
        errorEl.style.display = 'block';
    }
}

function showGeneralError(message) {
    const generalError = document.getElementById('generalError');
    if (generalError) {
        generalError.innerText = message;
        generalError.style.display = 'block';
    }
}

function validateForm() {
    hideAllErrors();
    let isValid = true;
    
    const name = document.getElementById('fullName').value.trim();
    if (!name) {
        showFieldError('errorName', window.errorMessages?.name || 'Please enter your full name');
        isValid = false;
    }
    
    const email = document.getElementById('email').value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email || !emailRegex.test(email)) {
        showFieldError('errorEmail', window.errorMessages?.email || 'Please enter a valid email');
        isValid = false;
    }
    
    const phone = document.getElementById('phone').value.trim();
    if (!phone) {
        showFieldError('errorPhone', window.errorMessages?.phone || 'Please enter your phone number');
        isValid = false;
    }
    
    return isValid;
}

// ==================== REGISTRATION LOGIC ====================
document.addEventListener('DOMContentLoaded', function() {
    applyLanguage();

    const submitBtn = document.getElementById('submitRegisterBtn');
    const regForm = document.getElementById('regForm');
    const successSection = document.getElementById('successSection');
    
    let isSubmitting = false;

    submitBtn.addEventListener('click', function() {
        if (isSubmitting) return;
        
        if (!validateForm()) return;
        
        isSubmitting = true;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        
        const name = document.getElementById('fullName').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const org = document.getElementById('organization').value.trim();
        const position = document.getElementById('position').value.trim();
        
        const visitorId = 'VIS' + Date.now() + Math.random().toString(36).substr(2, 8);
        
        const visitorData = {
            visitor_id: visitorId,
            full_name: name,
            email: email,
            phone: phone,
            organization: org,
            position: position,
            attendance: 'yes'
        };
        
        localStorage.setItem('expo_visitor', JSON.stringify(visitorData));
        sessionStorage.setItem('guestMode', 'true');
        sessionStorage.setItem('guestAttendance', 'yes');
        
        fetch('save_visitor.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(visitorData)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                regForm.style.display = 'none';
                successSection.style.display = 'block';
            } else {
                showGeneralError(data.message || (window.errorMessages?.server || 'Server error'));
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span id="registerBtnText">' + (translations[currentLang]?.registerBtn || 'Register') + '</span>';
            }
            isSubmitting = false;
        })
        .catch(err => {
            console.error('Fetch error:', err);
            showGeneralError(window.errorMessages?.connection || 'Connection error. Please try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span id="registerBtnText">' + (translations[currentLang]?.registerBtn || 'Register') + '</span>';
            isSubmitting = false;
        });
    });
    
    const continueBtn = document.getElementById('continueToSiteBtn');
    if (continueBtn) {
        continueBtn.addEventListener('click', function(e) {
            window.location.href = 'Home.html';
        });
    }
});