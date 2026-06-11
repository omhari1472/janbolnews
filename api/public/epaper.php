<?php
// ── Janbolnews Public — E-Paper ──────────────────────────────────────
// GET /api/public/epaper.php              → list epapers (optionally filtered by date/city)
// GET /api/public/epaper.php?id=123       → single epaper details
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';
cors();

$pdo  = getDb();
$id   = intParam('id');

// ── Single epaper ─────────────────────────────────────────────────────
if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM epapers WHERE id = ? AND is_active = 1");
    $stmt->execute([$id]);
    $ep = $stmt->fetch();
    if (!$ep) fail('E-paper not found', 404);

    $ep['pdf_url']       = resolveUploadUrl($ep['pdf_path']);
    $ep['thumbnail_url'] = resolveUploadUrl($ep['thumbnail_path']);
    ok(['epaper' => $ep]);
}

// ── List epapers ──────────────────────────────────────────────────────
$date  = strParam('date');   // YYYY-MM-DD
$city  = strParam('city');
$page  = max(1, intParam('page', 1));
$limit = min(50, max(1, intParam('limit', DEFAULT_LIMIT)));

$where  = ['is_active = 1'];
$params = [];

if ($date !== '') {
    $where[]  = 'date = ?';
    $params[] = $date;
}
if ($city !== '') {
    $where[]  = 'city LIKE ?';
    $params[] = '%' . $city . '%';
}

$whereClause = 'WHERE ' . implode(' AND ', $where);
$sql = "SELECT * FROM epapers {$whereClause} ORDER BY date DESC, id DESC";

$result = paginate($pdo, $sql, $params, $page, $limit);

foreach ($result['items'] as &$ep) {
    $ep['pdf_url']       = resolveUploadUrl($ep['pdf_path']);
    $ep['thumbnail_url'] = resolveUploadUrl($ep['thumbnail_path']);
}
unset($ep);

ok($result);
