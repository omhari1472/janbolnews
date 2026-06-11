/**
 * Janbol News — Site Configuration
 * API base, language, and global constants
 */
(function(window) {
  'use strict';

  /* ── API ── */
  window.API_BASE = '/api/news';

  /* ── Site Config ── */
  window.JN_CONFIG = {
    siteName: 'Janbol News',
    siteNameHi: 'जनबोल न्यूज़',
    siteTagline: 'आपकी आवाज़, आपकी खबर',
    siteTaglineEn: 'Your Voice, Your News',
    siteUrl: window.location.origin,

    defaultLang: 'hi',  // 'hi' | 'en'
    langKey: 'jn_lang',

    // Categories (slug → display name in both languages)
    categories: [
      { slug: 'politics',      hi: 'राजनीति',       en: 'Politics'       },
      { slug: 'sports',        hi: 'खेल',           en: 'Sports'         },
      { slug: 'entertainment', hi: 'मनोरंजन',       en: 'Entertainment'  },
      { slug: 'business',      hi: 'व्यापार',       en: 'Business'       },
      { slug: 'technology',    hi: 'तकनीक',         en: 'Technology'     },
      { slug: 'state',         hi: 'राज्य',         en: 'State'          },
      { slug: 'world',         hi: 'विदेश',         en: 'World'          },
      { slug: 'health',        hi: 'स्वास्थ्य',     en: 'Health'         },
    ],

    // Editions / Cities
    editions: [
      { slug: 'delhi',    hi: 'दिल्ली',     en: 'Delhi'    },
      { slug: 'mumbai',   hi: 'मुंबई',      en: 'Mumbai'   },
      { slug: 'lucknow',  hi: 'लखनऊ',      en: 'Lucknow'  },
      { slug: 'patna',    hi: 'पटना',       en: 'Patna'    },
      { slug: 'jaipur',   hi: 'जयपुर',      en: 'Jaipur'   },
      { slug: 'bhopal',   hi: 'भोपाल',      en: 'Bhopal'   },
      { slug: 'varanasi', hi: 'वाराणसी',    en: 'Varanasi' },
    ],

    // Social
    social: {
      facebook:  'https://facebook.com/janbolnews',
      twitter:   'https://twitter.com/janbolnews',
      instagram: 'https://instagram.com/janbolnews',
      youtube:   'https://youtube.com/janbolnews',
      telegram:  'https://t.me/janbolnews',
    },

    // Pagination
    articlesPerPage: 12,
    tickerRefreshMs: 60000,   // 1 minute
    viewIncrementDelay: 5000, // 5 seconds after load

    // Image fallback
    imgFallback: '/img/placeholder.svg',
    epaperimgFallback: '/img/epaper-placeholder.svg',
  };

  /* ── Language Helpers ── */

  /**
   * Get current language from localStorage (defaults to 'hi')
   */
  window.getLang = function() {
    return localStorage.getItem(window.JN_CONFIG.langKey) || window.JN_CONFIG.defaultLang;
  };

  /**
   * Set language and optionally reload
   */
  window.setLang = function(lang, reload) {
    if (lang !== 'hi' && lang !== 'en') return;
    localStorage.setItem(window.JN_CONFIG.langKey, lang);
    if (reload !== false) {
      window.location.reload();
    }
  };

  /**
   * Get localized text from an article/object
   * Tries obj.title_hi / obj.title_en etc, falls back gracefully
   */
  window.getLocalText = function(obj, field) {
    if (!obj) return '';
    const lang = getLang();
    const hiKey  = field + '_hi';
    const enKey  = field + '_en';
    if (lang === 'hi') {
      return obj[hiKey] || obj[field] || obj[enKey] || '';
    } else {
      return obj[enKey] || obj[field] || obj[hiKey] || '';
    }
  };

  /**
   * Get category display name by slug
   */
  window.getCategoryName = function(slug) {
    const cat = window.JN_CONFIG.categories.find(c => c.slug === slug);
    if (!cat) return slug;
    return getLang() === 'hi' ? cat.hi : cat.en;
  };

  /* ── Apply language class on load ── */
  document.addEventListener('DOMContentLoaded', function() {
    const lang = getLang();
    if (lang === 'hi') {
      document.body.classList.add('lang-hi');
    } else {
      document.body.classList.remove('lang-hi');
    }
    // Update lang toggle buttons
    document.querySelectorAll('.lang-btn').forEach(function(btn) {
      btn.classList.toggle('active', btn.dataset.lang === lang);
    });
  });

})(window);
