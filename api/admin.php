<?php
// ── Janbolnews — Admin API ───────────────────────────────────────────
// All actions behind ?key=janbolnews-admin-2026 OR X-Admin-Key header
// OR after login with session token (stored in site_settings)
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
cors();

$pdo    = getDb();
$action = $_GET['action'] ?? $_POST['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// ── Auth ──────────────────────────────────────────────────────────────
// login action is exempt from key check — it accepts email+password
if ($action !== 'login') {
    $adminKey = $_SERVER['HTTP_X_ADMIN_KEY'] ?? $_GET['key'] ?? '';
    if ($adminKey !== ADMIN_KEY) {
        // Also allow session token (sent via key/X-Admin-Key or token/X-Session-Token)
        $token = $_SERVER['HTTP_X_SESSION_TOKEN'] ?? $_GET['token'] ?? $adminKey;
        if ($token !== '') {
            $ts = $pdo->prepare("SELECT value FROM site_settings WHERE key = 'session_token'");
            $ts->execute();
            $stored = $ts->fetchColumn();
            if (!$stored || $stored !== $token) fail('Unauthorized', 401);
        } else {
            fail('Unauthorized', 401);
        }
    }
}

// ══════════════════════════════════════════════════════════════════════
// AUTH
// ══════════════════════════════════════════════════════════════════════

if ($action === 'login' && $method === 'POST') {
    $b        = body();
    $email    = trim($b['email'] ?? '');
    $password = trim($b['password'] ?? '');
    if (!$email || !$password) fail('Email and password required');

    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE email = ? AND is_active = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        fail('Invalid email or password', 401);
    }

    // Generate session token and store in settings
    $token = bin2hex(random_bytes(32));
    $pdo->prepare("INSERT OR REPLACE INTO site_settings (key, value) VALUES ('session_token', ?)")->execute([$token]);

    ok([
        'token' => $token,
        'user'  => [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ]
    ], 'Login successful');
}

// ══════════════════════════════════════════════════════════════════════
// STATS
// ══════════════════════════════════════════════════════════════════════

if ($action === 'stats') {
    $todayStart = date('Y-m-d') . ' 00:00:00';
    $todayEnd   = date('Y-m-d') . ' 23:59:59';

    $todayArticles = (int)$pdo->query("SELECT COUNT(*) FROM articles WHERE status='published' AND published_at BETWEEN '{$todayStart}' AND '{$todayEnd}'")->fetchColumn();
    $totalArticles = (int)$pdo->query("SELECT COUNT(*) FROM articles WHERE status='published'")->fetchColumn();
    $totalViews    = (int)($pdo->query("SELECT COALESCE(SUM(views),0) FROM articles")->fetchColumn() ?? 0);
    $breakingCount = (int)$pdo->query("SELECT COUNT(*) FROM breaking_news WHERE is_active=1")->fetchColumn();
    $totalEpapers  = (int)$pdo->query("SELECT COUNT(*) FROM epapers WHERE is_active=1")->fetchColumn();
    $draftCount    = (int)$pdo->query("SELECT COUNT(*) FROM articles WHERE status='draft'")->fetchColumn();

    ok([
        'today_articles' => $todayArticles,
        'total_articles' => $totalArticles,
        'total_views'    => $totalViews,
        'breaking_count' => $breakingCount,
        'total_epapers'  => $totalEpapers,
        'draft_count'    => $draftCount,
    ]);
}

// ══════════════════════════════════════════════════════════════════════
// ARTICLES
// ══════════════════════════════════════════════════════════════════════

