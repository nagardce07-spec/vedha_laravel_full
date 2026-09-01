<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // GET /admin/categories
    public function index()
    {
        $categories = Category::latest()->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    // POST /admin/categories  (Add Category modal -> Save)
    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        Category::create($data);
        return back()->with('success', 'Category added.');
    }

    // PUT /admin/categories/{category}  (pencil icon -> edit modal -> Save)
    public function update(Request $request, Category $category)
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        $category->update($data);
        return back()->with('success', 'Category updated.');
    }

    // DELETE /admin/categories/{category}  (trash icon)
    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }
}
