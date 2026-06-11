<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminArticleController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminBreakingController;
use App\Http\Controllers\Admin\AdminAuthorController;
use App\Http\Controllers\Admin\AdminEpaperController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Public\PublicArticleController;
use App\Http\Controllers\Public\PublicCategoryController;
use App\Http\Controllers\Public\PublicBreakingController;
use App\Http\Controllers\Public\PublicSearchController;
use App\Http\Controllers\Public\PublicEpaperController;

/* ══════════════════════════════════════════
   PUBLIC ROUTES
══════════════════════════════════════════ */
Route::prefix('public')->group(function () {
    // Articles
    Route::get('/articles',         [PublicArticleController::class, 'index']);
    Route::get('/articles/{slug}',  [PublicArticleController::class, 'show']);
    Route::post('/articles/{slug}/view', [PublicArticleController::class, 'incrementView']);

    // Categories
    Route::get('/categories',       [PublicCategoryController::class, 'index']);

    // Breaking news
    Route::get('/breaking',         [PublicBreakingController::class, 'index']);

    // Search
    Route::get('/search',           [PublicSearchController::class, 'search']);

    // E-papers
    Route::get('/epapers',          [PublicEpaperController::class, 'index']);
    Route::get('/epapers/{epaper}', [PublicEpaperController::class, 'show']);
});

/* ══════════════════════════════════════════
   ADMIN AUTH (public)
══════════════════════════════════════════ */
Route::post('/admin/login', [AdminAuthController::class, 'login']);

/* ══════════════════════════════════════════
   ADMIN PROTECTED
══════════════════════════════════════════ */
Route::middleware(['auth:sanctum', 'ability:admin'])->prefix('admin')->group(function () {

    // Auth
    Route::post('/logout',          [AdminAuthController::class, 'logout']);
    Route::get('/me',               [AdminAuthController::class, 'me']);
    Route::put('/profile/password', [AdminAuthController::class, 'updatePassword']);

    // Dashboard
    Route::get('/dashboard/stats',  [AdminDashboardController::class, 'stats']);

    // Articles
    Route::get('/articles',                [AdminArticleController::class, 'index']);
    Route::get('/articles/{article}',      [AdminArticleController::class, 'show']);
    Route::post('/articles',               [AdminArticleController::class, 'store']);
    Route::post('/articles/{article}',     [AdminArticleController::class, 'update']);   // POST with _method=PUT for multipart
    Route::delete('/articles/{article}',   [AdminArticleController::class, 'destroy']);

    // Categories
    Route::get('/categories',              [AdminCategoryController::class, 'index']);
    Route::post('/categories',             [AdminCategoryController::class, 'store']);
    Route::put('/categories/{category}',   [AdminCategoryController::class, 'update']);
    Route::delete('/categories/{category}',[AdminCategoryController::class, 'destroy']);

    // Breaking news
    Route::get('/breaking',                [AdminBreakingController::class, 'index']);
    Route::post('/breaking',               [AdminBreakingController::class, 'store']);
    Route::put('/breaking/{breaking}',     [AdminBreakingController::class, 'update']);
    Route::delete('/breaking/{breaking}',  [AdminBreakingController::class, 'destroy']);
    Route::post('/breaking/{breaking}/toggle', [AdminBreakingController::class, 'toggle']);

    // Authors
    Route::get('/authors',                 [AdminAuthorController::class, 'index']);
    Route::post('/authors',                [AdminAuthorController::class, 'store']);
    Route::post('/authors/{author}',       [AdminAuthorController::class, 'update']);
    Route::delete('/authors/{author}',     [AdminAuthorController::class, 'destroy']);

    // E-papers
    Route::get('/epapers',                 [AdminEpaperController::class, 'index']);
    Route::post('/epapers',                [AdminEpaperController::class, 'store']);
    Route::post('/epapers/{epaper}',       [AdminEpaperController::class, 'update']);
    Route::delete('/epapers/{epaper}',     [AdminEpaperController::class, 'destroy']);

    // Settings
    Route::get('/settings',                [AdminSettingsController::class, 'index']);
    Route::post('/settings',               [AdminSettingsController::class, 'update']);
});
