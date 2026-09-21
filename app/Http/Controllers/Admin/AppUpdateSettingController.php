<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppUpdateSetting;
use Illuminate\Http\Request;

class AppUpdateSettingController extends Controller
{
    // GET /admin/app-update
    public function edit()
    {
        $settings = AppUpdateSetting::current();
        return view('admin.appupdate.edit', compact('settings'));
    }

    // PUT /admin/app-update
    public function update(Request $request)
    {
        $data = $request->validate([
            'latest_version'      => 'required|string|max:20',
            'latest_version_code' => 'required|integer|min:1',
            'apk_url'             => 'nullable|url',
            'release_notes'       => 'nullable|string',
            'force_update'        => 'nullable|boolean',
        ]);

        $data['force_update'] = $request->boolean('force_update');

        AppUpdateSetting::current()->update($data);

        return back()->with('success', 'App update settings saved.');
    }
}
