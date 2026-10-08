# Adding Razorpay Payments to Your Live Backend

Your Railway backend is already live and working. This zip contains the
**complete project again** (with Razorpay added in), so the simplest path is
to replace your GitHub repo's contents entirely with this zip — same process
you already did once.

## What's new in this package

- `database/migrations/2026_02_01_*` — 3 new tables: `subscription_plans`,
  `payment_settings`, `customer_subscriptions`
- `app/Models/SubscriptionPlan.php`, `PaymentSetting.php`, `CustomerSubscription.php`
- `app/Http/Controllers/Admin/SubscriptionPlanController.php` — admin CRUD for plans
- `app/Http/Controllers/Admin/PaymentSettingController.php` — admin Razorpay key entry
- `app/Http/Controllers/Api/SubscriptionController.php` — order creation + payment
  verification the Flutter app calls
- `resources/views/admin/subscriptionplans/index.blade.php`
- `resources/views/admin/paymentsettings/edit.blade.php`
- Sidebar now has **💳 Subscription Plans** and **🏦 Payment Settings** links
- `composer.json` now requires `razorpay/razorpay`

## Deploy steps

1. Delete everything in your GitHub repo, upload every file from this zip
   (exactly like the first time).
2. Commit. Railway auto-redeploys and runs `composer install`, which will
   now also pull in the Razorpay PHP SDK.
3. Your Custom Start Command (Settings → Deploy) stays the same — it already
   runs `migrate --force`, which will pick up the 3 new tables automatically.
4. Log into the admin panel → **Payment Settings** → enter your Razorpay
   **Key ID** and **Key Secret**. Get these from
   [Razorpay Dashboard → Settings → API Keys](https://dashboard.razorpay.com/app/keys).
   Start with **Test Mode** checked so you can test with fake cards first.
5. Go to **Subscription Plans** → **Add Plan** → create e.g.:
   - Name: `Monthly`, Price: `299`, Duration: `30` days
   - Name: `Yearly`, Price: `2499`, Duration: `365` days, check "Best Value"

## How it works (payment flow)

1. Flutter app calls `GET /api/subscription-plans` → shows your plans on the paywall
2. User taps a plan → Flutter calls `POST /api/subscriptions/create-order`
   → backend creates a Razorpay order, returns `order_id` + `key_id`
3. Flutter opens Razorpay's checkout UI (card/UPI/netbanking) with those details
4. On success, Flutter sends the payment result to `POST /api/subscriptions/verify`
   → backend verifies the cryptographic signature (proves it's not spoofed),
   marks the subscription **active**, sets `expires_at` based on plan duration
5. Flutter calls `GET /api/subscriptions/status` anytime to check if the
   logged-in user is currently premium

No RevenueCat, no App Store/Play Store account needed — everything runs
through your own Razorpay account and your own backend.

## Test mode cards (for testing before going live)

Razorpay test mode accepts these without charging real money:
- Card: `4111 1111 1111 1111`, any future expiry, any CVV
- Full list: https://razorpay.com/docs/payments/payments/test-card-upi-details/

## Going live

Once tested, go to Razorpay Dashboard → switch to **Live Mode**, generate
live API keys, paste them into admin panel → Payment Settings, and uncheck
"Test Mode". Real payments will now be charged.
