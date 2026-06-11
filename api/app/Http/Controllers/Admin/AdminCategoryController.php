<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller {
    use ApiResponse;

    public function index() {
        return $this->successResponse(Category::orderBy('sort_order')->get());
    }

    public function store(Request $request) {
        $request->validate(['name_hi'=>'required','name_en'=>'required']);
        $slug = Str::slug($request->name_en ?: $request->name_hi);
        $cat  = Category::create($request->all() + ['slug' => $slug]);
        return $this->successResponse($cat, 'Category created', 201);
    }

    public function update(Request $request, Category $category) {
        $category->update($request->all());
        return $this->successResponse($category, 'Category updated');
    }

    public function destroy(Category $category) {
        $category->delete();
        return $this->successResponse([], 'Category deleted');
    }
}
