<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Author extends Model
{
    protected $fillable = ['name', 'image_path'];
    protected $appends = ['image_url', 'books_count'];

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }

    public function getBooksCountAttribute(): int
    {
        return $this->books()->count();
    }
}
