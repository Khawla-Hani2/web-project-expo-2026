const translations = {
    en: {
        title: "Visitor Registration",
        subtitle: "Please fill in your details to confirm your attendance at EXPO IAU 2026",
        fullName: "Full Name *",
        email: "Email *",
        phone: "Phone Number *",
        organization: "Organization (Optional)",
        position: "Position (Optional)",
        registerBtn: "Register & Get Entry Pass",
        qrTitle: "Your Entry Pass",
        qrSaveMsg: "Please save or screenshot this QR code.",
        qrInstruction: "Show this QR at the entrance of EXPO IAU 2026.",
        downloadBtn: "Download QR Code",
        continueBtn: "Continue to Website",
        footer: "Vice Deanship of Scientific Research and Innovation",
        placeholderName: "Enter your full name",
        placeholderEmail: "your@email.com",
        placeholderPhone: "+966 XXXXXXXX",
        placeholderOrg: "Company / University",
        placeholderPosition: "Job title",
        // top bar
        t1: "Sign-up", t2: "Health", t3: "Economies", t4: "Sustainability",
        t5: "Energy", t6: "Education", t7: "Research",
        // alerts
        nameRequired: "Please enter your full name",
        emailInvalid: "Please enter a valid email",
        phoneRequired: "Please enter your phone number"
    },
    ar: {
        title: "تسجيل الزائر",
        subtitle: "يرجى ملء بياناتك لتأكيد حضورك في إكسبو 2026",
        fullName: "الاسم الكامل *",
        email: "البريد الإلكتروني *",
        phone: "رقم الجوال *",
        organization: "جهة العمل (اختياري)",
        position: "المنصب (اختياري)",
        registerBtn: "تسجيل والحصول على بطاقة الدخول",
        qrTitle: "بطاقة الدخول الخاصة بك",
        qrSaveMsg: "يرجى حفظ أو تصوير رمز QR هذا",
        qrInstruction: "أظهر هذا الرمز عند مدخل إكسبو 2026",
        downloadBtn: "تحميل رمز QR",
        continueBtn: "متابعة إلى الموقع",
        footer: "وكالة البحث والابتكار العلمي",
        placeholderName: "أدخل اسمك الكامل",
        placeholderEmail: "بريدك@example.com",
        placeholderPhone: "+966 5xxxxxxxx",
        placeholderOrg: "شركة / جامعة",
        placeholderPosition: "المسمى الوظيفي",
        t1: "تسجيل الدخول", t2: "صحة الإنسان", t3: "اقتصاديات المستقبل", t4: "استدامة البيئة",
        t5: "الطاقة والصناعة", t6: "التعليم والقدرات", t7: "الأبحاث المنشورة",
        nameRequired: "يرجى إدخال الاسم الكامل",
        emailInvalid: "يرجى إدخال بريد إلكتروني صحيح",
        phoneRequired: "يرجى إدخال رقم الجوال"
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
    document.getElementById('qrTitle').innerText = t.qrTitle;
    document.getElementById('qrSaveMsg').innerHTML = `<i class="fas fa-info-circle"></i> ${t.qrSaveMsg}`;
    document.getElementById('qrInstruction').innerText = t.qrInstruction;
    document.getElementById('downloadBtnText').innerText = t.downloadBtn;
    document.getElementById('continueBtnText').innerText = t.continueBtn;
    document.getElementById('footerText').innerText = t.footer;
    document.getElementById('fullName').placeholder = t.placeholderName;
    document.getElementById('email').placeholder = t.placeholderEmail;
    document.getElementById('phone').placeholder = t.placeholderPhone;
    document.getElementById('organization').placeholder = t.placeholderOrg;
    document.getElementById('position').placeholder = t.placeholderPosition;
    // Top bar
    document.getElementById('t1').innerText = t.t1;
    document.getElementById('t2').innerText = t.t2;
    document.getElementById('t3').innerText = t.t3;
    document.getElementById('t4').innerText = t.t4;
    document.getElementById('t5').innerText = t.t5;
    document.getElementById('t6').innerText = t.t6;
    document.getElementById('t7').innerText = t.t7;
    // Logo
    const logo = document.getElementById('expoLogo');
    if (currentLang === 'ar') logo.src = 'expo2026_ar_white.png';
    else logo.src = 'expo2026_en_white.png';
    // Direction
    const html = document.documentElement;
    html.lang = currentLang;
    html.dir = currentLang === 'ar' ? 'rtl' : 'ltr';
}

window.toggleLang = function() {
    currentLang = currentLang === 'en' ? 'ar' : 'en';
    localStorage.setItem('visitorLang', currentLang);
    applyLanguage();
};

// ==================== REGISTRATION LOGIC ====================
document.addEventListener('DOMContentLoaded', function() {
    applyLanguage();

    const submitBtn = document.getElementById('submitRegisterBtn');
    const regForm = document.getElementById('regForm');
    const qrSection = document.getElementById('qrSection');
    let visitorData = {};

    function getAlertMessage(key) {
        return translations[currentLang][key];
    }

    submitBtn.addEventListener('click', function() {
        const name = document.getElementById('fullName').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const org = document.getElementById('organization').value.trim();
        const position = document.getElementById('position').value.trim();

        if (!name) { alert(getAlertMessage('nameRequired')); return; }
        if (!email || !/^\S+@\S+\.\S+$/.test(email)) { alert(getAlertMessage('emailInvalid')); return; }
        if (!phone) { alert(getAlertMessage('phoneRequired')); return; }

        const visitorId = 'VIS' + Date.now() + Math.random().toString(36).substr(2, 8);
        const qrContent = JSON.stringify({
            id: visitorId,
            name: name,
            email: email,
            event: "EXPO 2026",
            date: new Date().toLocaleDateString()
        });

        visitorData = {
            visitor_id: visitorId,
            full_name: name,
            email: email,
            phone: phone,
            organization: org,
            position: position,
            attendance: 'yes',
            qr_code: qrContent,
            registered_at: new Date().toISOString()
        };

        localStorage.setItem('expo_visitor', JSON.stringify(visitorData));
        sessionStorage.setItem('guestMode', 'true');
        sessionStorage.setItem('guestAttendance', 'yes');
alert(JSON.stringify(visitorData));
        // Send to PHP
        fetch('visitor.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(visitorData)
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) console.warn('DB save failed:', data.message);
        })
        .catch(err => console.error('Fetch error:', err));

        regForm.style.display = 'none';
        qrSection.style.display = 'block';

        new QRCode(document.getElementById('qrcode'), {
            text: qrContent,
            width: 200,
            height: 200,
            colorDark: "#632949",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    });

    document.getElementById('downloadQrBtn').addEventListener('click', function() {
        const canvas = document.querySelector('#qrcode canvas');
        if (canvas) {
            const link = document.createElement('a');
            link.download = `EXPO_${visitorData.full_name?.replace(/\s/g, '_') || 'visitor'}_pass.png`;
            link.href = canvas.toDataURL();
            link.click();
        } else {
            alert('QR code not ready');
        }
    });

    document.getElementById('continueToSiteBtn').addEventListener('click', function() {
        window.location.href = 'home.html';
    });
});