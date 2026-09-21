<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Model
{
    use HasApiTokens;

    protected $fillable = ['name', 'username', 'email', 'password', 'phone', 'device_type', 'login_type', 'fcm_token'];
    protected $hidden = ['password'];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function likes()
    {
        return $this->hasMany(BookLike::class);
    }

    public function reviews()
    {
        return $this->hasMany(BookReview::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(CustomerSubscription::class);
    }
}
