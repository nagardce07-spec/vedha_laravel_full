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

        // Include the stored file path in the version key so replacing audio always
        // produces a new URL, even when updated_at has only second-level precision.
        return $this->resource_path
            ? url('/api/audio/chapters/' . $this->id) . '?v=' . substr(sha1($this->resource_path . '|' . ($this->updated_at?->format('U.u') ?? '0')), 0, 16)
            : null;
    }
}
