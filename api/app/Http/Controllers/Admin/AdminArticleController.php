<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Article;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminArticleController extends Controller {
    use ApiResponse;
    public function __construct(private FileUploadService $uploader) {}

    public function index(Request $request) {
        $q = Article::with(['category','author'])
            ->when($request->status,   fn($q,$v) => $q->where('status',$v))
            ->when($request->category, fn($q,$v) => $q->whereHas('category',fn($q2)=>$q2->where('slug',$v)))
            ->when($request->search,   fn($q,$v) => $q->where(fn($q2)=>$q2->where('title_hi','like',"%$v%")->orWhere('title_en','like',"%$v%")))
            ->when($request->featured, fn($q,$v) => $q->where('is_featured',(bool)$v))
            ->orderByDesc('created_at');

        $articles = $q->paginate($request->limit ?? 20);
        return $this->successResponse([
            'items'       => $articles->map(fn($a) => $this->format($a)),
            'total'       => $articles->total(),
            'page'        => $articles->currentPage(),
            'total_pages' => $articles->lastPage(),
        ]);
    }

    public function show(Article $article) {
        return $this->successResponse($this->format($article->load(['category','author','tags'])));
    }

    public function store(Request $request) {
        $request->validate([
            'title_hi'        => 'required|string|max:500',
            'title_en'        => 'nullable|string|max:500',
            'category_id'     => 'required|exists:categories,id',
            'status'          => 'nullable|in:draft,published,scheduled',
            'featured_image'  => 'nullable|image|max:5120',
        ]);

        $slug = $this->uniqueSlug($request->title_hi);
        $data = $request->except(['featured_image']) + ['slug' => $slug];

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $this->uploader->upload($request->file('featured_image'), 'articles');
        }
        $status = $data['status'] ?? 'draft';
        if ($status === 'published' && !isset($data['published_at'])) {
            $data['published_at'] = now();
        }
        if ($status === 'scheduled' && isset($data['scheduled_at'])) {
            // keep scheduled_at as-is; published_at will be set when auto-published
            unset($data['published_at']);
        }

        $article = Article::create($data);
        return $this->successResponse($this->format($article->load('category')), 'Article created', 201);
    }

    public function update(Request $request, Article $article) {
        $request->validate([
            'title_hi'       => 'sometimes|string|max:500',
            'category_id'    => 'sometimes|exists:categories,id',
            'status'         => 'nullable|in:draft,published,scheduled',
            'featured_image' => 'nullable|image|max:5120',
        ]);

        $data = $request->except(['featured_image','_method']);
        if ($request->hasFile('featured_image')) {
            $this->uploader->delete($article->featured_image);
            $data['featured_image'] = $this->uploader->upload($request->file('featured_image'), 'articles');
        }
        if (isset($data['status'])) {
            if ($data['status'] === 'published' && !$article->published_at) {
                $data['published_at'] = now();
                $data['scheduled_at'] = null;
            } elseif ($data['status'] === 'scheduled' && isset($data['scheduled_at'])) {
                $data['published_at'] = null;
            }
        }

        $article->update($data);
        return $this->successResponse($this->format($article->fresh()->load('category')), 'Article updated');
    }

    public function destroy(Article $article) {
        $this->uploader->delete($article->featured_image);
        $article->delete();
        return $this->successResponse([], 'Article deleted');
    }

    private function uniqueSlug(string $title): string {
        $base = Str::slug($title) ?: 'article';
        $slug = $base; $i = 1;
        while (Article::where('slug',$slug)->exists()) { $slug = $base.'-'.$i++; }
        return $slug;
    }

    private function format(Article $a): array {
        return [
            'id'                => $a->id,
            'title_hi'          => $a->title_hi,
            'title_en'          => $a->title_en,
            'slug'              => $a->slug,
            'excerpt_hi'        => $a->excerpt_hi,
            'excerpt_en'        => $a->excerpt_en,
            'content_hi'        => $a->content_hi,
            'content_en'        => $a->content_en,
            'featured_image'    => $a->featured_image,
            'featured_image_url'=> $this->imgUrl($a->featured_image),
            'category_id'       => $a->category_id,
            'category_slug'     => $a->category?->slug,
            'category_name_hi'  => $a->category?->name_hi,
            'category_name_en'  => $a->category?->name_en,
            'author_id'         => $a->author_id,
            'author_name'       => $a->author?->name,
            'status'            => $a->status,
            'language'          => $a->language,
            'is_featured'       => $a->is_featured,
            'is_breaking'       => $a->is_breaking,
            'views'             => $a->views,
            'published_at'      => $a->published_at?->toIso8601String(),
            'scheduled_at'      => $a->scheduled_at?->toIso8601String(),
            'created_at'        => $a->created_at?->toIso8601String(),
            'updated_at'        => $a->updated_at?->toIso8601String(),
            'tags'              => $a->relationLoaded('tags') ? $a->tags->pluck('name') : [],
        ];
    }

    private function imgUrl(?string $path): ?string {
        if (!$path) return null;
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return $path;
        return asset('storage/'.$path);
    }
}
