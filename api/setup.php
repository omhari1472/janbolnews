<?php
/**
 * Janbolnews — Production Setup & Health Check
 * DELETE this file immediately after first deploy verification.
 *
 * Usage:
 *   https://janbolnews.com/api/setup.php?token=jnsetup2026&action=status
 *   https://janbolnews.com/api/setup.php?token=jnsetup2026&action=init_db
 *   https://janbolnews.com/api/setup.php?token=jnsetup2026&action=fix_permissions
 */

define('SETUP_TOKEN', 'jnsetup2026');

if (!isset($_GET['token']) || $_GET['token'] !== SETUP_TOKEN) {
    http_response_code(403);
    die('<h2>403 Forbidden</h2>');
}

header('Content-Type: text/plain; charset=utf-8');
$action = $_GET['action'] ?? 'status';
$root   = __DIR__;   // api/

echo "=== Janbolnews Production Setup ===\n\n";

// ── Status ────────────────────────────────────────────────────────────────
if ($action === 'status') {
    echo "PHP version    : " . PHP_VERSION . "\n";
    echo "SQLite3 ext    : " . (extension_loaded('pdo_sqlite') ? 'YES ✓' : 'NO ✗ — contact host') . "\n";
    echo "GD ext         : " . (extension_loaded('gd') ? 'YES ✓' : 'NO (image uploads may fail)') . "\n";
    echo "fileinfo ext   : " . (extension_loaded('fileinfo') ? 'YES ✓' : 'NO (MIME detection limited)') . "\n";
    echo "shell_exec     : " . (function_exists('shell_exec') ? 'YES' : 'NO (not needed)') . "\n\n";

    $dbDir  = $root . '/db';
    $dbFile = $dbDir . '/janbolnews.sqlite';
    $upDir  = $root . '/uploads';

    echo "db/ directory  : " . (is_dir($dbDir) ? 'EXISTS' : 'MISSING — run fix_permissions') . "\n";
    echo "db/ writable   : " . (is_writable($dbDir) ? 'YES ✓' : 'NO ✗ — run fix_permissions') . "\n";
    echo "SQLite file    : " . (file_exists($dbFile) ? 'EXISTS (' . round(filesize($dbFile)/1024) . ' KB)' : 'NOT YET — run init_db') . "\n";
    echo "uploads/       : " . (is_writable($upDir) ? 'WRITABLE ✓' : 'NOT WRITABLE — run fix_permissions') . "\n\n";

    echo "Actions available:\n";
    echo "  ?action=status           — this page\n";
    echo "  ?action=init_db          — create SQLite DB + seed data\n";
    echo "  ?action=fix_permissions  — chmod 755 on db/ and uploads/\n";
    echo "  ?action=reset_db         — DROP all tables + re-seed (DESTRUCTIVE)\n\n";
    echo "!! DELETE THIS FILE after setup is complete !!\n";
}

// ── Fix Permissions ───────────────────────────────────────────────────────
elseif ($action === 'fix_permissions') {
    $dirs = [
        $root . '/db',
        $root . '/uploads',
        $root . '/uploads/images',
        $root . '/uploads/epapers',
        $root . '/uploads/media',
    ];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
            echo "Created: $dir\n";
        }
        chmod($dir, 0755);
        echo "chmod 755: $dir\n";
    }
    echo "\nDone. Re-run ?action=status to verify.\n";
}

// ── Init DB ───────────────────────────────────────────────────────────────
elseif ($action === 'init_db') {
    try {
        require_once $root . '/config.php';
        require_once $root . '/helpers.php';
        require_once $root . '/db.php';
        $pdo = getDb(); // auto-seeds on fresh DB
        echo "Database initialized and seeded successfully.\n";
        echo "SQLite file: " . DB_PATH . "\n";
        $count = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
        echo "Articles seeded: $count\n";
        $count2 = $pdo->query("SELECT COUNT(*) FROM breaking_news")->fetchColumn();
        echo "Breaking news: $count2\n";
        echo "\nLogin: admin@janbolnews.com / janbolnews2026\n";
        echo "!! Change password in Settings after first login !!\n";
    } catch (Exception $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
}

// ── Reset DB (destructive) ────────────────────────────────────────────────
elseif ($action === 'reset_db') {
    $dbFile = __DIR__ . '/db/janbolnews.sqlite';
    if (file_exists($dbFile)) {
        unlink($dbFile);
        echo "Deleted existing database.\n";
    }
    try {
        require_once $root . '/config.php';
        require_once $root . '/helpers.php';
        require_once $root . '/db.php';
        $pdo = getDb();
        echo "Database re-created and seeded.\n";
    } catch (Exception $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
}

else {
    echo "Unknown action: $action\n";
}
