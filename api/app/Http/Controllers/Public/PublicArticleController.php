<?php
namespace App\Http\Controllers\Public;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Article;
use Illuminate\Http\Request;

class PublicArticleController extends Controller {
    use ApiResponse;

    public function index(Request $request) {
        $limit = min((int)($request->limit ?? 12), 50);
        $page  = max((int)($request->page ?? 1), 1);

        $q = Article::with(['category','author'])
            ->published()
            ->when($request->category, fn($q,$v) => $q->whereHas('category', fn($q2) => $q2->where('slug',$v)))
            ->when($request->featured,  fn($q,$v) => $q->where('is_featured',(bool)$v))
            ->when($request->breaking,  fn($q,$v) => $q->where('is_breaking',(bool)$v))
            ->when($request->lang && $request->lang !== 'both', fn($q,$v) => $q->whereIn('language',[$v,'both']))
            ->orderByDesc('published_at');

        $paginated = $q->paginate($limit, ['*'], 'page', $page);

        return $this->successResponse([
            'items'       => $paginated->map(fn($a) => $this->card($a)),
            'total'       => $paginated->total(),
            'page'        => $paginated->currentPage(),
            'total_pages' => $paginated->lastPage(),
            'has_next'    => $paginated->hasMorePages(),
        ]);
    }

    public function show(Request $request) {
        $slug    = $request->route('slug') ?? $request->query('slug');
        $article = Article::with(['category','author','tags'])->published()->where('slug',$slug)->firstOrFail();

        return $this->successResponse($this->full($article));
    }

    public function incrementView(Request $request) {
        $slug = $request->route('slug') ?? $request->query('slug');
        Article::published()->where('slug',$slug)->increment('views');
        return $this->successResponse([]);
    }

    private function card(Article $a): array {
        return [
            'id'               => $a->id,
            'title_hi'         => $a->title_hi,
            'title_en'         => $a->title_en,
            'slug'             => $a->slug,
            'excerpt_hi'       => $a->excerpt_hi,
            'excerpt_en'       => $a->excerpt_en,
            'featured_image'   => $a->featured_image ? asset('storage/'.$a->featured_image) : null,
            'category_slug'    => $a->category?->slug,
            'category_name'    => $a->category?->name_hi,
            'category_name_hi' => $a->category?->name_hi,
            'category_name_en' => $a->category?->name_en,
            'author_name'      => $a->author?->name,
            'views'            => $a->views,
            'is_featured'      => $a->is_featured,
            'is_breaking'      => $a->is_breaking,
            'published_at'     => $a->published_at?->toIso8601String(),
            'created_at'       => $a->created_at?->toIso8601String(),
        ];
    }

    private function full(Article $a): array {
        return array_merge($this->card($a), [
            'content_hi' => $a->content_hi,
            'content_en' => $a->content_en,
            'tags'       => $a->tags->pluck('name'),
        ]);
    }
}
