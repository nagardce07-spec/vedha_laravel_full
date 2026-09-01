<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\TrendingBook;
use Illuminate\Http\Request;

class TrendingBookController extends Controller
{
    // GET /admin/trending-books
    public function index()
    {
        $trending = TrendingBook::with('book.author')->orderBy('position')->get();
        return view('admin.trending.index', compact('trending'));
    }

    // GET /admin/trending-books/search?q=...  (the "Add Trending Book" search panel)
    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $alreadyTrendingIds = TrendingBook::pluck('book_id');

        $books = Book::with('author')
            ->whereNotIn('id', $alreadyTrendingIds)
            ->when($q, fn ($query) => $query->where('name', 'like', "%{$q}%")
                ->orWhereHas('author', fn ($a) => $a->where('name', 'like', "%{$q}%")))
            ->limit(16)
            ->get();

        return response()->json($books);
    }

    // POST /admin/trending-books  ("Add to Trending" button on a search result card)
    public function store(Request $request)
    {
        $data = $request->validate(['book_id' => 'required|exists:books,id']);

        $nextPosition = (TrendingBook::max('position') ?? 0) + 1;

        TrendingBook::firstOrCreate(
            ['book_id' => $data['book_id']],
            ['position' => $nextPosition]
        );

        return back()->with('success', 'Added to trending.');
    }

    // PATCH /admin/trending-books/reorder  (drag-and-drop handle re-order)
    public function reorder(Request $request)
    {
        $data = $request->validate(['order' => 'required|array', 'order.*' => 'exists:trending_books,id']);

        foreach ($data['order'] as $index => $id) {
            TrendingBook::where('id', $id)->update(['position' => $index + 1]);
        }

        return response()->json(['ok' => true]);
    }

    // DELETE /admin/trending-books/{trendingBook}
    public function destroy(TrendingBook $trendingBook)
    {
        $trendingBook->delete();
        return back()->with('success', 'Removed from trending.');
    }
}
