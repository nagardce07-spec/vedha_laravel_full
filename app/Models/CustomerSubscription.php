<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerSubscription extends Model
{
    protected $fillable = [
        'customer_id', 'subscription_plan_id', 'razorpay_order_id',
        'razorpay_payment_id', 'razorpay_signature', 'status', 'starts_at', 'expires_at',
    ];

    protected $casts = [
        'starts_at'  => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function plan()     { return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id'); }

    public function isCurrentlyActive(): bool
    {
        return $this->status === 'active' && $this->expires_at && $this->expires_at->isFuture();
    }
}
