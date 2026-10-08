<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['slug', 'title', 'content'];

    public static function bySlug(string $slug, string $title): self
    {
        return static::firstOrCreate(['slug' => $slug], ['title' => $title]);
    }
}
