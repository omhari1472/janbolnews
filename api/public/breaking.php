<?php
// ── Janbolnews Public — Breaking News ───────────────────────────────
// GET /api/public/breaking.php → active breaking news ordered by sort_order
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';
cors();

$pdo  = getDb();
$lang = strParam('lang');   // optional: hi | en

$stmt = $pdo->query("
    SELECT id, text_hi, text_en, url, sort_order, created_at
      FROM breaking_news
     WHERE is_active = 1
     ORDER BY sort_order ASC, id DESC
");
$items = $stmt->fetchAll();

// If lang filter: prefer matching text
if ($lang === 'hi') {
    foreach ($items as &$item) { $item['text'] = $item['text_hi']; }
} elseif ($lang === 'en') {
    foreach ($items as &$item) { $item['text'] = $item['text_en']; }
} else {
    // Return both
    foreach ($items as &$item) { $item['text'] = $item['text_hi']; }
}
unset($item);

ok(['breaking' => $items, 'count' => count($items)]);
