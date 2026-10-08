<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    protected $fillable = ['customer_id', 'name'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function books()
    {
        return $this->belongsToMany(Book::class, 'playlist_books')->withTimestamps();
    }
}
