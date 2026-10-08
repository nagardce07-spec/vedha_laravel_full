<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookSuggestion;

class BookSuggestionController extends Controller
{
    // GET /admin/book-suggestions
    public function index()
    {
        $suggestions = BookSuggestion::latest()->paginate(10);
        return view('admin.suggestions.index', compact('suggestions'));
    }

    // Optional: convert an approved suggestion straight into a real Book draft.
    public function approve(BookSuggestion $suggestion)
    {
        Book::create([
            'name'        => $suggestion->book_name,
            'description' => $suggestion->description,
            'is_visible'  => false, // stays hidden until admin finishes uploading cover/audio
        ]);

        return back()->with('success', 'Draft book created from suggestion.');
    }

    // DELETE /admin/book-suggestions/{suggestion}
    public function destroy(BookSuggestion $suggestion)
    {
        $suggestion->delete();
        return back()->with('success', 'Suggestion removed.');
    }
}
