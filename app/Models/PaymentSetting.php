<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    protected $fillable = ['razorpay_key_id', 'razorpay_key_secret', 'currency', 'test_mode'];
    protected $casts = ['test_mode' => 'boolean'];
    protected $hidden = ['razorpay_key_secret']; // never expose secret key in any API response

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }
}
