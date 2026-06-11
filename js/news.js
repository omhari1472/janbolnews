/**
 * Janbol News — NewsAPI Class + Rendering Utilities
 * All API calls go through this module
 */
(function(window) {
  'use strict';

  /* ═══════════════════════════════════════
     NewsAPI — API wrapper class
  ═══════════════════════════════════════ */
  class NewsAPI {
    constructor(base) {
      this.base = base || window.API_BASE || '/api/public';
    }

    /**
     * Internal fetch helper with error handling
     */
    async _get(path, params) {
      // Build base: on localhost Laravel runs on port 8000, production uses /api
      const isLocal = ['localhost','127.0.0.1'].includes(window.location.hostname);
      const base = isLocal ? 'http://localhost:8000/api/news' : (this.base || '/api/news');
      const parts = path.replace(/^\//, '').split('/');
      const resource = parts[0];
      const slug     = parts[1];
      // Slug goes into the URL path (not query param) — Laravel uses route params
      const filePath = slug ? base + '/' + resource + '/' + encodeURIComponent(decodeURIComponent(slug)) : base + '/' + resource;
      const url = new URL(filePath, isLocal ? 'http://localhost:8000' : window.location.origin);
      if (params) {
        Object.entries(params).forEach(([k, v]) => {
          if (v !== undefined && v !== null && v !== '') {
            url.searchParams.set(k, v);
          }
        });
      }
      const res = await fetch(url.toString(), {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      });
      if (!res.ok) {
        const err = new Error('HTTP ' + res.status);
        err.status = res.status;
        throw err;
      }
      return res.json();
    }

    /**
     * Fetch breaking news headlines (latest ~10)
     */
    async fetchBreaking() {
      return this._get('/breaking');
    }

    /**
     * Fetch articles with filters
     * @param {Object} params - { category, lang, page, limit, featured }
     */
    async fetchArticles(params) {
      return this._get('/articles', params);
    }

    /**
     * Fetch a single article by slug
     */
    async fetchArticle(slug) {
      return this._get('/articles/' + encodeURIComponent(slug));
    }

    /**
     * Fetch all categories with counts
     */
    async fetchCategories() {
      return this._get('/categories');
    }

    /**
     * Search articles
     * @param {string} q - search query
     * @param {Object} params - { lang, category, page, limit }
     */
    async fetchSearch(q, params) {
      return this._get('/search', { q, ...params });
    }

    /**
     * Fetch e-papers list
     * @param {Object} params - { edition, page, limit }
     */
    async fetchEpapers(params) {
      return this._get('/epapers', params);
    }

    /**
     * Increment view count for an article (fire-and-forget)
     */
    incrementView(slug) {
      fetch(this.base + '/articles/' + encodeURIComponent(slug) + '/view', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
      }).catch(() => {}); // silently ignore
    }
  }

  /* Singleton */
  window.newsAPI = new NewsAPI();

  /* ═══════════════════════════════════════
     DATE FORMATTING
  ═══════════════════════════════════════ */

  /**
   * Format a date string into localized relative or absolute
   * @param {string|Date} dateStr
   * @returns {string}
   */
  window.formatDate = function(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    if (isNaN(date)) return '';
    const now  = new Date();
    const diff = now - date; // ms
    const mins  = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days  = Math.floor(diff / 86400000);

    const lang = getLang();

    if (mins < 2) return lang === 'hi' ? 'अभी' : 'Just now';
    if (mins < 60) return lang === 'hi' ? `${mins} मिनट पहले` : `${mins}m ago`;
    if (hours < 24) return lang === 'hi' ? `${hours} घंटे पहले` : `${hours}h ago`;
    if (days < 7)   return lang === 'hi' ? `${days} दिन पहले` : `${days}d ago`;

    // Absolute date
    const opts = { day: 'numeric', month: 'long', year: 'numeric' };
    const locale = lang === 'hi' ? 'hi-IN' : 'en-IN';
    return date.toLocaleDateString(locale, opts);
  };

  /**
   * Format number with locale (e.g. views)
   */
  window.formatNumber = function(n) {
    if (!n) return '0';
    const lang = getLang();
    if (lang === 'hi') {
      if (n >= 10000000) return (n/10000000).toFixed(1) + ' कर.';
      if (n >= 100000)   return (n/100000).toFixed(1) + ' लाख';
      if (n >= 1000)     return (n/1000).toFixed(1) + ' हज.';
      return n.toString();
    }
    if (n >= 1000000) return (n/1000000).toFixed(1) + 'M';
    if (n >= 1000)    return (n/1000).toFixed(1) + 'K';
    return n.toString();
  };

  /* ═══════════════════════════════════════
     LANGUAGE TOGGLE HANDLER
  ═══════════════════════════════════════ */
  window.languageToggle = function(lang) {
    setLang(lang);
  };

  /* ═══════════════════════════════════════
     IMAGE HELPER
  ═══════════════════════════════════════ */
  function safeImg(src) {
    return src || window.JN_CONFIG.imgFallback;
  }

  function imgWithFallback(src, alt, cssClass) {
    const imgEl = document.createElement('img');
    imgEl.src   = safeImg(src);
    imgEl.alt   = alt || '';
    if (cssClass) imgEl.className = cssClass;
    imgEl.loading = 'lazy';
    imgEl.onerror = function() {
      this.onerror = null;
      this.src = window.JN_CONFIG.imgFallback;
    };
    return imgEl;
  }

  /* ═══════════════════════════════════════
     RENDER ARTICLE CARD
  ═══════════════════════════════════════ */

  /**
   * Render a full article card DOM element
   * @param {Object} article
   * @param {Object} opts - { size: 'sm'|'md'|'lg'|'xl', showExcerpt: bool, horizontal: bool }
   * @returns {HTMLElement}
   */
  window.renderArticleCard = function(article, opts) {
    opts = opts || {};
    const size        = opts.size || 'md';
    const showExcerpt = opts.showExcerpt !== false;
    const horizontal  = opts.horizontal || false;

    const title    = getLocalText(article, 'title')   || article.title || '';
    const excerpt  = getLocalText(article, 'excerpt') || article.excerpt || '';
    const category = getLocalText(article, 'category_name') || getCategoryName(article.category_slug) || article.category_name || '';
    const date     = formatDate(article.published_at || article.created_at);
    const author   = article.author_name || '';
    const views    = article.views || article.view_count || 0;
    const slug     = article.slug || '';
    const img      = article.featured_image || article.image || '';
    const href     = '/article.html?slug=' + encodeURIComponent(slug);

    const card = document.createElement('a');
    card.href  = href;
    card.className = 'article-card card-' + size + (horizontal ? ' card-horizontal' : '');
    card.setAttribute('aria-label', title);

    // Image
    const imgWrap = document.createElement('div');
    imgWrap.className = 'article-card-img';
    if (img) {
      imgWrap.appendChild(imgWithFallback(img, title));
    } else {
      const ph = document.createElement('div');
      ph.className = 'img-placeholder';
      ph.textContent = '📰';
      imgWrap.appendChild(ph);
    }
    if (category) {
      const badge = document.createElement('span');
      badge.className = 'card-category-badge hi-text';
      badge.textContent = category;
      imgWrap.appendChild(badge);
    }

    // Body
    const body = document.createElement('div');
    body.className = 'article-card-body';

    const headlineEl = document.createElement('h3');
    headlineEl.className = 'article-card-headline hi-text truncate-3';
    headlineEl.textContent = title;
    body.appendChild(headlineEl);

    if (showExcerpt && excerpt && !horizontal) {
      const excerptEl = document.createElement('p');
      excerptEl.className = 'article-card-excerpt hi-text truncate-2';
      excerptEl.textContent = excerpt;
      body.appendChild(excerptEl);
    }

    // Meta
    const meta = document.createElement('div');
    meta.className = 'article-card-meta';

    if (date) {
      const dateSpan = document.createElement('span');
      dateSpan.className = 'meta-date';
      dateSpan.innerHTML = '🕐 ' + date;
      meta.appendChild(dateSpan);
    }

    if (author) {
      const authorSpan = document.createElement('span');
      authorSpan.className = 'meta-author hi-text';
      authorSpan.innerHTML = '✍ ' + author;
      meta.appendChild(authorSpan);
    }

    if (views) {
      const viewSpan = document.createElement('span');
      viewSpan.className = 'meta-views';
      viewSpan.innerHTML = '👁 ' + formatNumber(views);
      meta.appendChild(viewSpan);
    }

    body.appendChild(meta);

    card.appendChild(imgWrap);
    card.appendChild(body);

    return card;
  };

  /* ═══════════════════════════════════════
     RENDER LIST ITEM
  ═══════════════════════════════════════ */
  window.renderArticleListItem = function(article, rank) {
    const title = getLocalText(article, 'title') || article.title || '';
    const date  = formatDate(article.published_at || article.created_at);
    const slug  = article.slug || '';
    const img   = article.featured_image || article.image || '';
    const href  = '/article.html?slug=' + encodeURIComponent(slug);

    if (rank !== undefined) {
      const item = document.createElement('a');
      item.href = href;
      item.className = 'ranked-item';

      const numEl = document.createElement('span');
      numEl.className = 'ranked-num en-serif';
      numEl.textContent = String(rank + 1).padStart(2, '0');

      const content = document.createElement('div');
      content.className = 'article-list-content';
      const h = document.createElement('div');
      h.className = 'article-list-headline hi-text truncate-3';
      h.textContent = title;
      const m = document.createElement('div');
      m.className = 'article-list-meta';
      m.textContent = date;
      content.appendChild(h);
      content.appendChild(m);

      item.appendChild(numEl);
      item.appendChild(content);
      return item;
    }

    const item = document.createElement('a');
    item.href = href;
    item.className = 'article-list-item';

    const imgDiv = document.createElement('div');
    imgDiv.className = 'article-list-img';
    if (img) {
      imgDiv.appendChild(imgWithFallback(img, title));
    }

    const content = document.createElement('div');
    content.className = 'article-list-content';
    const h = document.createElement('div');
    h.className = 'article-list-headline hi-text truncate-3';
    h.textContent = title;
    const m = document.createElement('div');
    m.className = 'article-list-meta';
    m.textContent = date;
    content.appendChild(h);
    content.appendChild(m);

    item.appendChild(imgDiv);
    item.appendChild(content);
    return item;
  };

  /* ═══════════════════════════════════════
     SKELETON LOADER
  ═══════════════════════════════════════ */
  window.renderSkeleton = function(count, imgHeight) {
    const frag = document.createDocumentFragment();
    for (let i = 0; i < (count || 4); i++) {
      const card = document.createElement('div');
      card.className = 'skeleton-card fade-in-up fade-in-up-delay-' + Math.min(i+1,4);

      const img = document.createElement('div');
      img.className = 'skeleton skeleton-img';
      img.style.height = (imgHeight || 180) + 'px';

      const body = document.createElement('div');
      body.className = 'skeleton-body';

      ['h-tall w-80', 'w-80', 'w-60', 'w-40'].forEach(function(cls) {
        const line = document.createElement('div');
        line.className = 'skeleton skeleton-line ' + cls;
        body.appendChild(line);
      });

      card.appendChild(img);
      card.appendChild(body);
      frag.appendChild(card);
    }
    return frag;
  };

  /* ═══════════════════════════════════════
     TOAST
  ═══════════════════════════════════════ */
  window.showToast = function(msg, type, duration) {
    let container = document.getElementById('toast-container');
    if (!container) {
      container = document.createElement('div');
      container.className = 'toast-container';
      container.id = 'toast-container';
      document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = 'toast hi-text ' + (type || '');
    toast.textContent = msg;
    container.appendChild(toast);

    requestAnimationFrame(function() {
      toast.classList.add('show');
    });

    setTimeout(function() {
      toast.classList.remove('show');
      setTimeout(function() { toast.remove(); }, 300);
    }, duration || 3000);
  };

  /* ═══════════════════════════════════════
     JSON-LD SCHEMA HELPER
  ═══════════════════════════════════════ */
  window.injectArticleSchema = function(article) {
    const schema = {
      '@context': 'https://schema.org',
      '@type': 'NewsArticle',
      headline:   article.title || '',
      description: article.excerpt || '',
      image:       article.featured_image ? [article.featured_image] : [],
      datePublished: article.published_at || article.created_at || '',
      dateModified:  article.updated_at   || article.published_at || '',
      author: [{
        '@type': 'Person',
        name: article.author_name || 'Janbol News',
      }],
      publisher: {
        '@type': 'Organization',
        name: 'Janbol News',
        logo: {
          '@type': 'ImageObject',
          url: window.location.origin + '/img/logo.png',
        },
      },
      mainEntityOfPage: {
        '@type': 'WebPage',
        '@id': window.location.href,
      },
    };

    const existing = document.getElementById('article-schema');
    if (existing) existing.remove();

    const script = document.createElement('script');
    script.type = 'application/ld+json';
    script.id   = 'article-schema';
    script.textContent = JSON.stringify(schema);
    document.head.appendChild(script);
  };

  // Expose to global scope
  window.NewsAPI = NewsAPI;

})(window);
