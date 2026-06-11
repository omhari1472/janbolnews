/**
 * Janbolnews — Self-Promo Ads for Janbol Business Solutions
 * Injects HTML/CSS ads into all .ad-slot placeholders
 * Replace these with AdSense code when ads are sold / approved
 */
(function () {
  'use strict';

  /* ── Brand tokens ── */
  var BRAND = {
    navy:    '#0B2167',
    navyDk:  '#07164A',
    orange:  '#FF6B2C',
    orangeLt:'#FF8A50',
    white:   '#FFFFFF',
    offWhite:'#F0F4FF',
    url:     'https://janbol.com',
    utm:     '?utm_source=janbolnews&utm_medium=banner&utm_campaign=self_promo',
  };

  function href() { return BRAND.url + BRAND.utm; }

  /* ════════════════════════════════════════════════
     AD TEMPLATES
  ════════════════════════════════════════════════ */

  /* ── Leaderboard 728×90 ── */
  function adLeaderboard(variant) {
    var msgs = [
      { head: 'Register Your Company in 12 Days', sub: '₹0 Hidden Charges &nbsp;·&nbsp; 1,000+ Clients &nbsp;·&nbsp; 28+ States' },
      { head: 'GST • Company Reg • Branding • Incubation', sub: 'All-in-one Business Launch — Fully Online, No Office Visits' },
    ];
    var m = msgs[(variant || 0) % msgs.length];
    return `
    <a href="${href()}" target="_blank" rel="noopener sponsored" title="Janbol Business Solutions" style="
      display:flex;align-items:center;justify-content:space-between;
      width:100%;height:90px;
      background:linear-gradient(105deg,${BRAND.navyDk} 0%,${BRAND.navy} 55%,#1A3FA8 100%);
      border-radius:6px;overflow:hidden;text-decoration:none;
      padding:0 22px;gap:16px;box-sizing:border-box;position:relative;
      font-family:'Inter','Noto Sans Devanagari',sans-serif;
    ">
      <!-- Decorative circles -->
      <span style="position:absolute;right:-20px;top:-30px;width:130px;height:130px;border-radius:50%;background:rgba(255,107,44,.12);pointer-events:none;"></span>
      <span style="position:absolute;right:90px;bottom:-40px;width:100px;height:100px;border-radius:50%;background:rgba(255,107,44,.07);pointer-events:none;"></span>

      <!-- Logo mark -->
      <div style="flex-shrink:0;display:flex;align-items:center;gap:10px;">
        <div style="
          width:42px;height:42px;border-radius:10px;
          background:${BRAND.orange};
          display:flex;align-items:center;justify-content:center;
          font-weight:900;font-size:20px;color:#fff;letter-spacing:-1px;
          box-shadow:0 4px 12px rgba(255,107,44,.4);flex-shrink:0;
        ">J</div>
        <div>
          <div style="color:${BRAND.white};font-weight:800;font-size:13px;line-height:1.1;white-space:nowrap;">Janbol Business</div>
          <div style="color:rgba(255,255,255,.5);font-size:9px;letter-spacing:.6px;text-transform:uppercase;">Solutions Pvt Ltd</div>
        </div>
      </div>

      <!-- Message -->
      <div style="flex:1;min-width:0;text-align:center;">
        <div style="color:${BRAND.white};font-weight:700;font-size:14px;line-height:1.25;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${m.head}</div>
        <div style="color:rgba(255,255,255,.65);font-size:11px;margin-top:3px;white-space:nowrap;">${m.sub}</div>
      </div>

      <!-- CTA -->
      <div style="
        flex-shrink:0;
        background:${BRAND.orange};
        color:#fff;font-weight:800;font-size:12px;
        padding:10px 18px;border-radius:7px;white-space:nowrap;
        box-shadow:0 4px 14px rgba(255,107,44,.4);
        letter-spacing:.2px;
      ">Book Free Consult →</div>
    </a>`;
  }

  /* ── Square 300×250 ── */
  function adSquare() {
    return `
    <a href="${href()}" target="_blank" rel="noopener sponsored" title="Janbol Business Solutions" style="
      display:flex;flex-direction:column;align-items:center;justify-content:space-between;
      width:100%;height:250px;
      background:linear-gradient(145deg,${BRAND.navyDk} 0%,${BRAND.navy} 60%,#1A3FA8 100%);
      border-radius:8px;overflow:hidden;text-decoration:none;
      padding:22px 20px 18px;box-sizing:border-box;position:relative;
      font-family:'Inter','Noto Sans Devanagari',sans-serif;
    ">
      <!-- Decorative -->
      <span style="position:absolute;right:-25px;top:-25px;width:120px;height:120px;border-radius:50%;background:rgba(255,107,44,.1);pointer-events:none;"></span>
      <span style="position:absolute;left:-20px;bottom:-20px;width:90px;height:90px;border-radius:50%;background:rgba(255,107,44,.07);pointer-events:none;"></span>

      <!-- Top: Logo -->
      <div style="display:flex;align-items:center;gap:9px;align-self:flex-start;position:relative;z-index:1;">
        <div style="
          width:34px;height:34px;border-radius:8px;
          background:${BRAND.orange};
          display:flex;align-items:center;justify-content:center;
          font-weight:900;font-size:16px;color:#fff;
          box-shadow:0 3px 10px rgba(255,107,44,.4);flex-shrink:0;
        ">J</div>
        <div>
          <div style="color:${BRAND.white};font-weight:800;font-size:12px;line-height:1.1;">Janbol Business</div>
          <div style="color:rgba(255,255,255,.45);font-size:8.5px;letter-spacing:.6px;text-transform:uppercase;">Solutions Pvt Ltd</div>
        </div>
      </div>

      <!-- Mid: Headline -->
      <div style="text-align:center;position:relative;z-index:1;">
        <div style="color:${BRAND.orange};font-size:11px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;margin-bottom:6px;">Building India's Ventures</div>
        <div style="color:${BRAND.white};font-weight:800;font-size:18px;line-height:1.25;">Start Your Business<br>in 12 Days</div>
        <div style="color:rgba(255,255,255,.6);font-size:11px;margin-top:8px;line-height:1.5;">
          ✓ &nbsp;Company Registration<br>
          ✓ &nbsp;GST &amp; Compliance<br>
          ✓ &nbsp;Branding &amp; Bank Account
        </div>
      </div>

      <!-- Stats strip -->
      <div style="
        display:flex;gap:10px;width:100%;
        background:rgba(255,255,255,.06);border-radius:7px;
        padding:8px 10px;box-sizing:border-box;position:relative;z-index:1;
      ">
        <div style="flex:1;text-align:center;">
          <div style="color:${BRAND.orange};font-weight:800;font-size:14px;">1000+</div>
          <div style="color:rgba(255,255,255,.5);font-size:9px;">Clients</div>
        </div>
        <div style="width:1px;background:rgba(255,255,255,.1);"></div>
        <div style="flex:1;text-align:center;">
          <div style="color:${BRAND.orange};font-weight:800;font-size:14px;">28+</div>
          <div style="color:rgba(255,255,255,.5);font-size:9px;">States</div>
        </div>
        <div style="width:1px;background:rgba(255,255,255,.1);"></div>
        <div style="flex:1;text-align:center;">
          <div style="color:${BRAND.orange};font-weight:800;font-size:14px;">₹0</div>
          <div style="color:rgba(255,255,255,.5);font-size:9px;">Hidden Fee</div>
        </div>
      </div>

      <!-- CTA -->
      <div style="
        width:100%;text-align:center;
        background:${BRAND.orange};
        color:#fff;font-weight:800;font-size:13px;
        padding:11px;border-radius:7px;
        box-shadow:0 4px 14px rgba(255,107,44,.35);
        position:relative;z-index:1;
      ">Book Free Consultation →</div>
    </a>`;
  }

  /* ── Rectangle / Tall 300×600 ── */
  function adRect() {
    var services = [
      { icon: '🏢', label: 'Company Registration' },
      { icon: '📋', label: 'GST & FSSAI Filing' },
      { icon: '🎨', label: 'Brand Kit & Logo' },
      { icon: '🚀', label: 'Incubation Program' },
      { icon: '🏦', label: 'Bank Account Setup' },
      { icon: '🌾', label: 'Agri & FPO Support' },
    ];
    return `
    <a href="${href()}" target="_blank" rel="noopener sponsored" title="Janbol Business Solutions" style="
      display:flex;flex-direction:column;align-items:center;
      width:100%;min-height:600px;
      background:linear-gradient(160deg,${BRAND.navyDk} 0%,${BRAND.navy} 50%,#1A3FA8 100%);
      border-radius:10px;overflow:hidden;text-decoration:none;
      padding:26px 20px 22px;box-sizing:border-box;position:relative;gap:18px;
      font-family:'Inter','Noto Sans Devanagari',sans-serif;
    ">
      <!-- Decorative circles -->
      <span style="position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,107,44,.09);pointer-events:none;"></span>
      <span style="position:absolute;left:-40px;top:40%;width:130px;height:130px;border-radius:50%;background:rgba(255,107,44,.06);pointer-events:none;"></span>
      <span style="position:absolute;right:-20px;bottom:80px;width:100px;height:100px;border-radius:50%;background:rgba(255,107,44,.08);pointer-events:none;"></span>

      <!-- Logo -->
      <div style="display:flex;align-items:center;gap:10px;align-self:flex-start;position:relative;z-index:1;">
        <div style="
          width:44px;height:44px;border-radius:11px;
          background:${BRAND.orange};
          display:flex;align-items:center;justify-content:center;
          font-weight:900;font-size:22px;color:#fff;
          box-shadow:0 4px 14px rgba(255,107,44,.4);flex-shrink:0;
        ">J</div>
        <div>
          <div style="color:${BRAND.white};font-weight:800;font-size:14px;line-height:1.15;">Janbol Business</div>
          <div style="color:rgba(255,255,255,.45);font-size:9px;letter-spacing:.6px;text-transform:uppercase;">Solutions Pvt Ltd</div>
        </div>
      </div>

      <!-- Hero headline -->
      <div style="text-align:center;position:relative;z-index:1;">
        <div style="color:${BRAND.orange};font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;margin-bottom:8px;">Building India's Venture Ecosystem</div>
        <div style="color:${BRAND.white};font-weight:900;font-size:24px;line-height:1.2;">Launch Your<br>Business Today</div>
        <div style="
          display:inline-flex;align-items:center;gap:6px;
          background:rgba(255,107,44,.15);border:1px solid rgba(255,107,44,.3);
          border-radius:20px;padding:5px 14px;margin-top:10px;
        ">
          <span style="width:6px;height:6px;border-radius:50%;background:${BRAND.orange};display:inline-block;"></span>
          <span style="color:${BRAND.orangeLt};font-size:11px;font-weight:700;">Avg. 12 Days to Incorporation</span>
        </div>
      </div>

      <!-- Divider -->
      <div style="width:100%;height:1px;background:rgba(255,255,255,.1);position:relative;z-index:1;"></div>

      <!-- Services list -->
      <div style="width:100%;position:relative;z-index:1;">
        <div style="color:rgba(255,255,255,.4);font-size:9.5px;letter-spacing:.7px;text-transform:uppercase;margin-bottom:10px;font-weight:600;">Our Services</div>
        <div style="display:flex;flex-direction:column;gap:7px;">
          ${services.map(function(s) { return `
          <div style="
            display:flex;align-items:center;gap:10px;
            background:rgba(255,255,255,.05);border-radius:7px;
            padding:9px 12px;
          ">
            <span style="font-size:15px;">${s.icon}</span>
            <span style="color:rgba(255,255,255,.85);font-size:12.5px;font-weight:500;">${s.label}</span>
            <span style="margin-left:auto;color:rgba(255,255,255,.25);font-size:11px;">→</span>
          </div>`; }).join('')}
        </div>
      </div>

      <!-- Stats row -->
      <div style="
        display:flex;gap:0;width:100%;
        background:rgba(255,255,255,.07);border-radius:8px;
        overflow:hidden;position:relative;z-index:1;
      ">
        <div style="flex:1;padding:12px 8px;text-align:center;">
          <div style="color:${BRAND.orange};font-weight:900;font-size:18px;">1000+</div>
          <div style="color:rgba(255,255,255,.45);font-size:9px;margin-top:2px;">Clients</div>
        </div>
        <div style="width:1px;background:rgba(255,255,255,.08);"></div>
        <div style="flex:1;padding:12px 8px;text-align:center;">
          <div style="color:${BRAND.orange};font-weight:900;font-size:18px;">28+</div>
          <div style="color:rgba(255,255,255,.45);font-size:9px;margin-top:2px;">States</div>
        </div>
        <div style="width:1px;background:rgba(255,255,255,.08);"></div>
        <div style="flex:1;padding:12px 8px;text-align:center;">
          <div style="color:${BRAND.orange};font-weight:900;font-size:18px;">₹0</div>
          <div style="color:rgba(255,255,255,.45);font-size:9px;margin-top:2px;">Hidden Fee</div>
        </div>
      </div>

      <!-- Guarantee badge -->
      <div style="
        width:100%;text-align:center;
        background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);
        border-radius:8px;padding:11px;position:relative;z-index:1;
      ">
        <div style="color:rgba(255,255,255,.9);font-size:11.5px;font-weight:600;">
          🛡️ &nbsp;6 Months Free Post-Service Support
        </div>
        <div style="color:rgba(255,255,255,.4);font-size:10px;margin-top:3px;">Written guarantee • No hidden charges</div>
      </div>

      <!-- CTA -->
      <div style="
        width:100%;text-align:center;
        background:${BRAND.orange};
        color:#fff;font-weight:800;font-size:14px;
        padding:14px;border-radius:8px;
        box-shadow:0 6px 20px rgba(255,107,44,.4);
        position:relative;z-index:1;
        letter-spacing:.2px;
      ">Book Free Consultation →</div>

      <!-- URL -->
      <div style="color:rgba(255,255,255,.3);font-size:10px;letter-spacing:.3px;position:relative;z-index:1;">janbol.com</div>
    </a>`;
  }

  /* ════════════════════════════════════════════════
     INJECT INTO SLOTS
  ════════════════════════════════════════════════ */
  function inject() {
    var slots = document.querySelectorAll('.ad-slot');
    var leaderboardCount = 0;

    slots.forEach(function (slot) {
      var html = '';

      if (slot.classList.contains('ad-slot-rect')) {
        html = adRect();
      } else if (slot.classList.contains('ad-slot-square')) {
        html = adSquare();
      } else if (slot.classList.contains('ad-slot-leaderboard') || slot.classList.contains('ad-slot-banner')) {
        html = adLeaderboard(leaderboardCount++);
      }

      if (html) {
        slot.style.background = 'transparent';
        slot.style.border = 'none';
        slot.innerHTML = html;
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inject);
  } else {
    inject();
  }

})();
