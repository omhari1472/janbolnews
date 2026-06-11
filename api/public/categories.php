<?php
// ── Janbolnews Public — Categories ──────────────────────────────────
// GET /api/public/categories.php → all active categories with article counts
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';
cors();

$pdo = getDb();

$stmt = $pdo->query("
    SELECT c.id, c.name_hi, c.name_en, c.slug, c.parent_id,
           c.icon, c.color, c.sort_order,
           COUNT(a.id) AS article_count
      FROM categories c
      LEFT JOIN articles a ON a.category_id = c.id AND a.status = 'published'
     WHERE c.is_active = 1
     GROUP BY c.id
     ORDER BY c.sort_order ASC, c.id ASC
");
$categories = $stmt->fetchAll();

ok(['categories' => $categories]);
