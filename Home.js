/* EXPO2026 — Home Dashboard Scripts */
/* Safe version: does NOT load main.js */

(function() {
  'use strict';

  /* Search functionality */
  var input = document.getElementById('searchInput');
  var container = document.getElementById('cardsContainer');
  if (input && container) {
    input.addEventListener('input', function() {
      var term = input.value.toLowerCase().trim();
      var cards = container.querySelectorAll('.card-link');
      cards.forEach(function(card) {
        var searchText = (card.getAttribute('data-search') || '').toLowerCase();
        var title = card.textContent.toLowerCase();
        var match = !term || searchText.indexOf(term) !== -1 || title.indexOf(term) !== -1;
        card.style.display = match ? '' : 'none';
      });
    });
  }
})();
