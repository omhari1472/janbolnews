<?php
namespace App\Http\Controllers\Public;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Category;

class PublicCategoryController extends Controller {
    use ApiResponse;

    public function index() {
        $cats = Category::where('is_active', true)
            ->withCount(['articles' => fn($q) => $q->where('status','published')])
            ->orderBy('sort_order')
            ->get()
            ->map(fn($c) => [
                'id'            => $c->id,
                'slug'          => $c->slug,
                'name_hi'       => $c->name_hi,
                'name_en'       => $c->name_en,
                'color'         => $c->color,
                'icon'          => $c->icon,
                'article_count' => $c->articles_count,
            ]);
        return $this->successResponse($cats);
    }
}