if ($action === 'articles') {
    $status   = strParam('status');    // '' = all
    $category = strParam('category');
    $lang     = strParam('lang');
    $page     = max(1, intParam('page', 1));
    $limit    = min(100, max(1, intParam('limit', DEFAULT_LIMIT)));

    $where  = ['1=1'];
    $params = [];
    if ($status !== '') { $where[] = 'a.status = ?';      $params[] = $status; }
    if ($category !== '') { $where[] = 'c.slug = ?';      $params[] = $category; }
    if ($lang !== '')   { $where[] = "(a.language = ? OR a.language = 'both')"; $params[] = $lang; }

    $wc  = 'WHERE ' . implode(' AND ', $where);
    $sql = "
        SELECT a.id, a.title_hi, a.title_en, a.slug, a.status, a.is_featured, a.is_breaking,
               a.language, a.views, a.published_at, a.created_at, a.updated_at,
               a.featured_image, a.excerpt_hi, a.excerpt_en,
               c.name_hi AS category_name_hi, c.name_en AS category_name_en, c.slug AS category_slug,
               au.name AS author_name
          FROM articles a
          LEFT JOIN categories c  ON c.id = a.category_id
          LEFT JOIN authors    au ON au.id = a.author_id
        {$wc}
        ORDER BY a.updated_at DESC, a.id DESC
    ";
    $result = paginate($pdo, $sql, $params, $page, $limit);
    foreach ($result['items'] as &$item) {
        $item['featured_image_url'] = resolveUploadUrl($item['featured_image']);
    }
    unset($item);
    ok($result);
}

