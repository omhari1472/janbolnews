<?php
// ── Janbolnews — Site Config ─────────────────────────────────────────
define('SITE_NAME',   'Janbolnews');
define('SITE_URL',    '');  // e.g. https://janbolnews.com — leave empty for auto-detect

// Admin API key — change before deploying
define('ADMIN_KEY',   'janbolnews-admin-2026');

// Database
define('DB_PATH',     __DIR__ . '/db/janbolnews.sqlite');

// Upload directories (relative to api/)
define('UPLOAD_DIR',  __DIR__ . '/uploads/');
define('UPLOAD_URL',  '');  // leave empty — storageUrl() auto-detects from SERVER vars

// Sub-dirs
define('IMG_DIR',     UPLOAD_DIR . 'images/');
define('EPAPER_DIR',  UPLOAD_DIR . 'epapers/');
define('MEDIA_DIR',   UPLOAD_DIR . 'media/');

// Upload limits
define('MAX_IMG_SIZE',    5 * 1024 * 1024);   // 5 MB
define('MAX_PDF_SIZE',   30 * 1024 * 1024);   // 30 MB

// Pagination default
define('DEFAULT_LIMIT', 20);

// CORS
define('ALLOWED_ORIGIN', '*');
