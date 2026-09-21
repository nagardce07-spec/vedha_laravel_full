<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Playlist;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    // GET /api/playlists  (requires auth) — the logged-in customer's playlists,
    // each with book_ids so the app can show checkmarks for "already added".
    public function index(Request $request)
    {
        $playlists = Playlist::where('customer_id', $request->user()->id)
            ->withCount('books')
            ->with(['books:id'])
            ->latest()
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'books_count' => $p->books_count,
                    'book_ids' => $p->books->pluck('id'),
                ];
            });

        return response()->json($playlists);
    }

    // GET /api/playlists/{playlist}  — full book details for one playlist.
    public function show(Request $request, Playlist $playlist)
    {
        abort_unless($playlist->customer_id === $request->user()->id, 403);
        $playlist->load(['books.category', 'books.author']);
        return response()->json($playlist);
    }

    // POST /api/playlists  (name) -> creates a new empty playlist
    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:255']);

        $playlist = Playlist::create([
            'customer_id' => $request->user()->id,
            'name' => $data['name'],
        ]);

        return response()->json($playlist, 201);
    }

    public function destroy(Request $request, Playlist $playlist)
    {
        abort_unless($playlist->customer_id === $request->user()->id, 403);
        $playlist->delete();
        return response()->json(['message' => 'Playlist deleted.']);
    }

    // POST /api/playlists/{playlist}/toggle-book  {book_id} -> adds if absent, removes if present
    public function toggleBook(Request $request, Playlist $playlist)
    {
        abort_unless($playlist->customer_id === $request->user()->id, 403);

        $data = $request->validate(['book_id' => 'required|exists:books,id']);

        if ($playlist->books()->where('book_id', $data['book_id'])->exists()) {
            $playlist->books()->detach($data['book_id']);
            return response()->json(['added' => false]);
        }

        $playlist->books()->attach($data['book_id']);
        return response()->json(['added' => true]);
    }
}
