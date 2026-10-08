{{-- resources/views/admin/appupdate/edit.blade.php --}}
@extends('layouts.admin')
@section('title', 'App Update')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title"><span class="dot">•</span> App Update Settings <span class="dot">•</span></div>
    </div>

    <p style="color:#6B7280; font-size:13.5px; margin-bottom:20px;">
        Whenever you release a new APK build, update the values below. The app checks this on
        every launch and shows an "Update Available" popup if the installed version is older.
    </p>

    <form action="{{ route('admin.appupdate.update') }}" method="POST">
        @csrf @method('PUT')
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px;">
            <div>
                <label>Latest Version (shown to users)</label>
                <input type="text" name="latest_version" value="{{ $settings->latest_version }}" placeholder="1.2.0">
            </div>
            <div>
                <label>Latest Version Code (must increase every release)</label>
                <input type="number" name="latest_version_code" value="{{ $settings->latest_version_code }}" min="1">
                <div style="color:#9CA3AF; font-size:12px; margin-top:4px;">Must match the "version+buildNumber" in pubspec.yaml, e.g. 1.0.0+5 → code is 5.</div>
            </div>
        </div>

        <label>APK Download Link</label>
        <input type="url" name="apk_url" value="{{ $settings->apk_url }}" placeholder="https://yourdomain.com/downloads/vedha-latest.apk">
        <div style="color:#9CA3AF; font-size:12px; margin-top:4px;">Direct link to the new APK file (upload it to your server or Codemagic artifact link).</div>

        <label>Release Notes ("What's New")</label>
        <textarea name="release_notes" rows="4" placeholder="- Fixed playback issues&#10;- Added Tamil language support">{{ $settings->release_notes }}</textarea>

        <div style="margin-top:16px; display:flex; align-items:flex-start; gap:10px; background:#FEF2F2; border:1px solid #FECACA; border-radius:10px; padding:14px;">
            <input type="checkbox" name="force_update" value="1" @checked($settings->force_update) style="margin-top:3px;">
            <div>
                <div style="font-weight:600; font-size:14px; color:#991B1B;">Force Update (block app until updated)</div>
                <div style="color:#7F1D1D; font-size:12.5px;">Only enable this for critical fixes — users cannot dismiss the popup or use the app until they update.</div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top:22px;">Save</button>
    </form>
</div>
@endsection
