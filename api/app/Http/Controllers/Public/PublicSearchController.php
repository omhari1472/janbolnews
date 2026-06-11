<?php
namespace App\Http\Controllers\Public;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Article;
use Illuminate\Http\Request;

class PublicSearchController extends Controller {
    use ApiResponse;

    public function search(Request $request) {
        $q    = trim($request->q ?? '');
        $limit = min((int)($request->limit ?? 12), 50);
        $page  = max((int)($request->page ?? 1), 1);

        if (strlen($q) < 2) {
            return $this->errorResponse('Query too short', 400);
        }

        $results = Article::with(['category','author'])
            ->published()
            ->where(fn($query) =>
                $query->where('title_hi',   'like', "%{$q}%")
                      ->orWhere('title_en',  'like', "%{$q}%")
                      ->orWhere('excerpt_hi','like', "%{$q}%")
                      ->orWhere('content_hi','like', "%{$q}%")
            )
            ->when($request->category, fn($query,$v) => $query->whereHas('category',fn($q2)=>$q2->where('slug',$v)))
            ->orderByDesc('published_at')
            ->paginate($limit, ['*'], 'page', $page);

        return $this->successResponse([
            'query'       => $q,
            'items'       => $results->map(fn($a) => [
                'id'            => $a->id,
                'title_hi'      => $a->title_hi,
                'title_en'      => $a->title_en,
                'slug'          => $a->slug,
                'excerpt_hi'    => $a->excerpt_hi,
                'featured_image'=> $a->featured_image ? asset('storage/'.$a->featured_image) : null,
                'category_slug' => $a->category?->slug,
                'category_name' => $a->category?->name_hi,
                'published_at'  => $a->published_at?->toIso8601String(),
            ]),
            'total'       => $results->total(),
            'page'        => $results->currentPage(),
            'total_pages' => $results->lastPage(),
        ]);
    }
}
