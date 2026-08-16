(function () {
  function placeTrustStrip() {
    if (document.getElementById('cf-trust-strip')) return;
    var sub = document.querySelector('.home-banner .banner-subtext');
    var btn = document.querySelector('.home-banner a.banner-btn, .home-banner .banner-btn');
    if (!sub) return;
    var el = document.createElement('div');
    el.id = 'cf-trust-strip';
    el.className = 'cf-trust-strip';
    el.innerHTML = '<a href="https://share.google/jZcVhdUkybPpbiz7O" target="_blank" rel="noopener noreferrer" aria-label="Coinsfera Google rating 4.9 out of 5 from 1043 reviews">'
      + '<span class="cf-stars" aria-hidden="true">★★★★★</span>'
      + '<span class="cf-score">4.9</span>'
      + '<span class="cf-count">1,043 Google reviews</span>'
      + '</a>';
    if (btn && btn.parentElement === sub.parentElement) {
      sub.parentElement.insertBefore(el, btn);
    } else {
      sub.insertAdjacentElement('afterend', el);
    }
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', placeTrustStrip);
  } else {
    placeTrustStrip();
  }
})();
