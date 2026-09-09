<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomerAuthController extends Controller
{
    // POST /api/register
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:customers,email',
            'password' => 'required|string|min:6',
            'phone'    => 'nullable|string|max:20',
        ]);

        $customer = Customer::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'phone'      => $data['phone'] ?? null,
            'login_type' => 'Email',
        ]);

        $token = $customer->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'customer' => $customer,
            'token'    => $token,
        ], 201);
    }

    // POST /api/login
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $customer = Customer::where('email', $data['email'])->first();

        if (!$customer || !$customer->password || !Hash::check($data['password'], $customer->password)) {
            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our records.'],
            ]);
        }

        $token = $customer->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'customer' => $customer,
            'token'    => $token,
        ]);
    }

    // POST /api/social-login — Google / Apple sign-in. Finds-or-creates the
    // customer by email (the actual Google/Apple token verification happens
    // client-side via the google_sign_in / sign_in_with_apple SDKs; this
    // endpoint trusts the verified email+name the app already obtained).
    public function socialLogin(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email',
            'provider' => 'required|in:Google,Apple',
        ]);

        $customer = Customer::firstOrCreate(
            ['email' => $data['email']],
            ['name' => $data['name'], 'login_type' => $data['provider']]
        );

        // If they'd previously registered with email/password and are now
        // linking a social login, keep their existing name but note the login type used this time.
        $customer->update(['login_type' => $data['provider']]);

        $token = $customer->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'customer' => $customer,
            'token'    => $token,
        ]);
    }

    // POST /api/logout  (requires auth:sanctum)
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out.']);
    }

    // GET /api/me  (requires auth:sanctum) — the logged-in customer's own profile
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    // PUT /api/me  (requires auth:sanctum) — edit name/phone
    public function updateMe(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $request->user()->update($data);

        return response()->json($request->user());
    }

    // POST /api/forgot-password — emails a 6-digit reset code.
    public function forgotPassword(Request $request)
    {
        $data = $request->validate(['email' => 'required|email']);

        $customer = Customer::where('email', $data['email'])->first();

        // Always respond success even if the email isn't registered —
        // avoids leaking which emails have accounts.
        if (!$customer) {
            return response()->json(['message' => 'If that email exists, a reset code has been sent.']);
        }

        $code = (string) random_int(100000, 999999);

        \App\Models\CustomerPasswordReset::updateOrCreate(
            ['email' => $customer->email],
            ['code' => $code, 'expires_at' => now()->addMinutes(15)]
        );

        try {
            \Illuminate\Support\Facades\Mail::raw(
                "Your Vedha AudioBooks password reset code is: {$code}\n\nThis code expires in 15 minutes.",
                function ($message) use ($customer) {
                    $message->to($customer->email)->subject('Password Reset Code');
                }
            );
        } catch (\Throwable $e) {
            // Email settings not configured yet in the admin panel — the code
            // still gets created above so support/dev can look it up manually.
            \Illuminate\Support\Facades\Log::warning('Failed to send password reset email: ' . $e->getMessage());
        }

        return response()->json(['message' => 'If that email exists, a reset code has been sent.']);
    }

    // POST /api/reset-password — verifies the code and sets a new password.
    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'code'     => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $reset = \App\Models\CustomerPasswordReset::where('email', $data['email'])
            ->where('code', $data['code'])
            ->where('expires_at', '>', now())
            ->first();

        if (!$reset) {
            throw ValidationException::withMessages([
                'code' => ['That code is invalid or has expired.'],
            ]);
        }

        $customer = Customer::where('email', $data['email'])->firstOrFail();
        $customer->update(['password' => Hash::make($data['password'])]);

        $reset->delete();

        return response()->json(['message' => 'Password updated. Please log in.']);
    }
}
