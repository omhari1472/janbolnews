<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Epaper;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class AdminEpaperController extends Controller {
    use ApiResponse;
    public function __construct(private FileUploadService $uploader) {}

    public function index() {
        return $this->successResponse(['items' => Epaper::orderByDesc('edition_date')->get()->map(fn($e) => $this->fmt($e))]);
    }

    public function store(Request $request) {
        $request->validate([
            'title'        => 'required|string|max:300',
            'edition_date' => 'required|date',
            'pdf'          => 'nullable|mimes:pdf|max:30720',
            'thumbnail'    => 'nullable|image|max:5120',
        ]);
        $data = $request->except(['pdf','thumbnail']);
        if ($request->hasFile('pdf'))       $data['pdf_path']       = $this->uploader->upload($request->file('pdf'), 'epapers');
        if ($request->hasFile('thumbnail')) $data['thumbnail_path'] = $this->uploader->upload($request->file('thumbnail'), 'epapers');
        return $this->successResponse($this->fmt(Epaper::create($data)), 'E-paper created', 201);
    }

    public function update(Request $request, Epaper $epaper) {
        $data = $request->except(['pdf','thumbnail','_method']);
        if ($request->hasFile('pdf')) {
            $this->uploader->delete($epaper->pdf_path);
            $data['pdf_path'] = $this->uploader->upload($request->file('pdf'), 'epapers');
        }
        if ($request->hasFile('thumbnail')) {
            $this->uploader->delete($epaper->thumbnail_path);
            $data['thumbnail_path'] = $this->uploader->upload($request->file('thumbnail'), 'epapers');
        }
        $epaper->update($data);
        return $this->successResponse($this->fmt($epaper->fresh()), 'Updated');
    }

    public function destroy(Epaper $epaper) {
        $this->uploader->delete($epaper->pdf_path);
        $this->uploader->delete($epaper->thumbnail_path);
        $epaper->delete();
        return $this->successResponse([], 'Deleted');
    }

    private function fmt(Epaper $e): array {
        return array_merge($e->toArray(), [
            'pdf_url'       => $this->imgUrl($e->pdf_path),
            'thumbnail_url' => $this->imgUrl($e->thumbnail_path),
        ]);
    }

    private function imgUrl(?string $path): ?string {
        if (!$path) return null;
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return $path;
        return asset('storage/'.$path);
    }
}
