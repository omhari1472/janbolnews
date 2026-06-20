<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\BreakingNews;
use Illuminate\Http\Request;

class AdminBreakingController extends Controller {
    use ApiResponse;

    public function index() {
        return $this->successResponse(['breaking' => BreakingNews::orderBy('sort_order')->get()]);
    }

    public function store(Request $request) {
        $request->validate(['text_hi' => 'required|string|max:500']);
        $item = BreakingNews::create($request->only(['text_hi','text_en','url','is_active','sort_order']));
        return $this->successResponse($item, 'Breaking news created', 201);
    }

    public function update(Request $request, BreakingNews $breaking) {
        $breaking->update($request->only(['text_hi','text_en','url','is_active','sort_order']));
        return $this->successResponse($breaking, 'Updated');
    }

    public function destroy(Request $request, BreakingNews $breaking) {
        if ($request->user()->role !== 'super')
            return $this->errorResponse('Only superadmin can delete breaking news.', 403);
        $breaking->delete();
        return $this->successResponse([], 'Deleted');
    }

    public function toggle(BreakingNews $breaking) {
        $breaking->update(['is_active' => !$breaking->is_active]);
        return $this->successResponse($breaking, 'Toggled');
    }
}
