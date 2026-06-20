(function () {
  var CACHE_KEY = 'jn_fonts';
  var CACHE_TTL = 3600000; // 1 hour

  var FONT_WEIGHTS = {
    'Noto Sans Devanagari': 'wght@300;400;500;700;800',
    'Baloo 2':              'wght@400;500;600;700;800',
    'Mukta':                'wght@300;400;500;700',
    'Laila':                'wght@400;600;700',
    'Hind':                 'wght@300;400;500;600;700',
    'Source Serif 4':       'ital,opsz,wght@0,8..60,300;0,8..60,400;0,8..60,600',
    'Roboto':               'wght@300;400;500;700',
    'Inter':                'wght@300;400;500;600;700',
    'Lato':                 'wght@300;400;700',
    'Merriweather':         'ital,wght@0,300;0,400;0,700',
    'Playfair Display':     'ital,wght@0,400;0,700;1,400',
  };

  function apply(fontHi, fontEn) {
    var parts = [];
    if (fontHi && FONT_WEIGHTS[fontHi]) {
      parts.push('family=' + encodeURIComponent(fontHi) + ':' + FONT_WEIGHTS[fontHi]);
    }
    if (fontEn && fontEn !== fontHi && FONT_WEIGHTS[fontEn]) {
      parts.push('family=' + encodeURIComponent(fontEn) + ':' + FONT_WEIGHTS[fontEn]);
    }
    if (parts.length) {
      var link = document.createElement('link');
      link.rel = 'stylesheet';
      link.href = 'https://fonts.googleapis.com/css2?' + parts.join('&') + '&display=swap';
      document.head.appendChild(link);
    }
    var css = ':root{';
    if (fontHi) css += "--font-hindi:'" + fontHi + "','Noto Sans Devanagari',sans-serif;";
    if (fontEn) css += "--font-en:'" + fontEn + "','Source Serif 4',Georgia,serif;";
    css += '}';
    var existing = document.getElementById('jn-fonts-override');
    if (existing) existing.remove();
    var style = document.createElement('style');
    style.id = 'jn-fonts-override';
    style.textContent = css;
    document.head.appendChild(style);
  }

  // Apply from cache immediately (avoids FOUC on repeat visits)
  try {
    var cached = JSON.parse(localStorage.getItem(CACHE_KEY) || 'null');
    if (cached && cached.ts && (Date.now() - cached.ts) < CACHE_TTL) {
      if (cached.font_hi || cached.font_en) apply(cached.font_hi, cached.font_en);
      return;
    }
  } catch (e) {}

  // Cache miss or expired — fetch from API
  var isLocal = ['localhost', '127.0.0.1'].includes(window.location.hostname);
  var apiBase = isLocal ? 'http://localhost:8000/api' : '/api';
  fetch(apiBase + '/settings')
    .then(function (r) { return r.json(); })
    .then(function (data) {
      var fontHi = (data && data.data && data.data.font_hi) || '';
      var fontEn = (data && data.data && data.data.font_en) || '';
      try {
        localStorage.setItem(CACHE_KEY, JSON.stringify({ font_hi: fontHi, font_en: fontEn, ts: Date.now() }));
      } catch (e) {}
      if (fontHi || fontEn) apply(fontHi, fontEn);
    })
    .catch(function () {});
})();
