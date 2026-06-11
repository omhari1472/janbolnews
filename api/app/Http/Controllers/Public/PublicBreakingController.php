<?php
namespace App\Http\Controllers\Public;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\BreakingNews;
use App\Models\Article;

class PublicBreakingController extends Controller {
    use ApiResponse;

    public function index() {
        $items = BreakingNews::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id','text_hi','text_en','url','is_active','sort_order']);

        // Also include recent breaking articles as headlines
        $articles = Article::published()->breaking()
            ->orderByDesc('published_at')
            ->limit(5)
            ->get(['id','title_hi','title_en','slug','published_at'])
            ->map(fn($a) => [
                'id'      => $a->id,
                'text_hi' => $a->title_hi,
                'text_en' => $a->title_en,
                'url'     => null,
                'slug'    => $a->slug,
            ]);

        return $this->successResponse(array_merge($items->toArray(), $articles->toArray()));
    }
}
