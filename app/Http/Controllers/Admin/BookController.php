<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use getID3; // composer require james-heinrich/getid3  (used to auto-detect audio duration)

class BookController extends Controller
{
    // GET /admin/books
    public function index()
    {
        $books = Book::with(['category', 'author'])->latest()->paginate(10);
        $categories = Category::orderBy('name')->get();
        $authors = Author::orderBy('name')->get();

        return view('admin.books.index', compact('books', 'categories', 'authors'));
    }

    public function create()
    {
        return response()->json([
            'categories' => Category::orderBy('name')->get(),
            'authors'    => Author::orderBy('name')->get(),
        ]);
    }

    // POST /admin/books  (Add Book modal -> Save)
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'image'         => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'category_id'   => 'required|exists:categories,id',
            'author_id'     => 'required|exists:authors,id',
            'description'   => 'nullable|string',
            'type'          => 'required|in:chapter,full',
            'upload_type'   => 'required_if:type,full|in:url,file',
            'resource_url'  => 'nullable|url',
            'resource_file' => 'nullable|file|mimes:mp3,m4a,wav|max:512000',
            'duration_min'  => 'nullable|integer|min:0',
            'duration_sec'  => 'nullable|integer|min:0|max:59',
        ]);

        $book = new Book();
        $book->name          = $data['name'];
        $book->image_path    = $request->file('image')->store('books/covers', 'public');
        $book->category_id   = $data['category_id'];
        $book->author_id     = $data['author_id'];
        $book->description   = $data['description'] ?? null;
        $book->type          = $data['type'];

        if ($data['type'] === 'full') {
            $book->upload_type = $data['upload_type'];

            if ($data['upload_type'] === 'file' && $request->hasFile('resource_file')) {
                $file = $request->file('resource_file');
                $book->resource_path = $file->store('books/audio', 'public');
                $book->duration = $this->detectDuration($file->getRealPath())
                    ?? $this->manualDuration($data);
            } else {
                $book->resource_url = $data['resource_url'] ?? null;
                $book->duration = $this->manualDuration($data);
            }
        }

        $book->save();

        return back()->with('success', 'Book uploaded.');
    }

    // PUT /admin/books/{book}  (Edit Book modal -> Update)
    public function update(Request $request, Book $book)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'category_id' => 'required|exists:categories,id',
            'author_id'   => 'required|exists:authors,id',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('books/covers', 'public');
        }

        $book->update($data);

        return back()->with('success', 'Book updated.');
    }

    // DELETE /admin/books/{book}
    public function destroy(Book $book)
    {
        $book->delete();
        return back()->with('success', 'Book deleted.');
    }

    // Toggle switches on the list row: visibility / premium / featured.
    public function toggle(Request $request, Book $book, string $field)
    {
        abort_unless(in_array($field, ['is_visible', 'is_premium', 'is_featured']), 404);
        $book->update([$field => $request->boolean('value')]);
        return response()->json(['ok' => true]);
    }

    // --- Chapter management (the "+" button opens this sub-resource) ---

    // GET /admin/books/{book}/chapters
    public function chapters(Book $book)
    {
        $chapters = $book->chapters;
        return view('admin.books.chapters', compact('book', 'chapters'));
    }

    // POST /admin/books/{book}/chapters  (Add Chapter modal -> Save)
    public function storeChapter(Request $request, Book $book)
    {
        $data = $request->validate([
            'title'            => 'nullable|string|max:255',
            'chapter_number'   => 'required|integer|min:1',
            'upload_type'      => 'required|in:url,file',
            'resource_url'     => 'nullable|url',
            'resource_file'    => 'nullable|file|mimes:mp3,m4a,wav|max:512000',
            'duration_min'     => 'nullable|integer|min:0',
            'duration_sec'     => 'nullable|integer|min:0|max:59',
        ]);

        $chapter = new \App\Models\BookChapter();
        $chapter->book_id         = $book->id;
        $chapter->title           = $data['title'] ?? ('Chapter ' . $data['chapter_number']);
        $chapter->chapter_number  = $data['chapter_number'];
        $chapter->upload_type     = $data['upload_type'];

        if ($data['upload_type'] === 'file' && $request->hasFile('resource_file')) {
            $file = $request->file('resource_file');
            $chapter->resource_path = $file->store('books/audio/chapters', 'public');
            $chapter->duration = $this->detectDuration($file->getRealPath()) ?? $this->manualDuration($data);
        } else {
            $chapter->resource_url = $data['resource_url'] ?? null;
            $chapter->duration = $this->manualDuration($data);
        }

        $chapter->save();

        return back()->with('success', 'Chapter added.');
    }

    // PUT /admin/books/{book}/chapters/{chapter}  (Edit Chapter modal -> Update)
    public function updateChapter(Request $request, Book $book, \App\Models\BookChapter $chapter)
    {
        $data = $request->validate([
            'title'          => 'nullable|string|max:255',
            'chapter_number' => 'required|integer|min:1',
            'upload_type'    => 'required|in:url,file',
            'resource_url'   => 'nullable|url',
            'resource_file'  => 'nullable|file|mimes:mp3,m4a,wav|max:512000',
            'duration_min'   => 'nullable|integer|min:0',
            'duration_sec'   => 'nullable|integer|min:0|max:59',
        ]);

        $chapter->title          = $data['title'] ?? $chapter->title;
        $chapter->chapter_number = $data['chapter_number'];
        $chapter->upload_type    = $data['upload_type'];

        if ($data['upload_type'] === 'file' && $request->hasFile('resource_file')) {
            $file = $request->file('resource_file');
            $chapter->resource_path = $file->store('books/audio/chapters', 'public');
            $chapter->duration = $this->detectDuration($file->getRealPath()) ?? $this->manualDuration($data);
        } elseif ($data['upload_type'] === 'url') {
            $chapter->resource_url = $data['resource_url'] ?? $chapter->resource_url;
            $chapter->duration = $this->manualDuration($data);
        }

        $chapter->save();

        return back()->with('success', 'Chapter updated.');
    }

    // DELETE /admin/books/{book}/chapters/{chapter}
    public function destroyChapter(Book $book, \App\Models\BookChapter $chapter)
    {
        $chapter->delete();
        return back()->with('success', 'Chapter deleted.');
    }

    // --- helpers ---

    private function manualDuration(array $data): ?string
    {
        if (!isset($data['duration_min']) && !isset($data['duration_sec'])) return null;
        $min = str_pad((string) ($data['duration_min'] ?? 0), 2, '0', STR_PAD_LEFT);
        $sec = str_pad((string) ($data['duration_sec'] ?? 0), 2, '0', STR_PAD_LEFT);
        return "{$min}:{$sec}";
    }

    private function detectDuration(string $absolutePath): ?string
    {
        if (!class_exists(getID3::class)) return null;

        $getID3 = new getID3();
        $info = $getID3->analyze($absolutePath);
        $seconds = (int) round($info['playtime_seconds'] ?? 0);
        if ($seconds <= 0) return null;

        return sprintf('%02d:%02d', intdiv($seconds, 60), $seconds % 60);
    }
}
