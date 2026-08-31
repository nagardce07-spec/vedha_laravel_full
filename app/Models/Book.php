<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    protected $fillable = [
        'name', 'image_path', 'category_id', 'author_id', 'description',
        'type', 'upload_type', 'resource_path', 'resource_url', 'duration',
        'views', 'is_visible', 'is_premium', 'is_featured',
    ];

    protected $casts = [
        'is_visible'  => 'boolean',
        'is_premium'  => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected $appends = ['image_url', 'resource_full_url', 'likes_count', 'average_rating', 'reviews_count'];

    public function category() { return $this->belongsTo(Category::class); }
    public function author()   { return $this->belongsTo(Author::class); }
    public function chapters() { return $this->hasMany(BookChapter::class)->orderBy('chapter_number'); }
    public function reviews()  { return $this->hasMany(BookReview::class); }
    public function likes()    { return $this->hasMany(BookLike::class); }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }

    // Full audiobook resource — either the uploaded file or the external URL.
    public function getResourceFullUrlAttribute(): ?string
    {
        if ($this->upload_type === 'url') return $this->resource_url;
        return $this->resource_path ? Storage::url($this->resource_path) : null;
    }

    public function getLikesCountAttribute(): int
    {
        return $this->likes()->count();
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }
}
