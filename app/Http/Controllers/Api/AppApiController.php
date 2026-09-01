<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookLike;
use App\Models\BookReview;
use App\Models\BookSuggestion;
use App\Models\Category;
use App\Models\OnboardingScreen;
use App\Models\TrendingBook;
use Illuminate\Http\Request;

class AppApiController extends Controller
{
    // GET /api/app-info  (logo, title, theme colors — used by the Flutter app's splash/home screens)
    public function appInfo()
    {
        $general = \App\Models\GeneralSetting::current();
        $theme = \App\Models\AppSetting::current();

        return response()->json([
            'title'                  => $general->title,
            'logo_url'               => $general->logo_light_url,
            'favicon_url'            => $general->favicon_url,
            'theme_color'            => $theme->theme_color,
            'theme_light_color'      => $theme->theme_light_color,
            'theme_background_color' => $theme->theme_background_color,
        ]);
    }

    // GET /api/onboarding-screens
    public function onboarding()
    {
        return OnboardingScreen::orderBy('position')->get();
    }

    // GET /api/categories
    public function categories()
    {
        return Category::orderBy('name')->get();
    }

    // GET /api/authors
    public function authors()
    {
        return Author::withCount('books')->orderBy('name')->get();
    }

    // GET /api/authors/{author}
    public function authorDetail(Author $author)
    {
        $author->load('books.category');
        return $author;
    }

    // GET /api/books?category_id=&featured=1
    public function books(Request $request)
    {
        $query = Book::with(['category', 'author'])->where('is_visible', true);

        if ($request->filled('category_id')) $query->where('category_id', $request->category_id);
        if ($request->boolean('featured'))    $query->where('is_featured', true);

        return $query->latest()->paginate(20);
    }

    // GET /api/books/{book}
    public function bookDetail(Book $book)
    {
        $book->increment('views');
        $book->load(['category', 'author', 'chapters', 'reviews']);
        return $book;
    }

    // GET /api/trending-books
    public function trending()
    {
        return TrendingBook::with('book.author')->orderBy('position')->get()->pluck('book');
    }

    // POST /api/books/{book}/reviews
    public function storeReview(Request $request, Book $book)
    {
        $data = $request->validate([
            'rating'    => 'required|integer|min:1|max:5',
            'review'    => 'nullable|string',
            'user_name' => 'required|string|max:255',
        ]);

        $data['customer_id'] = $request->user()?->id;
        $review = $book->reviews()->create($data);

        return response()->json($review, 201);
    }

    // POST /api/books/{book}/like  (toggle)
    public function toggleLike(Request $request, Book $book)
    {
        $customerId = $request->user()?->id;
        $userName   = $request->input('user_name');

        $existing = BookLike::where('book_id', $book->id)
            ->when($customerId, fn ($q) => $q->where('customer_id', $customerId))
            ->when(!$customerId, fn ($q) => $q->where('user_name', $userName))
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['liked' => false]);
        }

        BookLike::create(['book_id' => $book->id, 'customer_id' => $customerId, 'user_name' => $userName]);
        return response()->json(['liked' => true]);
    }

    // GET /api/liked-books  (requires auth:sanctum) — the logged-in user's liked list
    public function likedBooks(Request $request)
    {
        $customerId = $request->user()->id;
        $bookIds = BookLike::where('customer_id', $customerId)->pluck('book_id');
        return Book::whereIn('id', $bookIds)->with(['category', 'author'])->get();
    }

    // POST /api/book-suggestions
    public function storeSuggestion(Request $request)
    {
        $data = $request->validate([
            'book_name'   => 'required|string|max:255',
            'author_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'suggested_by'=> 'nullable|string|max:255',
        ]);

        $data['customer_id'] = $request->user()?->id;
        BookSuggestion::create($data);

        return response()->json(['message' => 'Thanks! Your suggestion was received.'], 201);
    }
}
