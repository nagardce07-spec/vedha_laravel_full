<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdmobSetting;
use Illuminate\Http\Request;

class AdmobSettingController extends Controller
{
    // GET /admin/admob
    public function edit()
    {
        $settings = AdmobSetting::current();
        return view('admin.admob.edit', compact('settings'));
    }

    // PUT /admin/admob
    public function update(Request $request)
    {
        $data = $request->validate([
            'android_banner_id'       => 'nullable|string|max:255',
            'android_interstitial_id' => 'nullable|string|max:255',
            'ios_banner_id'           => 'nullable|string|max:255',
            'ios_interstitial_id'     => 'nullable|string|max:255',
        ]);

        AdmobSetting::current()->update($data);

        return back()->with('success', 'Admob settings saved.');
    }
}
