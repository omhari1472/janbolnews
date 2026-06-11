<?php
// Single router for all public API endpoints
// Routes: /articles, /articles/{slug}, /categories, /breaking, /search, /epapers, /epapers/{id}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim(str_replace('/api/public', '', $uri), '/');
$parts = array_values(array_filter(explode('/', $uri)));
$resource = $parts[0] ?? '';
$id = $parts[1] ?? null;

if ($id) $_GET['slug'] = $_GET['id'] = $id;

switch ($resource) {
    case 'articles': require __DIR__ . '/articles.php'; break;
    case 'categories': require __DIR__ . '/categories.php'; break;
    case 'breaking': require __DIR__ . '/breaking.php'; break;
    case 'search': require __DIR__ . '/search.php'; break;
    case 'epapers': require __DIR__ . '/epaper.php'; break;
    case 'videos': require __DIR__ . '/videos.php'; break;
    default:
        header('Content-Type: application/json');
        echo json_encode(['success'=>false,'message'=>'Not found']);
}
