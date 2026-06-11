<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Article;
use App\Models\BreakingNews;
use App\Models\Epaper;

class AdminDashboardController extends Controller {
    use ApiResponse;

    public function stats() {
        return $this->successResponse([
            'total_articles'   => Article::count(),
            'published'        => Article::where('status','published')->count(),
            'drafts'           => Article::where('status','draft')->count(),
            'today_articles'   => Article::whereDate('created_at', today())->count(),
            'total_views'      => Article::sum('views'),
            'breaking_count'   => BreakingNews::where('is_active',true)->count(),
            'total_epapers'    => Epaper::count(),
            'featured_count'   => Article::where('is_featured',true)->count(),
        ]);
    }
}
