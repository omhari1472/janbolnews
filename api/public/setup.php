<?php
/**
 * Janbol News — One-time Production Setup
 * Run ONCE after uploading to Bluehost, then DELETE immediately.
 * URL: https://janbolnews.com/api/setup.php?token=JanbolNews@Setup2026!
 */

define('SETUP_TOKEN', 'JanbolNews@Setup2026!');

if (!isset($_GET['token']) || $_GET['token'] !== SETUP_TOKEN) {
    http_response_code(403);
    die('<h2>403 Forbidden</h2><p>Invalid or missing token.</p>');
}

$laravelRoot = __DIR__ . '/..';
$artisan = $laravelRoot . '/artisan';

header('Content-Type: text/plain; charset=utf-8');
echo "=== Janbol News — Production Setup ===\n\n";

$action = $_GET['action'] ?? 'status';

function runArtisan($root, $cmd) {
    $php = PHP_BINARY;
    $artisan = $root . '/artisan';
    return shell_exec("cd " . escapeshellarg($root) . " && " . escapeshellarg($php) . " " . escapeshellarg($artisan) . " $cmd 2>&1");
}

switch ($action) {
    case 'migrate':
        echo "--- Migrate ---\n";
        echo runArtisan($laravelRoot, 'migrate --force');
        break;

    case 'seed':
        echo "--- Seed ---\n";
        echo runArtisan($laravelRoot, 'db:seed --force');
        break;

    case 'migrate_seed':
        echo "--- Migrate ---\n";
        echo runArtisan($laravelRoot, 'migrate --force');
        echo "\n--- Seed ---\n";
        echo runArtisan($laravelRoot, 'db:seed --force');
        break;

    case 'cache_clear':
        echo runArtisan($laravelRoot, 'config:clear');
        echo runArtisan($laravelRoot, 'cache:clear');
        echo runArtisan($laravelRoot, 'route:clear');
        echo runArtisan($laravelRoot, 'view:clear');
        break;

    case 'storage_link':
        echo runArtisan($laravelRoot, 'storage:link');
        break;

    case 'fix_permissions':
        $storagePath = escapeshellarg($laravelRoot . '/storage');
        $cachePath   = escapeshellarg($laravelRoot . '/bootstrap/cache');
        echo shell_exec("chmod -R 755 $storagePath 2>&1") ?: "storage: chmod 755 done\n";
        echo shell_exec("chmod -R 755 $cachePath 2>&1")   ?: "bootstrap/cache: chmod 755 done\n";
        break;

    case 'key_generate':
        echo runArtisan($laravelRoot, 'key:generate');
        break;

    case 'status':
    default:
        echo "Laravel root : $laravelRoot\n";
        echo ".env exists  : " . (file_exists($laravelRoot . '/.env') ? 'YES' : 'NO') . "\n";
        echo "vendor exists: " . (file_exists($laravelRoot . '/vendor/autoload.php') ? 'YES' : 'NO') . "\n";
        echo "PHP version  : " . PHP_VERSION . "\n";
        echo "PHP binary   : " . PHP_BINARY . "\n";
        echo "shell_exec   : " . (function_exists('shell_exec') ? 'YES (required)' : 'NO (blocked — contact host)') . "\n";
        echo "\nActions: ?token=JanbolNews@Setup2026!&action=<action>\n";
        echo "  status         — this page\n";
        echo "  migrate        — run migrations\n";
        echo "  seed           — run seeders\n";
        echo "  migrate_seed   — migrate + seed (first deploy only)\n";
        echo "  cache_clear    — clear config/cache/route/view caches\n";
        echo "  storage_link   — create storage symlink\n";
        echo "  fix_permissions — chmod 755 storage + bootstrap/cache\n";
        echo "  key_generate   — generate new APP_KEY\n";
        echo "\n!! DELETE THIS FILE after setup is complete !!\n";
}
