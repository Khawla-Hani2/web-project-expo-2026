
// ================= MENU =================

fetch("menu.html")

.then(res => res.text())

.then(html => {

  document.getElementById(
    "menu-container"
  ).innerHTML = html;

});



// ================= LANGUAGE =================

let currentLang = "en";

function toggleLang(){

  currentLang =
  currentLang === "en"
  ? "ar"
  : "en";

  document.documentElement.lang =
  currentLang;

  document.documentElement.dir =
  currentLang === "ar"
  ? "rtl"
  : "ltr";



  // EXPO LOGO

  document.getElementById("expoLogo").src =

  currentLang === "ar"

  ? "expo2026_ar_white.png"

  : "expo2026_en_white.png";



  // HEADER

  document.getElementById("t1").innerText =
  currentLang === "ar"
  ? "تسجيل الدخول"
  : "Sign-up";

  document.getElementById("t2").innerText =
  currentLang === "ar"
  ? "الصحة"
  : "Health";

  document.getElementById("t3").innerText =
  currentLang === "ar"
  ? "الاقتصاد"
  : "Economies";

  document.getElementById("t4").innerText =
  currentLang === "ar"
  ? "الاستدامة"
  : "Sustainability";

  document.getElementById("t5").innerText =
  currentLang === "ar"
  ? "الطاقة"
  : "Energy";

  document.getElementById("t6").innerText =
  currentLang === "ar"
  ? "التعليم"
  : "Education";

  document.getElementById("t7").innerText =
  currentLang === "ar"
  ? "الأبحاث"
  : "Research";



  // PAGE TITLES

  document.getElementById("uploadTitle")
  .innerText =
  currentLang === "ar"
  ? "رفع قالب الشهادة"
  : "Upload Certificate Template";


  document.getElementById("typeTitle")
  .innerText =
  currentLang === "ar"
  ? "اختر نوع الشهادة"
  : "Select Certificate Type";


  document.getElementById("winnerTitle")
  .innerText =
  currentLang === "ar"
  ? "اختيار الفائزين"
  : "Select Winners";


  document.getElementById("timeTitle")
  .innerText =
  currentLang === "ar"
  ? "تحديد وقت النتائج"
  : "Set Result Time";


  document.getElementById("submitBtn")
  .innerText =
  currentLang === "ar"
  ? "إرسال"
  : "Submit";



  // CERTIFICATE TYPES

  document.getElementById("teamTitle")
  .innerText =
  currentLang === "ar"
  ? "شهادات الفرق"
  : "Team Certificates";


  document.getElementById("teamDesc")
  .innerText =
  currentLang === "ar"
  ? "إصدار شهادات للفرق الفائزة"
  : "Issue certificates for winning project teams";


  document.getElementById("judgeTitle")
  .innerText =
  currentLang === "ar"
  ? "شهادات المحكمين والمشرفين"
  : "Judges and Supervisors Certificates";


  document.getElementById("judgeDesc")
  .innerText =
  currentLang === "ar"
  ? "إصدار شهادات للمحكمين والمشرفين الأكاديميين"
  : "Issue certificates for judges and academic supervisors";

}



// ================= LOAD TEAMS =================

document.addEventListener(
"DOMContentLoaded",

function(){

const projectsBox =
document.getElementById(
"projectsBox"
);


fetch("get_teams.php")

.then(res => res.json())

.then(data => {

const totalTeams =
data.length;

data.forEach((team,index)=>{

const projectHTML = `

<div class="project team-project">

<div>

<input
type="checkbox"
id="p${index}"
data-id="${team.id}"
>

<label for="p${index}">

<strong>
${team.title}
</strong>

<br>

<small>
Track:
${team.track}
</small>

<br>

<small>
Supervisor:
${team.supervisor}
</small>

<br>

<small>
Final Score:
${team.final_score ?? 0}
</small>

<br>

<small>
Team Members:
${team.members ?? "No members"}
</small>

</label>

</div>

<select class="rank-select">

${generateRanks(totalTeams)}

</select>

</div>

`;

projectsBox.insertAdjacentHTML(
"beforeend",
projectHTML
);

});

preventDuplicateRanks();

});



fetch("get_judges.php")

.then(res => res.json())

.then(data => {

let judgesHTML = `

<div
id="judgesContainer"
style="display:none;"
>

`;

data.forEach(judge => {

judgesHTML += `

<div class="project judge-project">

<div>

<label>

${judge.firstName}
${judge.lastName}

<br>

<small>
${judge.email}
</small>

</label>

</div>

</div>

`;

});

judgesHTML += `</div>`;

projectsBox.insertAdjacentHTML(
"beforeend",
judgesHTML
);

});



const radios =
document.querySelectorAll(
'input[name="certificateType"]'
);

function toggleProjects(){

const selected =
document.querySelector(
'input[name="certificateType"]:checked'
).value;

const teamProjects =
document.querySelectorAll(
".team-project"
);

const judgesContainer =
document.getElementById(
"judgesContainer"
);



if(selected === "team"){

teamProjects.forEach(project => {

project.style.display = "flex";

});

if(judgesContainer){

judgesContainer.style.display =
"none";

}

}

else{

teamProjects.forEach(project => {

project.style.display = "none";

});

if(judgesContainer){

judgesContainer.style.display =
"block";

}

}

}

toggleProjects();

radios.forEach(radio => {

radio.addEventListener(
"change",
toggleProjects
);

});

});



// ================= RANKS =================

function generateRanks(total){

let options =
`<option value="">Select Rank</option>`;

for(let i=1;i<=total;i++){

let medal = "";

if(i===1) medal="🥇";
else if(i===2) medal="🥈";
else if(i===3) medal="🥉";

options += `

<option value="${i}">
${medal} Rank ${i}
</option>

`;

}

return options;

}



// ================= PREVENT DUPLICATE =================

function preventDuplicateRanks(){

document.addEventListener(
"change",

function(e){

if(
e.target.classList.contains(
"rank-select"
)
){

const selectedValues = [];

document.querySelectorAll(
".rank-select"
)

.forEach(select=>{

if(select.value){

selectedValues.push(
select.value
);

}

});

document.querySelectorAll(
".rank-select"
)

.forEach(select=>{

const currentValue =
select.value;

Array.from(select.options)

.forEach(option=>{

if(

option.value !== "" &&

selectedValues.includes(
option.value
)

&&

option.value !== currentValue

){

option.disabled = true;

}

else{

option.disabled = false;

}

});

});

}

});

}



// ================= REMOVE IMAGE =================

function removeImage(){

document.getElementById(
"templateFile"
).value = "";

document.getElementById(
"previewImage"
).src = "";

document.getElementById(
"previewContainer"
).style.display = "none";

}



// ================= SUBMIT =================

function submitAll(){

alert(

currentLang === "ar"

? "تم الإرسال بنجاح"

: "Submitted Successfully"

);

}