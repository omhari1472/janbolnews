<?php
namespace App\Http\Controllers\Public;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Epaper;
use Illuminate\Http\Request;

class PublicEpaperController extends Controller {
    use ApiResponse;

    public function index(Request $request) {
        $limit = min((int)($request->limit ?? 20), 50);
        $items = Epaper::withCount('articles')
            ->where('status', 'published')
            ->orderByDesc('edition_date')
            ->paginate($limit);

        return $this->successResponse([
            'items' => $items->map(fn($e) => $this->fmt($e)),
            'total' => $items->total(),
        ]);
    }

    public function show(Epaper $epaper) {
        abort_if($epaper->status !== 'published', 404);
        $epaper->load(['articles.category']);
        return $this->successResponse($this->fmt($epaper, true));
    }

    private function fmt(Epaper $e, bool $withArticles = false): array {
        $data = [
            'id'            => $e->id,
            'title'         => $e->title,
            'edition_date'  => $e->edition_date?->toDateString(),
            'edition'       => $e->edition,
            'article_count' => $e->articles_count ?? ($e->relationLoaded('articles') ? $e->articles->count() : 0),
        ];
        if ($withArticles) {
            $data['articles'] = $e->articles->map(fn($a) => [
                'id'                 => $a->id,
                'title_hi'           => $a->title_hi,
                'title_en'           => $a->title_en,
                'slug'               => $a->slug,
                'excerpt_hi'         => $a->excerpt_hi,
                'featured_image_url' => $a->featured_image ? asset('storage/'.$a->featured_image) : null,
                'category'           => $a->category?->name_hi,
                'category_slug'      => $a->category?->slug,
                'position'           => $a->pivot->position,
            ])->values();
        }
        return $data;
    }
}
