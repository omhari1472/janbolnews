// ── Janbolnews Admin Shared Utilities ────────────────────────────────
const ADMIN_KEY = () => localStorage.getItem('jn_admin_key') || '';
const API_BASE = '../api/admin.php';

// Inject Noto Sans Devanagari font
(function injectFonts() {
  const link = document.createElement('link');
  link.rel = 'preconnect';
  link.href = 'https://fonts.googleapis.com';
  document.head.appendChild(link);
  const link2 = document.createElement('link');
  link2.rel = 'stylesheet';
  link2.href = 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Noto+Sans+Devanagari:wght@400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap';
  document.head.appendChild(link2);
})();

function checkAuth() {
  if (!ADMIN_KEY()) { window.location.href = 'index.html'; return false; }
  return true;
}

function logout() {
  localStorage.removeItem('jn_admin_key');
  window.location.href = 'index.html';
}

async function apiFetch(action, opts = {}, extraParams = '') {
  const key = encodeURIComponent(ADMIN_KEY());
  const url = `${API_BASE}?action=${action}&key=${key}${extraParams}`;
  const res = await fetch(url, {
    ...opts,
    headers: {
      'X-Admin-Key': ADMIN_KEY(),
      ...(opts.body && !(opts.body instanceof FormData) ? { 'Content-Type': 'application/json' } : {}),
      ...(opts.headers || {})
    }
  });
  if (res.status === 401) { logout(); throw new Error('Unauthorized'); }
  return res.json();
}

function toast(msg, type = 'success') {
  const el = document.createElement('div');
  const bg = type === 'error' ? '#C41E3A' : type === 'warning' ? '#d97706' : '#16a34a';
  const icon = type === 'error' ? '✕' : type === 'warning' ? '⚠' : '✓';
  el.style.cssText = `position:fixed;bottom:24px;right:24px;background:${bg};color:#fff;padding:13px 22px;border-radius:10px;font-size:.88rem;font-weight:600;z-index:99999;box-shadow:0 4px 24px rgba(0,0,0,.25);font-family:'Inter',sans-serif;display:flex;align-items:center;gap:8px;animation:toastIn .25s ease;max-width:340px;`;
  el.innerHTML = `<span style="font-size:1rem;">${icon}</span><span>${msg}</span>`;
  document.body.appendChild(el);
  const style = document.createElement('style');
  style.textContent = '@keyframes toastIn{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}';
  document.head.appendChild(style);
  setTimeout(() => { el.style.opacity='0'; el.style.transform='translateY(12px)'; el.style.transition='.2s'; setTimeout(()=>el.remove(),200); }, 3200);
}

function formatDate(dateStr) {
  const d = new Date(dateStr);
  return d.toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
}

function formatDateTime(dateStr) {
  const d = new Date(dateStr);
  return d.toLocaleString('en-IN', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true });
}

function timeAgo(dateStr) {
  const now = Date.now();
  const then = new Date(dateStr).getTime();
  const diff = Math.floor((now - then) / 1000);
  if (diff < 60) return `${diff}s ago`;
  if (diff < 3600) return `${Math.floor(diff/60)}m ago`;
  if (diff < 86400) return `${Math.floor(diff/3600)}h ago`;
  return `${Math.floor(diff/86400)}d ago`;
}

