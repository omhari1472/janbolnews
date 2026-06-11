<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Author;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class AdminAuthorController extends Controller {
    use ApiResponse;
    public function __construct(private FileUploadService $uploader) {}

    public function index() {
        return $this->successResponse(Author::orderBy('name')->get()->map(fn($a) => $this->fmt($a)));
    }

    public function store(Request $request) {
        $request->validate(['name' => 'required|string|max:200']);
        $data = $request->except('avatar');
        if ($request->hasFile('avatar')) {
            $data['avatar_path'] = $this->uploader->upload($request->file('avatar'), 'authors');
        }
        return $this->successResponse($this->fmt(Author::create($data)), 'Author created', 201);
    }

    public function update(Request $request, Author $author) {
        $data = $request->except(['avatar','_method']);
        if ($request->hasFile('avatar')) {
            $this->uploader->delete($author->avatar_path);
            $data['avatar_path'] = $this->uploader->upload($request->file('avatar'), 'authors');
        }
        $author->update($data);
        return $this->successResponse($this->fmt($author->fresh()), 'Author updated');
    }

    public function destroy(Author $author) {
        $this->uploader->delete($author->avatar_path);
        $author->delete();
        return $this->successResponse([], 'Author deleted');
    }

    private function fmt(Author $a): array {
        return array_merge($a->toArray(), [
            'avatar_url' => $a->avatar_path ? asset('storage/'.$a->avatar_path) : null,
        ]);
    }
}
