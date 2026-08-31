<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BookChapter extends Model
{
    protected $fillable = [
        'book_id', 'chapter_number', 'title', 'upload_type',
        'resource_path', 'resource_url', 'duration',
    ];

    protected $appends = ['resource_full_url'];

    public function book() { return $this->belongsTo(Book::class); }

    public function getResourceFullUrlAttribute(): ?string
    {
        if ($this->upload_type === 'url') return $this->resource_url;
        return $this->resource_path ? Storage::url($this->resource_path) : null;
    }
}
