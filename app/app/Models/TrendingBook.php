<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrendingBook extends Model
{
    protected $fillable = ['book_id', 'position'];

    public function book() { return $this->belongsTo(Book::class); }
}
