/**
 * Janbolnews — Ad injection
 * Ad codes are configured by superadmin in Settings → AdSense.
 * If no code is set for a slot, the slot stays empty.
 */
(function () {
  'use strict';

  function inject(settings) {
    var s = settings || {};
    document.querySelectorAll('.ad-slot').forEach(function (slot) {
      var code = '';

      if (slot.classList.contains('ad-slot-leaderboard') || slot.classList.contains('ad-slot-banner')) {
        code = s.adsense_header || '';
      } else if (slot.classList.contains('ad-slot-square') || slot.classList.contains('ad-slot-rect')) {
        code = s.adsense_sidebar || '';
      } else if (slot.classList.contains('ad-slot-article')) {
        code = s.adsense_article || '';
      }

      if (code) {
        slot.style.display = 'block';
        slot.style.background = 'transparent';
        slot.style.border = 'none';
        slot.innerHTML = code;
        // Trigger AdSense if loaded
        if (window.adsbygoogle) {
          try { (adsbygoogle = window.adsbygoogle || []).push({}); } catch (e) {}
        }
      } else {
        // No code configured — hide the slot entirely
        slot.style.display = 'none';
      }
    });
  }

  function init() {
    var apiBase = (window.CONFIG && window.CONFIG.apiUrl)
      || (['localhost', '127.0.0.1'].indexOf(location.hostname) !== -1
          ? 'http://localhost:8000/api' : '/api');

    fetch(apiBase + '/news/settings', { cache: 'default' })
      .then(function (r) { return r.json(); })
      .then(function (r) {
        var s = r.data || {};
        if (s.ads_enabled === '0' || s.ads_enabled === 'false') {
          // Ads disabled by admin — hide all slots
          document.querySelectorAll('.ad-slot').forEach(function (el) { el.style.display = 'none'; });
          return;
        }
        inject(s);
      })
      .catch(function () {
        // API error — hide slots silently
        document.querySelectorAll('.ad-slot').forEach(function (el) { el.style.display = 'none'; });
      });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
