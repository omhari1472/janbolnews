<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Article;
use App\Models\Epaper;
use Illuminate\Http\Request;

class AdminEpaperController extends Controller {
    use ApiResponse;

    public function index() {
        $items = Epaper::withCount('articles')->orderByDesc('edition_date')->get()
            ->map(fn($e) => $this->fmt($e));
        return $this->successResponse(['items' => $items]);
    }

    public function show(Epaper $epaper) {
        $epaper->load(['articles.category']);
        return $this->successResponse($this->fmt($epaper, true));
    }

    public function store(Request $request) {
        $request->validate([
            'title'        => 'required|string|max:300',
            'edition_date' => 'required|date',
            'status'       => 'nullable|in:draft,published',
        ]);
        $epaper = Epaper::create($request->only(['title','edition_date','edition','status']));
        $this->syncArticles($epaper, $request->input('article_ids', []));
        $epaper->load(['articles.category']);
        return $this->successResponse($this->fmt($epaper, true), 'Edition created', 201);
    }

    public function update(Request $request, Epaper $epaper) {
        $request->validate([
            'title'        => 'sometimes|string|max:300',
            'edition_date' => 'sometimes|date',
            'status'       => 'nullable|in:draft,published',
        ]);
        $epaper->update($request->only(['title','edition_date','edition','status']));
        if ($request->has('article_ids')) {
            $this->syncArticles($epaper, $request->input('article_ids', []));
        }
        $epaper->load(['articles.category']);
        return $this->successResponse($this->fmt($epaper->fresh()->load(['articles.category']), true), 'Updated');
    }

    public function destroy(Request $request, Epaper $epaper) {
        if ($request->user()->role !== 'super')
            return $this->errorResponse('Only superadmin can delete editions.', 403);
        $epaper->articles()->detach();
        $epaper->delete();
        return $this->successResponse([], 'Deleted');
    }

    private function syncArticles(Epaper $epaper, array $ids): void {
        $sync = [];
        foreach (array_values($ids) as $pos => $id) {
            $sync[(int)$id] = ['position' => $pos];
        }
        $epaper->articles()->sync($sync);
    }

    private function fmt(Epaper $e, bool $withArticles = false): array {
        $data = [
            'id'            => $e->id,
            'title'         => $e->title,
            'edition_date'  => $e->edition_date?->toDateString(),
            'edition'       => $e->edition,
            'status'        => $e->status ?? 'draft',
            'article_count' => $e->articles_count ?? $e->articles->count(),
            'created_at'    => $e->created_at?->toIso8601String(),
        ];
        if ($withArticles) {
            $data['articles'] = $e->articles->map(fn($a) => [
                'id'          => $a->id,
                'title_hi'    => $a->title_hi,
                'title_en'    => $a->title_en,
                'category'    => $a->category?->name_hi,
                'status'      => $a->status,
                'position'    => $a->pivot->position,
            ])->values();
        }
        return $data;
    }
}
