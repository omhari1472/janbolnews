<?php
// ── Janbolnews Public — Search ───────────────────────────────────────
// GET /api/public/search.php?q=xxx&lang=&category=&page=
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';
cors();

$pdo      = getDb();
$q        = trim(strParam('q'));
$lang     = strParam('lang');
$category = strParam('category');
$page     = max(1, intParam('page', 1));
$limit    = min(50, max(1, intParam('limit', DEFAULT_LIMIT)));

if (strlen($q) < 2) fail('Search query must be at least 2 characters');

$where  = ["a.status = 'published'", "(a.title_hi LIKE ? OR a.title_en LIKE ? OR a.content_hi LIKE ? OR a.content_en LIKE ? OR a.excerpt_hi LIKE ? OR a.excerpt_en LIKE ?)"];
$like   = '%' . $q . '%';
$params = [$like, $like, $like, $like, $like, $like];

if ($lang !== '') {
    $where[]  = "(a.language = ? OR a.language = 'both')";
    $params[] = $lang;
}
if ($category !== '') {
    $where[]  = "c.slug = ?";
    $params[] = $category;
}

$whereClause = 'WHERE ' . implode(' AND ', $where);

$sql = "
    SELECT a.id, a.title_hi, a.title_en, a.slug, a.excerpt_hi, a.excerpt_en,
           a.featured_image, a.is_featured, a.language, a.views, a.published_at,
           c.name_hi AS category_name_hi, c.name_en AS category_name_en,
           c.slug AS category_slug, c.color AS category_color,
           au.name AS author_name
      FROM articles a
      LEFT JOIN categories c  ON c.id = a.category_id
      LEFT JOIN authors    au ON au.id = a.author_id
    {$whereClause}
    ORDER BY a.published_at DESC
";

$result = paginate($pdo, $sql, $params, $page, $limit);

foreach ($result['items'] as &$item) {
    $item['featured_image_url'] = resolveUploadUrl($item['featured_image']);
}
unset($item);

ok(array_merge($result, ['query' => $q]));
