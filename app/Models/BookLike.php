<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookLike extends Model
{
    protected $fillable = ['book_id', 'customer_id', 'user_name', 'liked_at'];
    protected $casts = ['liked_at' => 'datetime'];

    public function book()     { return $this->belongsTo(Book::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
}
