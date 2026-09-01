<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'email', 'device_type', 'login_type', 'fcm_token'];

    public function likes()
    {
        return $this->hasMany(BookLike::class);
    }

    public function reviews()
    {
        return $this->hasMany(BookReview::class);
    }
}
