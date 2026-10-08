{{-- resources/views/admin/appsettings/edit.blade.php --}}
@extends('layouts.admin')
@section('title', 'App Settings')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title"><span class="dot">•</span> App Settings <span class="dot">•</span></div>
    </div>

    <form action="{{ route('admin.appsettings.update') }}" method="POST">
        @csrf @method('PUT')
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:24px;">
            <div>
                <label>Theme Color</label>
                <div style="display:flex; gap:10px;">
                    <input type="text" name="theme_color" id="themeColor" value="{{ $settings->theme_color }}">
                    <input type="color" value="{{ $settings->theme_color }}" oninput="document.getElementById('themeColor').value=this.value" style="width:44px; border:none; padding:0; border-radius:8px;">
                </div>
            </div>
            <div>
                <label>Theme Light Color</label>
                <div style="display:flex; gap:10px;">
                    <input type="text" name="theme_light_color" id="themeLight" value="{{ $settings->theme_light_color }}">
                    <input type="color" value="{{ $settings->theme_light_color }}" oninput="document.getElementById('themeLight').value=this.value" style="width:44px; border:none; padding:0; border-radius:8px;">
                </div>
            </div>
            <div>
                <label>Theme Background Color</label>
                <div style="display:flex; gap:10px;">
                    <input type="text" name="theme_background_color" id="themeBg" value="{{ $settings->theme_background_color }}">
                    <input type="color" value="{{ $settings->theme_background_color }}" oninput="document.getElementById('themeBg').value=this.value" style="width:44px; border:none; padding:0; border-radius:8px;">
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:22px;">Save</button>
    </form>
</div>

<div class="card" style="margin-top:20px;">
    <div class="card-header">
        <div class="card-title"><span class="dot">•</span> App Version / Update Checker <span class="dot">•</span></div>
    </div>

    <form action="{{ route('admin.appsettings.version') }}" method="POST">
        @csrf @method('PUT')
        <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:24px;">
            <div>
                <label>Latest Version Code (integer, must match Flutter's build number — pubspec.yaml's version: X.X.X+<b>this number</b>)</label>
                <input type="number" name="latest_version_code" value="{{ $settings->latest_version_code }}" min="1">
            </div>
            <div>
                <label>Latest Version Name (shown to users)</label>
                <input type="text" name="latest_version_name" value="{{ $settings->latest_version_name }}" placeholder="1.2.0">
            </div>
            <div style="grid-column: span 2;">
                <label>APK Download URL</label>
                <input type="url" name="update_url" value="{{ $settings->update_url }}" placeholder="https://yourdomain.com/downloads/vedha-latest.apk">
            </div>
            <div style="grid-column: span 2;">
                <label>Update Message (What's New — shown in the popup)</label>
                <textarea name="update_message" rows="3" placeholder="Bug fixes and performance improvements.">{{ $settings->update_message }}</textarea>
            </div>
            <div style="display:flex; align-items:center; gap:10px;">
                <input type="checkbox" name="force_update" value="1" @checked($settings->force_update) style="width:18px; height:18px;">
                <label style="margin:0;">Force update (blocks app usage until user updates)</label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:22px;">Save Version Info</button>
    </form>
</div>
@endsection
