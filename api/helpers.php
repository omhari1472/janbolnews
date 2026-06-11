<?php
// ── Janbolnews — Helpers ─────────────────────────────────────────────

function cors(): void {
    header('Access-Control-Allow-Origin: ' . ALLOWED_ORIGIN);
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Admin-Key');
    header('Content-Type: application/json; charset=utf-8');
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
}

function ok(mixed $data, string $message = 'Success'): void {
    echo json_encode(['success' => true, 'message' => $message, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}

function fail(string $message, int $code = 400): void {
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

function body(): array {
    $raw = file_get_contents('php://input');
    $decoded = json_decode($raw, true);
    // Also merge $_POST for multipart/form-data requests
    return array_merge($decoded ?? [], $_POST ?? []);
}

function genSlug(string $text): string {
    // Handle Hindi/Devanagari — transliterate to ASCII slug using romanization if possible
    // For ASCII text: standard slug generation
    $text = mb_strtolower(trim($text), 'UTF-8');
    // Replace common Hindi vowel signs / spaces
    $text = preg_replace('/[\s\-]+/u', '-', $text);
    // Keep alphanumeric + hyphens; drop other Unicode for slug safety
    $text = preg_replace('/[^a-z0-9\-]/u', '', $text);
    $text = trim($text, '-');
    // If the slug ended up empty (pure Hindi), use a timestamp
    if ($text === '') {
        $text = 'article-' . time();
    }
    return $text;
}

function genUniqueSlug(string $base, PDO $pdo, string $table = 'articles', int $excludeId = 0): string {
    $slug  = genSlug($base);
    $orig  = $slug;
    $i     = 1;
    while (true) {
        $q = $pdo->prepare("SELECT id FROM {$table} WHERE slug = ? AND id != ?");
        $q->execute([$slug, $excludeId]);
        if (!$q->fetch()) break;
        $slug = $orig . '-' . $i++;
    }
    return $slug;
}

function paginate(PDO $pdo, string $sql, array $params, int $page, int $limit): array {
    // Count total
    $countSql = preg_replace('/SELECT .+? FROM/is', 'SELECT COUNT(*) as total FROM', $sql);
    // Strip ORDER BY for count
    $countSql = preg_replace('/ORDER BY .+$/is', '', $countSql);
    $cs = $pdo->prepare($countSql);
    $cs->execute($params);
    $total = (int)($cs->fetch()['total'] ?? 0);

    $offset = ($page - 1) * $limit;
    $stmt = $pdo->prepare($sql . " LIMIT {$limit} OFFSET {$offset}");
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    return [
        'items'        => $rows,
        'total'        => $total,
        'page'         => $page,
        'limit'        => $limit,
        'total_pages'  => $limit > 0 ? (int)ceil($total / $limit) : 1,
        'has_next'     => ($page * $limit) < $total,
    ];
}

function storageUrl(string $path): string {
    if (empty($path)) return '';
    // If already a full URL, return as-is
    if (str_starts_with($path, 'http')) return $path;

    if (UPLOAD_URL) return rtrim(UPLOAD_URL, '/') . '/' . ltrim($path, '/');

    $scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host    = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base    = $scheme . '://' . $host;
    // Detect base path: strip /api suffix from SCRIPT_NAME-derived path
    $script  = $_SERVER['SCRIPT_NAME'] ?? '';
    $apiBase = rtrim(dirname(dirname($script)), '/');  // go up two levels from /api/xxx.php
    return $base . $apiBase . '/api/' . ltrim($path, '/');
}

function resolveUploadUrl(string $relativePath): string {
    if (empty($relativePath)) return '';
    return storageUrl('uploads/' . $relativePath);
}

function sanitize(string $val): string {
    return htmlspecialchars(trim($val), ENT_QUOTES, 'UTF-8');
}

function intParam(string $key, int $default = 0): int {
    return isset($_GET[$key]) ? (int)$_GET[$key] : $default;
}

function strParam(string $key, string $default = ''): string {
    return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
}

function saveUploadedFile(array $file, string $destDir, array $allowedMimes, int $maxBytes): string {
    if ($file['error'] !== UPLOAD_ERR_OK) fail('File upload error: ' . $file['error']);
    if ($file['size'] > $maxBytes) fail('File too large (max ' . round($maxBytes / 1024 / 1024) . 'MB)');
    $mime = mime_content_type($file['tmp_name']);
    if (!in_array($mime, $allowedMimes)) fail('Invalid file type: ' . $mime);

    if (!is_dir($destDir)) mkdir($destDir, 0755, true);

    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $name = uniqid('jbn_', true) . '.' . $ext;
    $dest = rtrim($destDir, '/') . '/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $dest)) fail('Could not save uploaded file');
    return $name;
}
