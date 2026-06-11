<?php
// ── Janbolnews Public — Videos ───────────────────────────────────────
// GET /api/public/videos.php → articles in the video category (or with video embed)
// For now: returns published articles that belong to a category named "video/videos"
// OR articles whose content contains an iframe/embed (youtube, etc.)
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';
cors();

$pdo   = getDb();
$page  = max(1, intParam('page', 1));
$limit = min(50, max(1, intParam('limit', DEFAULT_LIMIT)));

// Try to find a "video" category
$videoCatStmt = $pdo->query("
    SELECT id FROM categories
    WHERE slug IN ('video','videos','वीडियो') AND is_active = 1
    LIMIT 1
");
$videoCat = $videoCatStmt->fetchColumn();

if ($videoCat) {
    // Articles in video category
    $sql = "
        SELECT a.id, a.title_hi, a.title_en, a.slug, a.excerpt_hi, a.excerpt_en,
               a.featured_image, a.content_hi, a.content_en, a.language, a.views, a.published_at,
               c.name_hi AS category_name_hi, c.name_en AS category_name_en, c.slug AS category_slug
          FROM articles a
          LEFT JOIN categories c ON c.id = a.category_id
         WHERE a.status = 'published' AND a.category_id = ?
         ORDER BY a.published_at DESC
    ";
    $result = paginate($pdo, $sql, [(int)$videoCat], $page, $limit);
} else {
    // Fallback: articles whose content contains an iframe or youtube embed
    $sql = "
        SELECT a.id, a.title_hi, a.title_en, a.slug, a.excerpt_hi, a.excerpt_en,
               a.featured_image, a.content_hi, a.content_en, a.language, a.views, a.published_at,
               c.name_hi AS category_name_hi, c.name_en AS category_name_en, c.slug AS category_slug
          FROM articles a
          LEFT JOIN categories c ON c.id = a.category_id
         WHERE a.status = 'published'
           AND (a.content_hi LIKE '%<iframe%' OR a.content_en LIKE '%<iframe%'
             OR a.content_hi LIKE '%youtube%'  OR a.content_en LIKE '%youtube%')
         ORDER BY a.published_at DESC
    ";
    $result = paginate($pdo, $sql, [], $page, $limit);
}

foreach ($result['items'] as &$item) {
    $item['featured_image_url'] = resolveUploadUrl($item['featured_image']);
    // Extract first iframe/embed src for convenience
    $content = $item['content_hi'] ?: $item['content_en'];
    $item['video_embed'] = null;
    if (preg_match('/<iframe[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $m)) {
        $item['video_embed'] = $m[1];
    }
    unset($item['content_hi'], $item['content_en']);
}
unset($item);

ok($result);
