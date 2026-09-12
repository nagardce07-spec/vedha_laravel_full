<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerSubscription;
use App\Models\PaymentSetting;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Razorpay\Api\Api as RazorpayApi;

class SubscriptionController extends Controller
{
    // GET /api/subscription-plans  (public — paywall screen reads this)
    public function plans()
    {
        return SubscriptionPlan::where('is_active', true)->orderBy('position')->get();
    }

    // GET /api/subscriptions/status  (requires auth) — is the logged-in user premium right now?
    public function status(Request $request)
    {
        $active = CustomerSubscription::where('customer_id', $request->user()->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        return response()->json([
            'is_premium' => (bool) $active,
            'expires_at' => $active?->expires_at,
        ]);
    }

    // POST /api/subscriptions/create-order  (requires auth) — step 1 of checkout
    public function createOrder(Request $request)
    {
        $data = $request->validate(['plan_id' => 'required|exists:subscription_plans,id']);
        $plan = SubscriptionPlan::findOrFail($data['plan_id']);
        $settings = PaymentSetting::current()->makeVisible('razorpay_key_secret');

        $api = new RazorpayApi($settings->razorpay_key_id, $settings->razorpay_key_secret);

        $order = $api->order->create([
            'receipt'  => 'plan_'.$plan->id.'_user_'.$request->user()->id.'_'.time(),
            'amount'   => $plan->amount_in_subunits, // paise
            'currency' => $plan->currency,
        ]);

        $subscription = CustomerSubscription::create([
            'customer_id'          => $request->user()->id,
            'subscription_plan_id' => $plan->id,
            'razorpay_order_id'    => $order['id'],
            'status'               => 'pending',
        ]);

        return response()->json([
            'subscription_id' => $subscription->id,
            'order_id'        => $order['id'],
            'amount'          => $plan->amount_in_subunits,
            'currency'        => $plan->currency,
            'key_id'          => $settings->razorpay_key_id, // public key only — safe to expose
            'plan_name'       => $plan->name,
        ]);
    }

    // POST /api/subscriptions/verify  (requires auth) — step 2, after Razorpay checkout succeeds
    public function verify(Request $request)
    {
        $data = $request->validate([
            'subscription_id'   => 'required|exists:customer_subscriptions,id',
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature'  => 'required|string',
        ]);

        $subscription = CustomerSubscription::findOrFail($data['subscription_id']);
        $settings = PaymentSetting::current()->makeVisible('razorpay_key_secret');

        $api = new RazorpayApi($settings->razorpay_key_id, $settings->razorpay_key_secret);

        try {
            // Throws an exception if the signature doesn't match — this is
            // the step that proves the payment is genuine, not spoofed.
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $data['razorpay_order_id'],
                'razorpay_payment_id' => $data['razorpay_payment_id'],
                'razorpay_signature'  => $data['razorpay_signature'],
            ]);
        } catch (\Exception $e) {
            $subscription->update(['status' => 'failed']);
            return response()->json(['verified' => false, 'message' => 'Signature verification failed.'], 422);
        }

        $plan = $subscription->plan;
        $startsAt = now();
        $expiresAt = $startsAt->copy()->addDays($plan->duration_days);

        $subscription->update([
            'razorpay_payment_id' => $data['razorpay_payment_id'],
            'razorpay_signature'  => $data['razorpay_signature'],
            'status'              => 'active',
            'starts_at'           => $startsAt,
            'expires_at'          => $expiresAt,
        ]);

        return response()->json([
            'verified'   => true,
            'is_premium' => true,
            'expires_at' => $expiresAt,
        ]);
    }
}
