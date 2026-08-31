<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GeneralSettingController extends Controller
{
    // GET /admin/settings
    public function edit()
    {
        $settings = GeneralSetting::current();
        return view('admin.settings.edit', compact('settings'));
    }

    // PUT /admin/settings/admin  (Title + Favicon + Logo Light + Login Image -> Save)
    public function updateAdmin(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'favicon'     => 'nullable|image|mimes:png,ico,jpg|max:2048',
            'logo_light'  => 'nullable|image|mimes:png,jpg,svg|max:2048',
            'login_image' => 'nullable|image|mimes:png,jpg|max:5120',
        ]);

        $settings = GeneralSetting::current();

        if ($request->hasFile('favicon'))     $data['favicon_path']     = $request->file('favicon')->store('branding', 'public');
        if ($request->hasFile('logo_light'))  $data['logo_light_path']  = $request->file('logo_light')->store('branding', 'public');
        if ($request->hasFile('login_image')) $data['login_image_path'] = $request->file('login_image')->store('branding', 'public');

        $settings->update($data);

        return back()->with('success', 'Admin settings saved.');
    }

    // PUT /admin/settings/password  (Old Password / New Password -> Save)
    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $admin = Auth::guard('admin')->user();

        if (!Hash::check($request->old_password, $admin->password)) {
            return back()->withErrors(['old_password' => 'Old password is incorrect.']);
        }

        $admin->update(['password' => Hash::make($request->new_password)]);

        return back()->with('success', 'Password updated.');
    }

    // PUT /admin/settings/storage  (Storage Provider dropdown -> Save Settings)
    public function updateStorage(Request $request)
    {
        $data = $request->validate(['storage_provider' => 'required|in:local,s3,do_spaces']);
        GeneralSetting::current()->update($data);
        return back()->with('success', 'Storage settings saved.');
    }

    // PUT /admin/settings/email  (Mail Driver/Host/Port/... -> Save)
    public function updateEmail(Request $request)
    {
        $data = $request->validate([
            'mail_driver'       => 'required|string|max:50',
            'mail_host'         => 'required|string|max:255',
            'mail_port'         => 'required|string|max:10',
            'mail_encryption'   => 'nullable|string|max:20',
            'mail_username'     => 'required|string|max:255',
            'mail_password'     => 'nullable|string|max:255',
            'mail_from_address' => 'required|email',
            'mail_from_name'    => 'required|string|max:255',
        ]);

        if (empty($data['mail_password'])) unset($data['mail_password']); // keep existing if left blank

        GeneralSetting::current()->update($data);

        return back()->with('success', 'Email settings saved.');
    }
}
