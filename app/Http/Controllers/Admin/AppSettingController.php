<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class AppSettingController extends Controller
{
    // GET /admin/app-settings  (Theme Color / Theme Light Color / Theme Background Color)
    public function edit()
    {
        $settings = AppSetting::current();
        return view('admin.appsettings.edit', compact('settings'));
    }

    // PUT /admin/app-settings
    public function update(Request $request)
    {
        $data = $request->validate([
            'theme_color'            => 'required|string|max:7',
            'theme_light_color'      => 'required|string|max:7',
            'theme_background_color' => 'required|string|max:7',
        ]);

        AppSetting::current()->update($data);

        return back()->with('success', 'App theme saved.');
    }
}
