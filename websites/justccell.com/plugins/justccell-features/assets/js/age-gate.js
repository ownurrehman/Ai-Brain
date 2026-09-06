/**
 * Age verification modal — client-side cookie gate (cache-safe).
 *
 * Developed by Rank Ray — https://rankray.com
 */
(function () {
  'use strict';

  var root = document.getElementById('jc-age-gate');
  if (!root) {
    return;
  }

  var COOKIE_NAME = 'justccell_age_verified';
  var STORAGE_KEY = 'justccell_age_verified';
  var days = parseInt(root.getAttribute('data-cookie-days') || '30', 10);
  if (!days || days < 1) {
    days = 30;
  }
  var declineUrl = root.getAttribute('data-decline-url') || 'https://www.google.com';

  function readCookie(name) {
    var parts = document.cookie ? document.cookie.split('; ') : [];
    var i;
    for (i = 0; i < parts.length; i += 1) {
      var pair = parts[i].split('=');
      if (pair[0] === name) {
        return decodeURIComponent(pair.slice(1).join('='));
      }
    }
    return '';
  }

  function isVerified() {
    if (readCookie(COOKIE_NAME) === 'true') {
      return true;
    }
    try {
      return window.localStorage.getItem(STORAGE_KEY) === 'true';
    } catch (err) {
      return false;
    }
  }

  function setVerified() {
    var maxAge = days * 86400;
    document.cookie =
      COOKIE_NAME + '=true; path=/; max-age=' + maxAge + '; SameSite=Lax';
    try {
      window.localStorage.setItem(STORAGE_KEY, 'true');
    } catch (err) {
      /* storage blocked — cookie still applies */
    }
  }

  function showGate() {
    root.hidden = false;
    root.setAttribute('aria-hidden', 'false');
    document.body.classList.add('jc-age-gate-open');
    var confirm = root.querySelector('.jc-age-gate__confirm');
    if (confirm) {
      confirm.focus();
    }
  }

  function hideGate() {
    root.classList.add('jc-age-gate--closing');
    document.body.classList.remove('jc-age-gate-open');
    window.setTimeout(function () {
      root.hidden = true;
      root.setAttribute('aria-hidden', 'true');
      root.classList.remove('jc-age-gate--closing');
    }, 320);
  }

  if (isVerified()) {
    return;
  }

  showGate();

  var confirmBtn = root.querySelector('.jc-age-gate__confirm');
  var declineBtn = root.querySelector('.jc-age-gate__decline');

  if (confirmBtn) {
    confirmBtn.addEventListener('click', function () {
      setVerified();
      hideGate();
    });
  }

  if (declineBtn) {
    declineBtn.addEventListener('click', function () {
      window.location.href = declineUrl;
    });
  }
})();
