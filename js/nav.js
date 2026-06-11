/**
 * Janbol News — Shared Navbar + Footer Injector
 * Self-contained IIFE. Injects header and footer into any page.
 * Usage: include this script; it runs automatically on DOMContentLoaded.
 */
(function() {
  'use strict';

  /* ═════════════════════════════════
     NAVBAR HTML
  ═════════════════════════════════ */
  function buildNavbarHTML() {
    const cats = (window.JN_CONFIG && window.JN_CONFIG.categories) || [];
    const lang = (window.getLang && window.getLang()) || 'hi';

    const catLinks = cats.slice(0, 8).map(function(cat) {
      return `<a href="/category.html?cat=${cat.slug}" data-cat="${cat.slug}" class="hi-text">${lang === 'hi' ? cat.hi : cat.en}</a>`;
    }).join('');

    const drawerLinks = cats.map(function(cat) {
      return `<a href="/category.html?cat=${cat.slug}" class="hi-text">${lang === 'hi' ? cat.hi : cat.en}</a>`;
    }).join('');

    return `
    <!-- Breaking News Ticker -->
    <div class="breaking-ticker" id="breaking-ticker">
      <div class="ticker-label">
        <span class="dot"></span>
        <span class="ticker-label-hi hi-text">ब्रेकिंग</span>
        <span>NEWS</span>
      </div>
      <div class="ticker-scroll-wrap">
        <div class="ticker-track" id="ticker-track">
          <span class="ticker-item hi-text">समाचार लोड हो रहे हैं…</span>
        </div>
      </div>
    </div>

    <!-- Top Bar -->
    <div class="topbar">
      <div class="container">
        <div class="topbar-left">
          <span class="topbar-date" id="topbar-date"></span>
          <div class="topbar-edition" id="topbar-editions"></div>
        </div>
        <div class="topbar-right">
          <div class="topbar-social">
            <a href="${(window.JN_CONFIG||{social:{}}).social.facebook||'#'}" target="_blank" rel="noopener" title="Facebook" aria-label="Facebook"><svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
            <a href="${(window.JN_CONFIG||{social:{}}).social.twitter||'#'}" target="_blank" rel="noopener" title="Twitter/X" aria-label="Twitter"><svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
            <a href="${(window.JN_CONFIG||{social:{}}).social.instagram||'#'}" target="_blank" rel="noopener" title="Instagram" aria-label="Instagram"><svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
            <a href="${(window.JN_CONFIG||{social:{}}).social.youtube||'#'}" target="_blank" rel="noopener" title="YouTube" aria-label="YouTube"><svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M23.495 6.205a3.007 3.007 0 0 0-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 0 0 .527 6.205a31.247 31.247 0 0 0-.522 5.805 31.247 31.247 0 0 0 .522 5.783 3.007 3.007 0 0 0 2.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 0 0 2.088-2.088 31.247 31.247 0 0 0 .5-5.783 31.247 31.247 0 0 0-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/></svg></a>
            <a href="${(window.JN_CONFIG||{social:{}}).social.telegram||'#'}" target="_blank" rel="noopener" title="Telegram" aria-label="Telegram"><svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg></a>
          </div>
          <a href="/epaper.html" class="epaper-link">
            📰 ई-पेपर
          </a>
        </div>
      </div>
    </div>

    <!-- Main Navbar -->
    <nav class="navbar" id="main-navbar" role="navigation" aria-label="मुख्य नेविगेशन">
      <div class="container">
        <div class="navbar-inner">
          <!-- Logo -->
          <a href="/index.html" class="navbar-logo" aria-label="Janbol News - होम पेज">
            <div class="logo-icon hi-text">ज</div>
            <div class="logo-text">
              <span class="logo-en">Janbol News</span>
              <span class="logo-hi hi-text">जनबोल न्यूज़</span>
            </div>
          </a>

          <!-- Category Nav -->
          <nav class="navbar-nav" id="navbar-nav" aria-label="श्रेणियाँ">
            ${catLinks}
          </nav>

          <!-- Right: Lang toggle + Search + Hamburger -->
          <div class="navbar-right">
            <div class="lang-toggle" role="group" aria-label="भाषा चुनें">
              <button class="lang-btn hi-text" data-lang="hi" onclick="window.languageToggle('hi')" aria-pressed="${lang==='hi'}">हिं</button>
              <button class="lang-btn" data-lang="en" onclick="window.languageToggle('en')" aria-pressed="${lang==='en'}">EN</button>
            </div>
            <button class="search-btn" id="search-toggle-btn" aria-label="खोजें" title="Search">🔍</button>
            <button class="hamburger" id="hamburger-btn" aria-label="मेनू खोलें" aria-expanded="false">
              <span></span><span></span><span></span>
            </button>
          </div>
        </div>

        <!-- Search Dropdown -->
        <div class="search-dropdown" id="search-dropdown" role="search">
          <div class="search-input-wrap">
            <input type="search" id="nav-search-input" placeholder="खबर खोजें… / Search news…" autocomplete="off" aria-label="समाचार खोजें">
            <button class="search-submit" id="nav-search-submit" aria-label="खोजें">🔍</button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Mobile Drawer -->
    <div class="mobile-drawer" id="mobile-drawer" role="dialog" aria-modal="true" aria-label="नेविगेशन मेनू">
      <div class="mobile-drawer-overlay" id="drawer-overlay"></div>
      <div class="mobile-drawer-panel">
        <div class="drawer-header">
          <a href="/index.html" class="navbar-logo">
            <div class="logo-icon hi-text">ज</div>
            <div class="logo-text">
              <span class="logo-en" style="color:#fff">Janbol News</span>
              <span class="logo-hi hi-text">जनबोल न्यूज़</span>
            </div>
          </a>
          <button class="drawer-close" id="drawer-close-btn" aria-label="बंद करें">✕</button>
        </div>
        <nav class="drawer-nav" aria-label="मोबाइल नेविगेशन">
          <a href="/index.html" class="hi-text">🏠 होम</a>
          ${drawerLinks}
          <a href="/epaper.html" class="hi-text">📰 ई-पेपर</a>
          <a href="/search.html" class="hi-text">🔍 खोजें</a>
        </nav>
        <div class="drawer-lang">
          <button class="lang-btn hi-text" data-lang="hi" onclick="window.languageToggle('hi'); closeDrawer();">हिंदी</button>
          <button class="lang-btn" data-lang="en" onclick="window.languageToggle('en'); closeDrawer();">English</button>
        </div>
      </div>
    </div>
    `;
  }

  /* ═════════════════════════════════
     FOOTER HTML
  ═════════════════════════════════ */
  function buildFooterHTML() {
    const cats = (window.JN_CONFIG && window.JN_CONFIG.categories) || [];
    const social = (window.JN_CONFIG && window.JN_CONFIG.social) || {};
    const lang = (window.getLang && window.getLang()) || 'hi';

    const catLinks1 = cats.slice(0, 5).map(function(cat) {
      return `<a href="/category.html?cat=${cat.slug}" class="hi-text">${lang === 'hi' ? cat.hi : cat.en}</a>`;
    }).join('');

    const catLinks2 = cats.slice(5, 10).map(function(cat) {
      return `<a href="/category.html?cat=${cat.slug}" class="hi-text">${lang === 'hi' ? cat.hi : cat.en}</a>`;
    }).join('');

    const year = new Date().getFullYear();

    return `
    <footer class="footer" role="contentinfo">
      <div class="footer-top">
        <div class="container">
          <div class="footer-grid">

            <!-- Brand Column -->
            <div class="footer-brand">
              <a href="/index.html" class="navbar-logo" style="margin-bottom:12px; display:inline-flex;">
                <div class="logo-icon hi-text">ज</div>
                <div class="logo-text">
                  <span class="logo-en">Janbol News</span>
                  <span class="logo-hi hi-text">जनबोल न्यूज़</span>
                </div>
              </a>
              <p class="footer-tagline hi-text">
                जनबोल न्यूज़ — भारत की ताज़ा खबरें, राजनीति, खेल, मनोरंजन और अधिक। आपकी आवाज़, आपकी खबर।
              </p>
              <div class="footer-social">
                <a href="${social.facebook||'#'}" target="_blank" rel="noopener" aria-label="Facebook"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
                <a href="${social.twitter||'#'}" target="_blank" rel="noopener" aria-label="Twitter/X"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
                <a href="${social.instagram||'#'}" target="_blank" rel="noopener" aria-label="Instagram"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
                <a href="${social.youtube||'#'}" target="_blank" rel="noopener" aria-label="YouTube"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M23.495 6.205a3.007 3.007 0 0 0-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 0 0 .527 6.205a31.247 31.247 0 0 0-.522 5.805 31.247 31.247 0 0 0 .522 5.783 3.007 3.007 0 0 0 2.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 0 0 2.088-2.088 31.247 31.247 0 0 0 .5-5.783 31.247 31.247 0 0 0-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/></svg></a>
                <a href="${social.telegram||'#'}" target="_blank" rel="noopener" aria-label="Telegram"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg></a>
              </div>
            </div>

            <!-- Categories 1 -->
            <div>
              <div class="footer-col-title hi-text">श्रेणियाँ</div>
              <div class="footer-links">
                ${catLinks1}
              </div>
            </div>

            <!-- Categories 2 -->
            <div>
              <div class="footer-col-title hi-text">अधिक</div>
              <div class="footer-links">
                ${catLinks2}
                <a href="/epaper.html" class="hi-text">📰 ई-पेपर</a>
              </div>
            </div>

            <!-- Links -->
            <div>
              <div class="footer-col-title hi-text">जानकारी</div>
              <div class="footer-links">
                <a href="/about.html" class="hi-text">हमारे बारे में</a>
                <a href="/contact.html" class="hi-text">संपर्क करें</a>
                <a href="/advertise.html" class="hi-text">विज्ञापन दें</a>
                <a href="/privacy.html" class="hi-text">गोपनीयता नीति</a>
                <a href="/terms.html" class="hi-text">नियम और शर्तें</a>
                <a href="/sitemap.xml" class="hi-text">साइटमैप</a>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="container">
        <div class="footer-bottom">
          <p class="footer-copyright">
            <span class="hi-text">© ${year} जनबोल न्यूज़। सर्वाधिकार सुरक्षित।</span>
            &nbsp;|&nbsp; © ${year} Janbol News. All rights reserved.
          </p>
          <div class="footer-legal">
            <a href="/privacy.html">Privacy</a>
            <a href="/terms.html">Terms</a>
            <a href="/sitemap.xml">Sitemap</a>
          </div>
        </div>
      </div>
    </footer>
    `;
  }

  /* ═════════════════════════════════
     ACTIVE LINK DETECTION
  ═════════════════════════════════ */
  function setActiveLinks() {
    const path = window.location.pathname;
    const params = new URLSearchParams(window.location.search);
    const currentCat = params.get('cat');

    document.querySelectorAll('.navbar-nav a, .drawer-nav a').forEach(function(link) {
      const href = link.getAttribute('href');
      if (!href) return;

      // Home
      if ((href === '/index.html' || href === '/') && (path === '/' || path === '/index.html')) {
        link.classList.add('active');
        return;
      }

      // Category pages
      if (href.includes('category.html')) {
        const u = new URL(href, window.location.origin);
        const cat = u.searchParams.get('cat');
        if (cat && cat === currentCat) {
          link.classList.add('active');
          return;
        }
      }

      // Other exact matches
      if (href !== '/index.html' && href !== '/' && path.includes(href.replace(/^\//, ''))) {
        link.classList.add('active');
      }
    });
  }

  /* ═════════════════════════════════
     TOPBAR DATE
  ═════════════════════════════════ */
  function setTopbarDate() {
    const el = document.getElementById('topbar-date');
    if (!el) return;
    const now = new Date();
    const lang = (window.getLang && window.getLang()) || 'hi';
    const opts = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
    const locale = lang === 'hi' ? 'hi-IN' : 'en-IN';
    el.textContent = now.toLocaleDateString(locale, opts);
  }

  /* ═════════════════════════════════
     TOPBAR EDITIONS
  ═════════════════════════════════ */
  function buildEditions() {
    const el = document.getElementById('topbar-editions');
    if (!el || !window.JN_CONFIG) return;
    const lang = window.getLang();
    window.JN_CONFIG.editions.slice(0, 5).forEach(function(ed) {
      const a = document.createElement('a');
      a.href = '/category.html?edition=' + ed.slug;
      a.className = 'hi-text';
      a.textContent = lang === 'hi' ? ed.hi : ed.en;
      el.appendChild(a);
    });
  }

  /* ═════════════════════════════════
     BREAKING TICKER
  ═════════════════════════════════ */
  function renderTicker(items) {
    const track = document.getElementById('ticker-track');
    if (!track || !items || !items.length) return;

    // Build two copies for seamless loop
    const lang = (window.getLang && window.getLang()) || 'hi';
    const all = [...items, ...items]; // duplicate for seamless animation
    track.innerHTML = '';

    all.forEach(function(item) {
      const span = document.createElement('a');
      span.className = 'ticker-item hi-text';
      span.textContent = getLocalText(item, 'title') || item.title || '';
      span.href = '/article.html?slug=' + encodeURIComponent(item.slug || '');
      track.appendChild(span);
    });

    // Adjust animation duration based on content length
    const totalWidth = track.scrollWidth;
    const duration = Math.max(20, totalWidth / 60);
    track.style.animationDuration = duration + 's';
  }

  async function loadTicker() {
    if (!window.newsAPI) return;
    try {
      const data = await window.newsAPI.fetchBreaking();
      const items = data.articles || data.data || data || [];
      renderTicker(items);
    } catch (e) {
      // Keep placeholder text
    }
  }

  /* ═════════════════════════════════
     HAMBURGER / DRAWER
  ═════════════════════════════════ */
  window.closeDrawer = function() {
    const drawer   = document.getElementById('mobile-drawer');
    const hamburger = document.getElementById('hamburger-btn');
    if (drawer) drawer.classList.remove('open');
    if (hamburger) {
      hamburger.classList.remove('open');
      hamburger.setAttribute('aria-expanded', 'false');
    }
    document.body.style.overflow = '';
  };

  function openDrawer() {
    const drawer    = document.getElementById('mobile-drawer');
    const hamburger = document.getElementById('hamburger-btn');
    if (drawer) drawer.classList.add('open');
    if (hamburger) {
      hamburger.classList.add('open');
      hamburger.setAttribute('aria-expanded', 'true');
    }
    document.body.style.overflow = 'hidden';
  }

  /* ═════════════════════════════════
     SEARCH DROPDOWN
  ═════════════════════════════════ */
  function initSearch() {
    const toggleBtn  = document.getElementById('search-toggle-btn');
    const dropdown   = document.getElementById('search-dropdown');
    const input      = document.getElementById('nav-search-input');
    const submitBtn  = document.getElementById('nav-search-submit');

    if (!toggleBtn || !dropdown) return;

    toggleBtn.addEventListener('click', function() {
      const isOpen = dropdown.classList.toggle('open');
      if (isOpen && input) {
        setTimeout(function() { input.focus(); }, 50);
      }
    });

    function doSearch() {
      const q = input ? input.value.trim() : '';
      if (q) {
        window.location.href = '/search.html?q=' + encodeURIComponent(q);
      }
    }

    if (submitBtn) submitBtn.addEventListener('click', doSearch);

    if (input) {
      input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') doSearch();
        if (e.key === 'Escape') dropdown.classList.remove('open');
      });
    }

    // Close on outside click
    document.addEventListener('click', function(e) {
      if (!dropdown.contains(e.target) && e.target !== toggleBtn) {
        dropdown.classList.remove('open');
      }
    });
  }

  /* ═════════════════════════════════
     INIT
  ═════════════════════════════════ */
  function init() {
    // Inject navbar before #app-root or at start of body
    const navTarget = document.getElementById('navbar-inject');
    const navHTML = buildNavbarHTML();
    if (navTarget) {
      navTarget.innerHTML = navHTML;
    } else {
      const first = document.body.firstElementChild;
      const wrapper = document.createElement('div');
      wrapper.id = 'navbar-wrapper';
      wrapper.innerHTML = navHTML;
      document.body.insertBefore(wrapper, first);
    }

    // Inject footer
    const footTarget = document.getElementById('footer-inject');
    const footHTML = buildFooterHTML();
    if (footTarget) {
      footTarget.innerHTML = footHTML;
    } else {
      const wrapper = document.createElement('div');
      wrapper.id = 'footer-wrapper';
      wrapper.innerHTML = footHTML;
      document.body.appendChild(wrapper);
    }

    // Wire events
    setTopbarDate();
    buildEditions();
    setActiveLinks();
    initSearch();

    const hamburger = document.getElementById('hamburger-btn');
    if (hamburger) hamburger.addEventListener('click', openDrawer);

    const drawerClose = document.getElementById('drawer-close-btn');
    if (drawerClose) drawerClose.addEventListener('click', closeDrawer);

    const overlay = document.getElementById('drawer-overlay');
    if (overlay) overlay.addEventListener('click', closeDrawer);

    // ESC to close drawer
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') closeDrawer();
    });

    // Lang toggle buttons state
    const lang = (window.getLang && window.getLang()) || 'hi';
    document.querySelectorAll('.lang-btn').forEach(function(btn) {
      btn.classList.toggle('active', btn.dataset.lang === lang);
    });

    // Load breaking ticker
    loadTicker();

    // Auto-refresh ticker
    const interval = (window.JN_CONFIG && window.JN_CONFIG.tickerRefreshMs) || 60000;
    setInterval(loadTicker, interval);
  }

  // Run after DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
