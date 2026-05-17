<?php
/**
 * EXPO2026 — Landing Page (Countdown)
 */
require_once 'header.php';
?>

<div class="center">
  <h1 id="title">مرحبًا بكم في اكسبو ٢٠٢٦</h1>
  <h3 id="sub">باقي على المعرض..</h3>

  <div class="countdown">
    <div class="time-box"><h2 id="days">0</h2><div id="dlabel">يوم</div></div>
    <div class="time-box"><h2 id="hours">0</h2><div id="hlabel">ساعة</div></div>
    <div class="time-box"><h2 id="mins">0</h2><div id="mlabel">دقيقة</div></div>
    <div class="time-box"><h2 id="secs">0</h2><div id="slabel">ثانية</div></div>
  </div>
</div>

<script>
(function() {
  var target = new Date('2026-10-01T00:00:00').getTime();
  function update() {
    var now = new Date().getTime();
    var diff = target - now;
    if (diff < 0) diff = 0;
    document.getElementById('days').textContent  = Math.floor(diff / (1000*60*60*24));
    document.getElementById('hours').textContent = Math.floor((diff % (1000*60*60*24)) / (1000*60*60));
    document.getElementById('mins').textContent  = Math.floor((diff % (1000*60*60)) / (1000*60));
    document.getElementById('secs').textContent  = Math.floor((diff % (1000*60)) / 1000);
  }
  update();
  setInterval(update, 1000);
})();
</script>

<?php require_once 'footer.php'; ?>
