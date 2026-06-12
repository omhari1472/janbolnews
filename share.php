<?php
/**
 * Janbol News — Article Share Proxy
 * Generates proper OG/Twitter meta tags for WhatsApp/Telegram/Twitter previews.
 * Usage: /share.php?slug=article-slug (used internally by .htaccess for crawlers)
 */

$slug = trim($_GET['slug'] ?? '');
if (!$slug) { header('Location: /'); exit; }

// Fetch article from Laravel API (internal request)
$apiUrl = 'http://localhost/api/news/articles/' . urlencode($slug);
$ctx    = stream_context_create(['http' => ['timeout' => 5, 'ignore_errors' => true]]);
$json   = @file_get_contents($apiUrl, false, $ctx);
$data   = $json ? json_decode($json, true) : null;
$art    = $data['data'] ?? null;

// Defaults
$siteUrl  = 'https://janbolnews.com';
$siteName = 'जनबोल न्यूज़ | Janbol News';
$siteLogo = $siteUrl . '/img/logo.png';

if ($art) {
    $title   = htmlspecialchars($art['title_hi'] ?: ($art['title_en'] ?? $siteName));
    $desc    = htmlspecialchars($art['excerpt_hi'] ?: ($art['excerpt_en'] ?? 'जनबोल न्यूज़ — आपकी आवाज़, आपकी खबर'));
    $img     = htmlspecialchars($art['featured_image'] ?? $siteLogo);
    $url     = $siteUrl . '/article.html?slug=' . urlencode($slug);
    $catName = htmlspecialchars($art['category_name'] ?? '');
    $author  = htmlspecialchars($art['author_name'] ?? 'Janbol News');
} else {
    // Article not found — redirect
    header('Location: /article.html?slug=' . urlencode($slug));
    exit;
}
?>
<!DOCTYPE html>
<html lang="hi" prefix="og: https://ogp.me/ns#">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Primary -->
  <title><?= $title ?> | जनबोल न्यूज़</title>
  <meta name="description" content="<?= $desc ?>">
  <meta name="author" content="<?= $author ?>">

  <!-- Open Graph (WhatsApp, Facebook, Telegram) -->
  <meta property="og:type"        content="article">
  <meta property="og:title"       content="<?= $title ?>">
  <meta property="og:description" content="<?= $desc ?>">
  <meta property="og:image"       content="<?= $img ?>">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:url"         content="<?= htmlspecialchars($url) ?>">
  <meta property="og:site_name"   content="जनबोल न्यूज़">
  <meta property="og:locale"      content="hi_IN">
  <?php if ($catName): ?>
  <meta property="article:section" content="<?= $catName ?>">
  <?php endif; ?>

  <!-- Twitter Card -->
  <meta name="twitter:card"        content="summary_large_image">
  <meta name="twitter:site"        content="@janbolnews">
  <meta name="twitter:title"       content="<?= $title ?>">
  <meta name="twitter:description" content="<?= $desc ?>">
  <meta name="twitter:image"       content="<?= $img ?>">

  <!-- Redirect to actual article page immediately -->
  <link rel="canonical" href="<?= htmlspecialchars($url) ?>">
  <meta http-equiv="refresh" content="0;url=<?= htmlspecialchars($url) ?>">
</head>
<body>
  <p>लोड हो रहा है… <a href="<?= htmlspecialchars($url) ?>">यहाँ क्लिक करें</a></p>
  <script>window.location.replace('<?= addslashes($url) ?>');</script>
</body>
</html>
