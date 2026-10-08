<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookLike;

class BookLikeController extends Controller
{
    // GET /admin/likes  (read-only table)
    public function index()
    {
        $likes = BookLike::with('book')->latest('liked_at')->paginate(10);
        return view('admin.likes.index', compact('likes'));
    }
}
