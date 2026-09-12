<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookReview;
use Illuminate\Http\Request;

class BookReviewController extends Controller
{
    // GET /admin/reviews
    public function index()
    {
        $reviews = BookReview::with('book')->latest()->paginate(10);
        return view('admin.reviews.index', compact('reviews'));
    }

    // PUT /admin/reviews/{review}  (pencil icon -> edit rating/text)
    public function update(Request $request, BookReview $review)
    {
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);
        $review->update($data);
        return back()->with('success', 'Review updated.');
    }

    // DELETE /admin/reviews/{review}
    public function destroy(BookReview $review)
    {
        $review->delete();
        return back()->with('success', 'Review deleted.');
    }
}
