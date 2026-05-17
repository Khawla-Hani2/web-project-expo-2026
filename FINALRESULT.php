<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "expo2026"
);

$query = mysqli_query($conn, "

SELECT

projects.id,
projects.title,
projects.track,
projects.supervisor,

GROUP_CONCAT(
project_members.member_name
SEPARATOR ', '
) AS members,

MAX(evaluations.total_score)
AS final_score

FROM projects

LEFT JOIN project_members
ON project_members.project_id = projects.id

LEFT JOIN evaluations
ON evaluations.project_id = projects.id

GROUP BY projects.id

ORDER BY final_score DESC

");

$teams = [];

while($row = mysqli_fetch_assoc($query)){

    $teams[] = $row;

}

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>
EXPO IAU 2026 | Final Results
</title>

<link rel="stylesheet" href="style.css">

<link rel="stylesheet"href="FINALRESULT.css">

</head>

<body class="final-result-page"
onclick="outsideClick(event)">

<!-- HEADER -->
<div class="top-strip">

<div class="logoL">
<img src="IAU_logo_white.png">
</div>

<div class="seg s1">
<img src="user_vector.png">
<span id="t1">Sign-up</span>
</div>

<div class="seg s2">
<img src="Human_Health.png">
<span id="t2">Health</span>
</div>

<div class="seg s3">
<img src="Future_Economies.png">
<span id="t3">Economies</span>
</div>

<div class="seg s4">
<img src="Environmental_Sustainability.png">
<span id="t4">Sustainability</span>
</div>

<div class="seg s5">
<img src="Energy_and_Industry.png">
<span id="t5">Energy</span>
</div>

<div class="seg s6">
<img src="Education_and_Development.png">
<span id="t6">Education</span>
</div>

<div class="seg s7">
<img src="Research_Publications.png">
<span id="t7">Research</span>
</div>

<div class="logoR">
<img
src="expo2026_en_white.png"
id="expoLogo">
</div>

</div>

<!-- SUBBAR -->
<div class="subbar">

<div class="menu-btn"
onclick="toggleMenu(event)">

<div></div>
<div></div>
<div></div>

</div>

<div class="lang-btn"
onclick="toggleLang()">

<img src="lang.png">

</div>

</div>

<!-- MENU -->
<div class="side-menu"
id="menu">

<div class="close-x"
onclick="toggleMenu(event)">

×

</div>

<a href="#" id="m1">Home</a>
<a href="#" id="m2">Announcements</a>
<a href="#" id="m3">Event Schedule</a>
<a href="#" id="m4">Sponsors</a>

</div>

<!-- MAIN -->
<main class="main-content">

<h1 class="page-title"
id="pageTitle">

Final Results

</h1>

<p class="page-subtitle"
id="pageSubtitle">

View winners and rankings by track

</p>

<!-- FILTERS -->
<section class="section">

<h2 id="filterTitle">

Filters

</h2>

<div class="filters">

<div>

<label for="trackSelect">
Track
</label>

<select id="trackSelect">

<option value="all">
All Tracks
</option>

<option value="Human Health">
Human Health
</option>

<option value="Environmental Sustainability & Basic Needs">
Environmental Sustainability & Basic Needs
</option>

<option value="Leadership in Energy & Industry">
Leadership in Energy & Industry
</option>

<option value="Economics of the Future">
Economics of the Future
</option>

<option value="Education & Human Capacity Development">
Education & Human Capacity Development
</option>

<option value="Published Research">
Published Research
</option>

</select>

</div>

<div>

<label for="sortSelect">
Sort by
</label>

<select id="sortSelect">

<option value="rankAsc">
Rank
</option>

<option value="scoreDesc">
Score High to Low
</option>

<option value="nameAsc">
Project Name A-Z
</option>

</select>

</div>

<div>

<label for="searchInput">
Search
</label>

<input
type="text"
id="searchInput"
placeholder="Search project">

</div>

<div class="actions">

<button
type="button"
id="applyBtn">

Apply

</button>

<button
type="button"
id="resetBtn"
class="reset-btn">

Reset

</button>

<button
type="button"
id="printBtn">

Print / PDF

</button>

</div>

</div>

</section>

<!-- WINNERS -->
<section class="section">

<h2>

Winners

<span class="pill"
id="winnersCount">

0 Winners

</span>

</h2>

<div class="table-wrap">

<table>

<thead>

<tr>

<th>Rank</th>
<th>Project</th>
<th>Members</th>
<th>Score</th>
<th>Track</th>

</tr>

</thead>

<tbody id="winnersBody">

<?php

$rank = 1;

foreach($teams as $team){

if($rank > 3){
break;
}

?>

<tr

data-rank="<?php echo $rank; ?>"

data-score="<?php echo $team['final_score'] ?? 0; ?>"

data-project="<?php echo $team['title']; ?>"

data-track="<?php echo strtolower($team['track']); ?>"

>

<td>
#<?php echo $rank; ?>
</td>

<td>
<?php echo $team['title']; ?>
</td>

<td>
<?php echo $team['members']; ?>
</td>

<td>
<?php echo $team['final_score'] ?? 0; ?>
</td>

<td>
<?php echo $team['track']; ?>
</td>

</tr>

<?php

$rank++;

}

?>

</tbody>

</table>

</div>

</section>

<!-- ALL PROJECTS -->
<section class="section">

<h2>

Ranking All Projects

<span class="pill"
id="rankingCount">

0 Projects

</span>

</h2>

<div class="table-wrap">

<table>

<thead>

<tr>

<th>Rank</th>
<th>Project</th>
<th>Members</th>
<th>Score</th>
<th>Track</th>
<th>Supervisor</th>

</tr>

</thead>

<tbody id="rankingBody">

<?php

$rank = 1;

foreach($teams as $team){

?>

<tr

data-rank="<?php echo $rank; ?>"

data-score="<?php echo $team['final_score'] ?? 0; ?>"

data-project="<?php echo $team['title']; ?>"

data-track="<?php echo strtolower($team['track']); ?>"

>

<td>
#<?php echo $rank; ?>
</td>

<td>
<?php echo $team['title']; ?>
</td>

<td>
<?php echo $team['members']; ?>
</td>

<td>
<?php echo $team['final_score'] ?? 0; ?>
</td>

<td>
<?php echo $team['track']; ?>
</td>

<td>
<?php echo $team['supervisor']; ?>
</td>

</tr>

<?php

$rank++;

}

?>

</tbody>

</table>

</div>

</section>

</main>

<!-- FOOTER -->
<div class="footer-img">

<img src="footer.png">

<div class="footer-text"
id="footerText">

Vice Deanship of Scientific Research and Innovation

</div>

</div>

<script src="FINALRESULT.js"></script>

</body>
</html>