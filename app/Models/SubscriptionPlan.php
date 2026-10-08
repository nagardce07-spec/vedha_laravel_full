<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name', 'price', 'currency', 'duration_days',
        'description', 'is_best_value', 'is_active', 'position',
    ];

    protected $casts = [
        'is_best_value' => 'boolean',
        'is_active'     => 'boolean',
        'price'         => 'decimal:2',
    ];

    // Price in the smallest currency unit (paise for INR) — Razorpay requires this.
    public function getAmountInSubunitsAttribute(): int
    {
        return (int) round($this->price * 100);
    }
}