if ($action === 'article' && isset($_GET['id'])) {
    $id   = (int)$_GET['id'];
    $stmt = $pdo->prepare("
        SELECT a.*,
               c.name_hi AS category_name_hi, c.name_en AS category_name_en, c.slug AS category_slug,
               au.name AS author_name
          FROM articles a
          LEFT JOIN categories c  ON c.id = a.category_id
          LEFT JOIN authors    au ON au.id = a.author_id
         WHERE a.id = ?
    ");
    $stmt->execute([$id]);
    $article = $stmt->fetch();
    if (!$article) fail('Article not found', 404);

    // Tags
    $tagStmt = $pdo->prepare("SELECT t.* FROM tags t JOIN article_tags at2 ON at2.tag_id=t.id WHERE at2.article_id=?");
    $tagStmt->execute([$id]);
    $article['tags'] = $tagStmt->fetchAll();
    $article['featured_image_url'] = resolveUploadUrl($article['featured_image']);
    ok(['article' => $article]);
}

if ($action === 'save_article' && $method === 'POST') {
    $b = body();
    $id = (int)($b['id'] ?? 0);

    $titleHi  = trim($b['title_hi'] ?? '');
    $titleEn  = trim($b['title_en'] ?? '');
    if (!$titleHi && !$titleEn) fail('At least one title (Hindi or English) is required');

    $slugBase = $b['slug'] ?? ($titleEn ?: $titleHi);
    $slug     = genUniqueSlug($slugBase, $pdo, 'articles', $id);

    $featuredImage = '';
    if (!empty($_FILES['featured_image']['name'])) {
        $fname = saveUploadedFile(
            $_FILES['featured_image'],
            IMG_DIR,
            ['image/jpeg','image/png','image/webp','image/gif'],
            MAX_IMG_SIZE
        );
        $featuredImage = 'images/' . $fname;
    } elseif (!empty($b['featured_image'])) {
        $featuredImage = $b['featured_image'];
    }

    $statusVal   = in_array($b['status'] ?? '', ['draft','published','scheduled']) ? $b['status'] : 'draft';
    $langVal     = in_array($b['language'] ?? '', ['hi','en','both']) ? $b['language'] : 'both';
    $publishedAt = $b['published_at'] ?? null;
    if ($statusVal === 'published' && !$publishedAt) $publishedAt = date('Y-m-d H:i:s');
    $now = date('Y-m-d H:i:s');

    if ($id > 0) {
        // Update
        $q = $pdo->prepare("
            UPDATE articles SET
                title_hi=?, title_en=?, slug=?, content_hi=?, content_en=?,
                excerpt_hi=?, excerpt_en=?, category_id=?, author_id=?,
                status=?, is_featured=?, is_breaking=?, language=?,
                published_at=?, updated_at=?
                " . ($featuredImage !== '' ? ', featured_image=?' : '') . "
            WHERE id=?
        ");
        $params = [
            $titleHi, $titleEn, $slug,
            $b['content_hi'] ?? '', $b['content_en'] ?? '',
            $b['excerpt_hi'] ?? '', $b['excerpt_en'] ?? '',
            ($b['category_id'] ?? null) ?: null,
            ($b['author_id'] ?? null) ?: null,
            $statusVal,
            (int)($b['is_featured'] ?? 0),
            (int)($b['is_breaking'] ?? 0),
            $langVal,
            $publishedAt,
            $now,
        ];
        if ($featuredImage !== '') $params[] = $featuredImage;
        $params[] = $id;
        $q->execute($params);
    } else {
        // Insert
        $q = $pdo->prepare("
            INSERT INTO articles
                (title_hi, title_en, slug, content_hi, content_en, excerpt_hi, excerpt_en,
                 featured_image, category_id, author_id, status, is_featured, is_breaking,
                 language, published_at, created_at, updated_at)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ");
        $q->execute([
            $titleHi, $titleEn, $slug,
            $b['content_hi'] ?? '', $b['content_en'] ?? '',
            $b['excerpt_hi'] ?? '', $b['excerpt_en'] ?? '',
            $featuredImage,
            ($b['category_id'] ?? null) ?: null,
            ($b['author_id'] ?? null) ?: null,
            $statusVal,
            (int)($b['is_featured'] ?? 0),
            (int)($b['is_breaking'] ?? 0),
            $langVal,
            $publishedAt,
            $now, $now,
        ]);
        $id = (int)$pdo->lastInsertId();
    }

    // Tags: expects comma-separated tag IDs in $b['tag_ids'] or array
    $pdo->prepare("DELETE FROM article_tags WHERE article_id = ?")->execute([$id]);
    $tagIds = $b['tag_ids'] ?? [];
    if (is_string($tagIds)) $tagIds = array_filter(array_map('intval', explode(',', $tagIds)));
    $tagIns = $pdo->prepare("INSERT OR IGNORE INTO article_tags (article_id, tag_id) VALUES (?,?)");
    foreach ($tagIds as $tid) {
        if ((int)$tid > 0) $tagIns->execute([$id, (int)$tid]);
    }

    ok(['id' => $id, 'slug' => $slug], $id > 0 ? 'Article updated' : 'Article created');
}

if ($action === 'delete_article' && $method === 'POST') {
    $b  = body();
    $id = (int)($b['id'] ?? 0);
    if (!$id) fail('ID required');
    $pdo->prepare("DELETE FROM articles WHERE id = ?")->execute([$id]);
    ok(['deleted' => true]);
}

// ══════════════════════════════════════════════════════════════════════
// BREAKING NEWS
// ══════════════════════════════════════════════════════════════════════

if ($action === 'breaking') {
    $stmt = $pdo->query("SELECT * FROM breaking_news ORDER BY sort_order ASC, id DESC");
    ok(['breaking' => $stmt->fetchAll()]);
}

if ($action === 'save_breaking' && $method === 'POST') {
    $b  = body();
    $id = (int)($b['id'] ?? 0);
    $textHi = trim($b['text_hi'] ?? '');
    $textEn = trim($b['text_en'] ?? '');
    if (!$textHi && !$textEn) fail('At least one text (Hindi or English) required');

    if ($id > 0) {
        $pdo->prepare("UPDATE breaking_news SET text_hi=?, text_en=?, url=?, is_active=?, sort_order=? WHERE id=?")
            ->execute([$textHi, $textEn, $b['url'] ?? '', (int)($b['is_active'] ?? 1), (int)($b['sort_order'] ?? 0), $id]);
    } else {
        $pdo->prepare("INSERT INTO breaking_news (text_hi, text_en, url, is_active, sort_order) VALUES (?,?,?,?,?)")
            ->execute([$textHi, $textEn, $b['url'] ?? '', (int)($b['is_active'] ?? 1), (int)($b['sort_order'] ?? 0)]);
        $id = (int)$pdo->lastInsertId();
    }
    ok(['id' => $id], 'Breaking news saved');
}

if ($action === 'delete_breaking' && $method === 'POST') {
    $b  = body();
    $id = (int)($b['id'] ?? 0);
    if (!$id) fail('ID required');
    $pdo->prepare("DELETE FROM breaking_news WHERE id = ?")->execute([$id]);
    ok(['deleted' => true]);
}

// ══════════════════════════════════════════════════════════════════════
// EPAPERS
// ══════════════════════════════════════════════════════════════════════

if ($action === 'epapers') {
    $page  = max(1, intParam('page', 1));
    $limit = min(50, max(1, intParam('limit', DEFAULT_LIMIT)));
    $sql   = "SELECT * FROM epapers ORDER BY date DESC, id DESC";
    $result = paginate($pdo, $sql, [], $page, $limit);
    foreach ($result['items'] as &$ep) {
        $ep['pdf_url']       = resolveUploadUrl($ep['pdf_path']);
        $ep['thumbnail_url'] = resolveUploadUrl($ep['thumbnail_path']);
    }
    unset($ep);
    ok($result);
}

if ($action === 'save_epaper' && $method === 'POST') {
    $b     = body();
    $id    = (int)($b['id'] ?? 0);
    $title = trim($b['title'] ?? '');
    $date  = trim($b['date'] ?? date('Y-m-d'));
    if (!$title) fail('Title required');

    // PDF upload
    $pdfPath = $b['pdf_path'] ?? '';
    if (!empty($_FILES['pdf']['name'])) {
        $fname   = saveUploadedFile($_FILES['pdf'], EPAPER_DIR, ['application/pdf'], MAX_PDF_SIZE);
        $pdfPath = 'epapers/' . $fname;
    }

    // Thumbnail upload
    $thumbPath = $b['thumbnail_path'] ?? '';
    if (!empty($_FILES['thumbnail']['name'])) {
        $fname     = saveUploadedFile($_FILES['thumbnail'], EPAPER_DIR, ['image/jpeg','image/png','image/webp'], MAX_IMG_SIZE);
        $thumbPath = 'epapers/' . $fname;
    }

    if ($id > 0) {
        $pdo->prepare("
            UPDATE epapers SET title=?, edition=?, city=?, date=?, total_pages=?, is_active=?
            " . ($pdfPath ? ', pdf_path=?' : '') . "
            " . ($thumbPath ? ', thumbnail_path=?' : '') . "
            WHERE id=?
        ")->execute(array_merge(
            [$title, $b['edition'] ?? '', $b['city'] ?? '', $date, (int)($b['total_pages'] ?? 0), (int)($b['is_active'] ?? 1)],
            $pdfPath   ? [$pdfPath]   : [],
            $thumbPath ? [$thumbPath] : [],
            [$id]
        ));
    } else {
        $pdo->prepare("
            INSERT INTO epapers (title, edition, city, date, pdf_path, thumbnail_path, total_pages, is_active)
            VALUES (?,?,?,?,?,?,?,?)
        ")->execute([$title, $b['edition'] ?? '', $b['city'] ?? '', $date, $pdfPath, $thumbPath, (int)($b['total_pages'] ?? 0), (int)($b['is_active'] ?? 1)]);
        $id = (int)$pdo->lastInsertId();
    }
    ok(['id' => $id], 'E-paper saved');
}

if ($action === 'delete_epaper' && $method === 'POST') {
    $b  = body();
    $id = (int)($b['id'] ?? 0);
    if (!$id) fail('ID required');
    $pdo->prepare("DELETE FROM epapers WHERE id = ?")->execute([$id]);
    ok(['deleted' => true]);
}

// ══════════════════════════════════════════════════════════════════════
// CATEGORIES
// ══════════════════════════════════════════════════════════════════════

if ($action === 'categories') {
    $stmt = $pdo->query("
        SELECT c.*, COUNT(a.id) AS article_count
          FROM categories c
          LEFT JOIN articles a ON a.category_id = c.id
         GROUP BY c.id
         ORDER BY c.sort_order ASC, c.id ASC
    ");
    ok(['categories' => $stmt->fetchAll()]);
}

if ($action === 'save_category' && $method === 'POST') {
    $b      = body();
    $id     = (int)($b['id'] ?? 0);
    $nameHi = trim($b['name_hi'] ?? '');
    $nameEn = trim($b['name_en'] ?? '');
    if (!$nameHi && !$nameEn) fail('At least one category name required');

    $slugBase = $b['slug'] ?? ($nameEn ?: $nameHi);
    $slug     = genUniqueSlug($slugBase, $pdo, 'categories', $id);

    if ($id > 0) {
        $pdo->prepare("UPDATE categories SET name_hi=?, name_en=?, slug=?, parent_id=?, icon=?, color=?, sort_order=?, is_active=? WHERE id=?")
            ->execute([$nameHi, $nameEn, $slug, ($b['parent_id'] ?? null) ?: null, $b['icon'] ?? '', $b['color'] ?? '#e53e3e', (int)($b['sort_order'] ?? 0), (int)($b['is_active'] ?? 1), $id]);
    } else {
        $pdo->prepare("INSERT INTO categories (name_hi, name_en, slug, parent_id, icon, color, sort_order, is_active) VALUES (?,?,?,?,?,?,?,?)")
            ->execute([$nameHi, $nameEn, $slug, ($b['parent_id'] ?? null) ?: null, $b['icon'] ?? '', $b['color'] ?? '#e53e3e', (int)($b['sort_order'] ?? 0), (int)($b['is_active'] ?? 1)]);
        $id = (int)$pdo->lastInsertId();
    }
    ok(['id' => $id, 'slug' => $slug], 'Category saved');
}

if ($action === 'delete_category' && $method === 'POST') {
    $b  = body();
    $id = (int)($b['id'] ?? 0);
    if (!$id) fail('ID required');
    // Nullify articles referencing this category
    $pdo->prepare("UPDATE articles SET category_id = NULL WHERE category_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
    ok(['deleted' => true]);
}

// ══════════════════════════════════════════════════════════════════════
// MEDIA
// ══════════════════════════════════════════════════════════════════════

if ($action === 'media') {
    $page  = max(1, intParam('page', 1));
    $limit = min(100, max(1, intParam('limit', DEFAULT_LIMIT)));
    $sql   = "SELECT * FROM media ORDER BY created_at DESC";
    $result = paginate($pdo, $sql, [], $page, $limit);
    foreach ($result['items'] as &$m) {
        $m['url'] = resolveUploadUrl($m['path']);
    }
    unset($m);
    ok($result);
}

if ($action === 'upload_media' && $method === 'POST') {
    if (empty($_FILES['file']['name'])) fail('No file uploaded');
    $file = $_FILES['file'];

    $allowedMimes = ['image/jpeg','image/png','image/webp','image/gif','image/svg+xml','application/pdf'];
    $fname = saveUploadedFile($file, MEDIA_DIR, $allowedMimes, MAX_PDF_SIZE);
    $path  = 'media/' . $fname;
    $url   = resolveUploadUrl($path);

    $pdo->prepare("INSERT INTO media (filename, original_name, path, mime_type, size, alt_text) VALUES (?,?,?,?,?,?)")
        ->execute([$fname, $file['name'], $path, $file['type'], $file['size'], $file['name']]);
    $mid = (int)$pdo->lastInsertId();

    ok(['id' => $mid, 'url' => $url, 'path' => $path, 'filename' => $fname], 'File uploaded');
}

// ══════════════════════════════════════════════════════════════════════
// AUTHORS
// ══════════════════════════════════════════════════════════════════════

if ($action === 'authors') {
    $stmt = $pdo->query("SELECT * FROM authors ORDER BY name ASC");
    $authors = $stmt->fetchAll();
    foreach ($authors as &$a) {
        $a['avatar_url'] = resolveUploadUrl($a['avatar']);
    }
    unset($a);
    ok(['authors' => $authors]);
}

if ($action === 'save_author' && $method === 'POST') {
    $b  = body();
    $id = (int)($b['id'] ?? 0);
    if (empty($b['name'])) fail('Author name required');

    $avatar = $b['avatar'] ?? '';
    if (!empty($_FILES['avatar']['name'])) {
        $fname  = saveUploadedFile($_FILES['avatar'], IMG_DIR, ['image/jpeg','image/png','image/webp'], MAX_IMG_SIZE);
        $avatar = 'images/' . $fname;
    }

    if ($id > 0) {
        $pdo->prepare("UPDATE authors SET name=?, bio_hi=?, bio_en=?, email=?, is_active=?" . ($avatar !== '' ? ', avatar=?' : '') . " WHERE id=?")
            ->execute(array_merge(
                [trim($b['name']), $b['bio_hi'] ?? '', $b['bio_en'] ?? '', $b['email'] ?? '', (int)($b['is_active'] ?? 1)],
                $avatar !== '' ? [$avatar] : [],
                [$id]
            ));
    } else {
        $pdo->prepare("INSERT INTO authors (name, bio_hi, bio_en, avatar, email, is_active) VALUES (?,?,?,?,?,?)")
            ->execute([trim($b['name']), $b['bio_hi'] ?? '', $b['bio_en'] ?? '', $avatar, $b['email'] ?? '', (int)($b['is_active'] ?? 1)]);
        $id = (int)$pdo->lastInsertId();
    }
    ok(['id' => $id], 'Author saved');
}

// ══════════════════════════════════════════════════════════════════════
// SETTINGS
// ══════════════════════════════════════════════════════════════════════

if ($action === 'settings') {
    $stmt = $pdo->query("SELECT key, value FROM site_settings ORDER BY key ASC");
    $rows = $stmt->fetchAll();
    $settings = [];
    foreach ($rows as $row) {
        if ($row['key'] !== 'session_token') {   // never expose session token
            $settings[$row['key']] = $row['value'];
        }
    }
    ok(['settings' => $settings]);
}

if ($action === 'save_settings' && $method === 'POST') {
    $b   = body();
    $ins = $pdo->prepare("INSERT OR REPLACE INTO site_settings (key, value) VALUES (?,?)");
    $count = 0;
    foreach ($b as $key => $value) {
        if ($key === 'session_token') continue; // protect session token
        $ins->execute([sanitize($key), (string)$value]);
        $count++;
    }
    ok(['saved' => $count], 'Settings saved');
}

// ══════════════════════════════════════════════════════════════════════
// TAGS
// ══════════════════════════════════════════════════════════════════════

if ($action === 'tags') {
    $stmt = $pdo->query("SELECT * FROM tags ORDER BY name_en ASC, name_hi ASC");
    ok(['tags' => $stmt->fetchAll()]);
}

if ($action === 'save_tag' && $method === 'POST') {
    $b      = body();
    $id     = (int)($b['id'] ?? 0);
    $nameHi = trim($b['name_hi'] ?? '');
    $nameEn = trim($b['name_en'] ?? '');
    if (!$nameHi && !$nameEn) fail('Tag name required');

    $slugBase = $b['slug'] ?? ($nameEn ?: $nameHi);
    $slug     = genUniqueSlug($slugBase, $pdo, 'tags', $id);

    if ($id > 0) {
        $pdo->prepare("UPDATE tags SET name_hi=?, name_en=?, slug=? WHERE id=?")->execute([$nameHi, $nameEn, $slug, $id]);
    } else {
        $pdo->prepare("INSERT INTO tags (name_hi, name_en, slug) VALUES (?,?,?)")->execute([$nameHi, $nameEn, $slug]);
        $id = (int)$pdo->lastInsertId();
    }
    ok(['id' => $id, 'slug' => $slug], 'Tag saved');
}

if ($action === 'delete_tag' && $method === 'POST') {
    $b  = body();
    $id = (int)($b['id'] ?? 0);
    if (!$id) fail('ID required');
    $pdo->prepare("DELETE FROM article_tags WHERE tag_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM tags WHERE id = ?")->execute([$id]);
    ok(['deleted' => true]);
}

// ══════════════════════════════════════════════════════════════════════
// ADMIN USERS
// ══════════════════════════════════════════════════════════════════════

if ($action === 'admin_users') {
    $stmt = $pdo->query("SELECT id, name, email, role, is_active, created_at FROM admin_users ORDER BY id ASC");
    ok(['users' => $stmt->fetchAll()]);
}

if ($action === 'save_admin_user' && $method === 'POST') {
    $b  = body();
    $id = (int)($b['id'] ?? 0);
    if (empty($b['name']) || empty($b['email'])) fail('Name and email required');

    $role = in_array($b['role'] ?? '', ['super','editor','reporter']) ? $b['role'] : 'reporter';

    if ($id > 0) {
        $updates = "name=?, email=?, role=?, is_active=?";
        $params  = [trim($b['name']), trim($b['email']), $role, (int)($b['is_active'] ?? 1)];
        if (!empty($b['password'])) {
            $updates .= ', password_hash=?';
            $params[] = password_hash($b['password'], PASSWORD_DEFAULT);
        }
        $params[] = $id;
        $pdo->prepare("UPDATE admin_users SET {$updates} WHERE id=?")->execute($params);
    } else {
        if (empty($b['password'])) fail('Password required for new user');
        $pdo->prepare("INSERT INTO admin_users (name, email, password_hash, role, is_active) VALUES (?,?,?,?,?)")
            ->execute([trim($b['name']), trim($b['email']), password_hash($b['password'], PASSWORD_DEFAULT), $role, 1]);
        $id = (int)$pdo->lastInsertId();
    }
    ok(['id' => $id], 'Admin user saved');
}

fail('Unknown action');
