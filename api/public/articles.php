<?php
// ── Janbolnews Public — Articles ─────────────────────────────────────
// GET /api/public/articles.php              → paginated list
// GET /api/public/articles.php?slug=xxx     → single article
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';
cors();

$pdo  = getDb();
$slug = strParam('slug');

// ── Single article ────────────────────────────────────────────────────
if ($slug !== '') {
    $stmt = $pdo->prepare("
        SELECT a.*,
               c.name_hi  AS category_name_hi, c.name_en AS category_name_en, c.slug AS category_slug,
               c.color    AS category_color,
               au.name    AS author_name, au.avatar AS author_avatar, au.bio_hi AS author_bio_hi, au.bio_en AS author_bio_en
          FROM articles a
          LEFT JOIN categories c  ON c.id = a.category_id
          LEFT JOIN authors    au ON au.id = a.author_id
         WHERE a.slug = ? AND a.status = 'published'
    ");
    $stmt->execute([$slug]);
    $article = $stmt->fetch();
    if (!$article) fail('Article not found', 404);

    // Increment view count (rate-limit by IP: once per hour per article)
    $ip       = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $oneHrAgo = date('Y-m-d H:i:s', time() - 3600);
    $viewCheck = $pdo->prepare("SELECT COUNT(*) FROM article_views WHERE article_id=? AND ip=? AND created_at > ?");
    $viewCheck->execute([$article['id'], $ip, $oneHrAgo]);
    if ((int)$viewCheck->fetchColumn() === 0) {
        $pdo->prepare("INSERT INTO article_views (article_id, ip) VALUES (?,?)")->execute([$article['id'], $ip]);
        $pdo->prepare("UPDATE articles SET views = views + 1 WHERE id = ?")->execute([$article['id']]);
        $article['views'] = (int)$article['views'] + 1;
    }

    // Tags
    $tagStmt = $pdo->prepare("
        SELECT t.name_hi, t.name_en, t.slug
          FROM tags t
          JOIN article_tags at2 ON at2.tag_id = t.id
         WHERE at2.article_id = ?
    ");
    $tagStmt->execute([$article['id']]);
    $article['tags'] = $tagStmt->fetchAll();

    // Related articles (same category, not same slug, latest 5)
    $relStmt = $pdo->prepare("
        SELECT a.id, a.title_hi, a.title_en, a.slug, a.excerpt_hi, a.excerpt_en,
               a.featured_image, a.views, a.published_at, a.language
          FROM articles a
         WHERE a.category_id = ? AND a.slug != ? AND a.status = 'published'
         ORDER BY a.published_at DESC
         LIMIT 5
    ");
    $relStmt->execute([$article['category_id'], $slug]);
    $related = $relStmt->fetchAll();

    // Resolve image URLs
    $article['featured_image_url'] = resolveUploadUrl($article['featured_image']);
    $article['author_avatar_url']  = resolveUploadUrl($article['author_avatar']);
    foreach ($related as &$r) {
        $r['featured_image_url'] = resolveUploadUrl($r['featured_image']);
    }
    unset($r);

    ok(['article' => $article, 'related' => $related]);
}

// ── Article list ──────────────────────────────────────────────────────
$lang     = strParam('lang');         // hi | en | both | '' (all)
$category = strParam('category');     // category slug
$featured = strParam('featured');     // '1' = only featured
$breaking = strParam('breaking');     // '1' = only breaking
$page     = max(1, intParam('page', 1));
$limit    = min(100, max(1, intParam('limit', DEFAULT_LIMIT)));

$where  = ["a.status = 'published'"];
$params = [];

if ($lang !== '') {
    $where[]  = "(a.language = ? OR a.language = 'both')";
    $params[] = $lang;
}
if ($category !== '') {
    $where[]  = "c.slug = ?";
    $params[] = $category;
}
if ($featured === '1') {
    $where[] = "a.is_featured = 1";
}
if ($breaking === '1') {
    $where[] = "a.is_breaking = 1";
}

$whereClause = 'WHERE ' . implode(' AND ', $where);

$sql = "
    SELECT a.id, a.title_hi, a.title_en, a.slug, a.excerpt_hi, a.excerpt_en,
           a.featured_image, a.is_featured, a.is_breaking, a.language,
           a.views, a.published_at, a.created_at,
           c.name_hi AS category_name_hi, c.name_en AS category_name_en,
           c.slug AS category_slug, c.color AS category_color,
           au.name AS author_name
      FROM articles a
      LEFT JOIN categories c  ON c.id = a.category_id
      LEFT JOIN authors    au ON au.id = a.author_id
    {$whereClause}
    ORDER BY a.published_at DESC, a.id DESC
";

$result = paginate($pdo, $sql, $params, $page, $limit);

// Resolve image URLs for list items
foreach ($result['items'] as &$item) {
    $item['featured_image_url'] = resolveUploadUrl($item['featured_image']);
}
unset($item);

ok($result);
