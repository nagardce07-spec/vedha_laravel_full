<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    // GET /admin/authors
    public function index()
    {
        $authors = Author::withCount('books')->latest()->paginate(10);
        return view('admin.authors.index', compact('authors'));
    }

    // POST /admin/authors  (Add Author modal: Name + Image file -> Save)
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $path = $request->hasFile('image') ? $request->file('image')->store('authors', 'public') : null;

        Author::create(['name' => $data['name'], 'image_path' => $path]);

        return back()->with('success', 'Author added.');
    }

    // PUT /admin/authors/{author}
    public function update(Request $request, Author $author)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('authors', 'public');
        }

        $author->update($data);

        return back()->with('success', 'Author updated.');
    }

    // DELETE /admin/authors/{author}
    public function destroy(Author $author)
    {
        $author->delete();
        return back()->with('success', 'Author deleted.');
    }
}
