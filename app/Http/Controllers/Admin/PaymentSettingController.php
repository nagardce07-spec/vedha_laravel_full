<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;

class PaymentSettingController extends Controller
{
    // GET /admin/payment-settings
    public function edit()
    {
        // Fetch raw (bypassing the $hidden secret) so the admin can see/edit it.
        $settings = PaymentSetting::current()->makeVisible('razorpay_key_secret');
        return view('admin.paymentsettings.edit', compact('settings'));
    }

    // PUT /admin/payment-settings
    public function update(Request $request)
    {
        $data = $request->validate([
            'razorpay_key_id'     => 'required|string|max:255',
            'razorpay_key_secret' => 'nullable|string|max:255', // optional: leave blank to keep existing
            'currency'            => 'required|string|max:3',
            'test_mode'           => 'nullable|boolean',
        ]);

        if (empty($data['razorpay_key_secret'])) {
            unset($data['razorpay_key_secret']); // don't overwrite with blank
        }
        $data['test_mode'] = $request->boolean('test_mode');

        PaymentSetting::current()->update($data);

        return back()->with('success', 'Payment settings saved.');
    }
}
