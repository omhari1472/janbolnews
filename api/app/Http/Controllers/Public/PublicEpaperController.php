<?php
namespace App\Http\Controllers\Public;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Epaper;
use Illuminate\Http\Request;

class PublicEpaperController extends Controller {
    use ApiResponse;

    public function index(Request $request) {
        $limit = min((int)($request->limit ?? 12), 50);
        $items = Epaper::where('is_active', true)
            ->orderByDesc('edition_date')
            ->paginate($limit);

        return $this->successResponse([
            'items' => $items->map(fn($e) => $this->fmt($e)),
            'total' => $items->total(),
        ]);
    }

    public function show(Epaper $epaper) {
        abort_if(!$epaper->is_active, 404);
        return $this->successResponse($this->fmt($epaper));
    }

    private function fmt(Epaper $e): array {
        return [
            'id'            => $e->id,
            'title'         => $e->title,
            'edition_date'  => $e->edition_date?->toDateString(),
            'edition'       => $e->edition,
            'pdf_url'       => $e->pdf_path       ? asset('storage/'.$e->pdf_path)       : null,
            'thumbnail_url' => $e->thumbnail_path  ? asset('storage/'.$e->thumbnail_path) : null,
            'is_active'     => $e->is_active,
            'created_at'    => $e->created_at?->toIso8601String(),
        ];
    }
}
