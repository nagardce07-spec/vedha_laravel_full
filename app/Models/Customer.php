<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Model
{
    use HasApiTokens;

    protected $fillable = ['name', 'username', 'email', 'password', 'phone', 'dob', 'gender', 'device_type', 'login_type', 'fcm_token', 'last_active_at'];
    protected $hidden = ['password'];

    protected $casts = [
        'password' => 'hashed',
        'last_active_at' => 'datetime',
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

    public function testBatchAccesses() { return $this->hasMany(TestBatchAccess::class); }
    public function testAttempts() { return $this->hasMany(TestAttempt::class); }
}
