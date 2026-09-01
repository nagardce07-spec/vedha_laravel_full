<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookLike;
use App\Models\BookReview;
use App\Models\Category;
use App\Models\Customer;
use App\Models\TrendingBook;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    // GET /admin/dashboard
    public function index()
    {
        $stats = [
            'categories'      => Category::count(),
            'books'           => Book::count(),
            'authors'         => Author::count(),
            'reviews'         => BookReview::count(),
            'trending_books'  => TrendingBook::count(),
            'total_likes'     => BookLike::count(),
            'users'           => Customer::count(),
            'average_rating'  => round(BookReview::avg('rating') ?? 0, 1),
        ];

        // Most popular category = category with the most books.
        $mostPopular = Category::withCount('books')->orderByDesc('books_count')->first();

        // Daily customer registrations for the selected month (defaults to current month).
        $month = request('month') ? Carbon::parse(request('month')) : now();
        $start = $month->copy()->startOfMonth();
        $end   = $month->copy()->endOfMonth();

        $daily = Customer::selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('day')
            ->pluck('total', 'day');

        $chartLabels = [];
        $chartData = [];
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $chartLabels[] = $d->format('M d');
            $chartData[] = $daily[$d->format('Y-m-d')] ?? 0;
        }

        return view('admin.dashboard', compact('stats', 'mostPopular', 'chartLabels', 'chartData', 'month'));
    }
}