// Inject sidebar
function injectSidebar(activePage) {
  const nav = [
    { href: 'dashboard.html',     icon: 'fa-gauge-high',        label: 'Dashboard',       labelHi: 'डैशबोर्ड' },
    { href: 'articles.html',      icon: 'fa-newspaper',         label: 'Articles',        labelHi: 'समाचार' },
    { href: 'breaking.html',      icon: 'fa-bolt',              label: 'Breaking News',   labelHi: 'ब्रेकिंग न्यूज़' },
    { href: 'epaper.html',        icon: 'fa-file-pdf',          label: 'E-Paper',         labelHi: 'ई-पेपर' },
    { href: 'settings.html',      icon: 'fa-gear',              label: 'Settings',        labelHi: 'सेटिंग्स' },
  ];

  const html = `
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    :root{
      --red:#C41E3A;--red2:#a01830;--red3:rgba(196,30,58,.1);
      --navy:#1a1a1a;--navy2:#242424;--navy3:#2e2e2e;
      --sidebar:230px;
      --font:'Inter',sans-serif;
      --font-hi:'Noto Sans Devanagari','Inter',sans-serif;
      --text:#111;--text2:#444;--text3:#888;
      --border:#e5e5e5;--bg:#f6f6f4;
    }
    body{font-family:var(--font);background:var(--bg);display:flex;min-height:100vh;color:var(--text)}
    .sidebar{width:var(--sidebar);background:var(--navy);min-height:100vh;display:flex;flex-direction:column;flex-shrink:0;position:fixed;top:0;left:0;bottom:0;z-index:100;border-right:3px solid var(--red)}
    .sb-brand{padding:20px 18px 16px;border-bottom:1px solid rgba(255,255,255,.07)}
    .sb-logo{display:flex;align-items:center;gap:10px}
    .sb-icon{width:38px;height:38px;background:var(--red);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .sb-icon i{color:#fff;font-size:16px}
    .sb-name{font-family:var(--font-hi);font-weight:800;font-size:.92rem;color:#fff;line-height:1.2;letter-spacing:.3px}
    .sb-role{font-size:.66rem;color:rgba(255,255,255,.4);font-family:var(--font);letter-spacing:.5px;text-transform:uppercase;margin-top:2px}
    .sb-nav{flex:1;padding:14px 10px}
    .sb-nav a{display:flex;align-items:center;gap:10px;padding:10px 13px;border-radius:7px;color:rgba(255,255,255,.55);font-size:.84rem;font-weight:500;text-decoration:none;transition:.15s;margin-bottom:2px;position:relative}
    .sb-nav a:hover{background:rgba(255,255,255,.06);color:#fff}
    .sb-nav a.active{background:var(--red3);color:#ff6b7a;font-weight:700;border-left:3px solid var(--red);padding-left:10px}
    .sb-nav a i{width:18px;text-align:center;font-size:.83rem;flex-shrink:0}
    .sb-nav-label{font-size:.62rem;text-transform:uppercase;letter-spacing:1.2px;color:rgba(255,255,255,.2);padding:12px 13px 6px;font-weight:600}
    .sb-footer{padding:14px 10px;border-top:1px solid rgba(255,255,255,.07)}
    .sb-footer a{display:flex;align-items:center;gap:10px;padding:9px 13px;border-radius:7px;color:rgba(255,255,255,.35);font-size:.81rem;cursor:pointer;transition:.15s;text-decoration:none}
    .sb-footer a:hover{background:rgba(255,255,255,.05);color:#fff}
    .main-wrap{margin-left:var(--sidebar);flex:1;min-height:100vh;display:flex;flex-direction:column}
    .top-bar{background:#fff;border-bottom:2px solid var(--border);padding:13px 26px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;box-shadow:0 1px 4px rgba(0,0,0,.04)}
    .top-title{font-family:var(--font);font-weight:800;font-size:1rem;color:var(--navy);display:flex;align-items:center;gap:8px}
    .top-title::before{content:'';display:inline-block;width:4px;height:18px;background:var(--red);border-radius:2px}
    .top-right{display:flex;align-items:center;gap:10px}
    .top-badge{background:var(--red3);color:var(--red);font-size:.7rem;font-weight:700;padding:4px 10px;border-radius:20px;border:1px solid rgba(196,30,58,.2)}
    .top-date{font-size:.78rem;color:var(--text3);font-weight:500}
    .page-body{padding:24px 26px;flex:1}
    .card{background:#fff;border:1px solid var(--border);border-radius:10px;overflow:hidden;margin-bottom:18px;box-shadow:0 1px 3px rgba(0,0,0,.03)}
    .card-head{padding:15px 20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;background:#fafafa}
    .card-head h3{font-weight:800;font-size:.88rem;color:var(--navy);display:flex;align-items:center;gap:7px;text-transform:uppercase;letter-spacing:.5px}
    .card-body{padding:20px}
    .btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:7px;font-size:.83rem;font-weight:700;cursor:pointer;border:none;transition:.15s;text-decoration:none;font-family:var(--font)}
    .btn-red{background:var(--red);color:#fff}
    .btn-red:hover{background:var(--red2)}
    .btn-outline{background:transparent;color:var(--text2);border:1.5px solid var(--border)}
    .btn-outline:hover{border-color:var(--red);color:var(--red)}
    .btn-sm{padding:5px 11px;font-size:.77rem}
    .badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:20px;font-size:.7rem;font-weight:700}
    .badge-red{background:#fee2e2;color:#C41E3A}
    .badge-green{background:#dcfce7;color:#16a34a}
    .badge-blue{background:#dbeafe;color:#1d4ed8}
    .badge-amber{background:#fef3c7;color:#92400e}
    .badge-purple{background:#f3e8ff;color:#7c3aed}
    .badge-gray{background:#f1f5f9;color:#475569}
    .form-group{margin-bottom:16px}
    .form-group label{display:block;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#555;margin-bottom:5px}
    .form-group input,.form-group select,.form-group textarea{width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:.9rem;font-family:var(--font);outline:none;transition:.2s;background:#fff;color:var(--text)}
    .form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:var(--red);box-shadow:0 0 0 3px rgba(196,30,58,.07)}
    .form-group .hint{font-size:.73rem;color:var(--text3);margin-top:4px}
    .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px}
    .grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
    .table{width:100%;border-collapse:collapse;font-size:.84rem}
    .table th{padding:10px 14px;text-align:left;font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;color:var(--text3);font-weight:700;border-bottom:2px solid var(--border);background:#fafafa}
    .table td{padding:12px 14px;border-bottom:1px solid var(--border);color:var(--text2);vertical-align:middle}
    .table tr:hover td{background:#fdf6f7}
    .table tr:last-child td{border-bottom:none}
    .empty-state{text-align:center;padding:48px 24px;color:var(--text3)}
    .empty-state i{font-size:2.2rem;display:block;margin-bottom:12px;color:#ddd}
    .empty-state p{font-size:.88rem}
    .hindi{font-family:var(--font-hi)}
    @media(max-width:900px){.sidebar{transform:translateX(-100%)}.main-wrap{margin-left:0}.grid-4{grid-template-columns:1fr 1fr}}
  </style>
  <div class="sidebar">
    <div class="sb-brand">
      <div class="sb-logo">
        <div class="sb-icon"><i class="fa-solid fa-newspaper"></i></div>
        <div>
          <div class="sb-name">जनबोलन्यूज़</div>
          <div class="sb-role">Admin Panel</div>
        </div>
      </div>
    </div>
    <nav class="sb-nav">
      <div class="sb-nav-label">Main</div>
      ${nav.map(n => `<a href="${n.href}" class="${n.href === activePage ? 'active' : ''}"><i class="fa-solid ${n.icon}"></i> <span>${n.label}</span></a>`).join('')}
    </nav>
    <div class="sb-footer">
      <a href="../index.html" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> View Site</a>
      <a onclick="logout()"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
  </div>
  <div class="main-wrap">
    <div class="top-bar">
      <div class="top-title" id="pageTitle">Dashboard</div>
      <div class="top-right">
        <span class="top-date" id="topDate"></span>
        <span class="top-badge"><i class="fa-solid fa-circle" style="font-size:.45rem;margin-right:4px;color:var(--red);"></i> LIVE</span>
        <a href="article-edit.html" class="btn btn-red btn-sm"><i class="fa-solid fa-plus"></i> New Article</a>
      </div>
    </div>
    <div class="page-body" id="pageBody">`;

  document.body.insertAdjacentHTML('afterbegin', html);
  document.body.insertAdjacentHTML('beforeend', '</div></div>');

  // Set date
  const dateEl = document.getElementById('topDate');
  if (dateEl) {
    dateEl.textContent = new Date().toLocaleDateString('en-IN', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
  }
}

// Category list (shared)
const JN_CATEGORIES = [
  { value: 'politics',     label: 'Politics',     labelHi: 'राजनीति' },
  { value: 'national',     label: 'National',     labelHi: 'राष्ट्रीय' },
  { value: 'international',label: 'International',labelHi: 'अंतरराष्ट्रीय' },
  { value: 'sports',       label: 'Sports',       labelHi: 'खेल' },
  { value: 'entertainment',label: 'Entertainment',labelHi: 'मनोरंजन' },
  { value: 'business',     label: 'Business',     labelHi: 'व्यापार' },
  { value: 'technology',   label: 'Technology',   labelHi: 'तकनीक' },
  { value: 'crime',        label: 'Crime',        labelHi: 'अपराध' },
  { value: 'health',       label: 'Health',       labelHi: 'स्वास्थ्य' },
  { value: 'education',    label: 'Education',    labelHi: 'शिक्षा' },
  { value: 'local',        label: 'Local',        labelHi: 'स्थानीय' },
  { value: 'religion',     label: 'Religion',     labelHi: 'धर्म' },
];

function categoryLabel(val, hi = false) {
  const cat = JN_CATEGORIES.find(c => c.value === val);
  if (!cat) return val || '—';
  return hi ? cat.labelHi : cat.label;
}

function statusBadge(status) {
  const map = {
    published: '<span class="badge badge-green">Published</span>',
    draft:     '<span class="badge badge-gray">Draft</span>',
    scheduled: '<span class="badge badge-blue">Scheduled</span>',
  };
  return map[status] || `<span class="badge badge-gray">${status}</span>`;
}

function langBadge(lang) {
  const map = {
    hi:   '<span class="badge badge-amber">हिंदी</span>',
    en:   '<span class="badge badge-blue">English</span>',
    both: '<span class="badge badge-purple">Both</span>',
  };
  return map[lang] || `<span class="badge badge-gray">${lang}</span>`;
}

function catBadge(cat) {
  return `<span class="badge badge-red">${categoryLabel(cat)}</span>`;
}
