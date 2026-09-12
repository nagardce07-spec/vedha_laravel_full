<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AndroidShaKey;
use App\Models\QuickShareSetting;
use Illuminate\Http\Request;

class QuickShareSettingController extends Controller
{
    // GET /admin/quick-share
    public function edit()
    {
        $settings = QuickShareSetting::current();
        $shaKeys  = AndroidShaKey::latest()->get();
        return view('admin.quickshare.edit', compact('settings', 'shaKeys'));
    }

    // PUT /admin/quick-share/scheme  (top card: App Scheme + Play Store + App Store links)
    public function updateScheme(Request $request)
    {
        $data = $request->validate([
            'app_scheme'      => 'required|string|max:100',
            'play_store_link' => 'nullable|url',
            'app_store_link'  => 'nullable|url',
        ]);

        QuickShareSetting::current()->update($data);

        return back()->with('success', 'App scheme saved.');
    }

    // PUT /admin/quick-share/android  (Package Name + SHA keys)
    public function updateAndroid(Request $request)
    {
        $data = $request->validate(['android_package_name' => 'required|string|max:255']);
        QuickShareSetting::current()->update($data);
        return back()->with('success', 'Android settings saved.');
    }

    // POST /admin/quick-share/android/sha-keys  (Add SHA 256 Key button)
    public function addShaKey(Request $request)
    {
        $data = $request->validate(['sha256_key' => 'required|string|max:255']);
        AndroidShaKey::create($data);
        return back()->with('success', 'SHA key added.');
    }

    public function deleteShaKey(AndroidShaKey $shaKey)
    {
        $shaKey->delete();
        return back()->with('success', 'SHA key removed.');
    }

    // GET /admin/quick-share/android/validate  ("Check Validation" button)
    public function validateAndroid()
    {
        $settings = QuickShareSetting::current();
        $valid = filled($settings->android_package_name) && AndroidShaKey::exists();
        return response()->json(['valid' => $valid]);
    }

    // PUT /admin/quick-share/ios  (Bundle ID + Team ID)
    public function updateIos(Request $request)
    {
        $data = $request->validate([
            'ios_bundle_id' => 'required|string|max:255',
            'ios_team_id'   => 'required|string|max:100',
        ]);

        QuickShareSetting::current()->update($data);

        return back()->with('success', 'iOS settings saved.');
    }

    // GET /admin/quick-share/ios/validate
    public function validateIos()
    {
        $settings = QuickShareSetting::current();
        $valid = filled($settings->ios_bundle_id) && filled($settings->ios_team_id);
        return response()->json(['valid' => $valid]);
    }
}
