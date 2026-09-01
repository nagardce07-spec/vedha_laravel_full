{{-- resources/views/admin/quickshare/edit.blade.php --}}
@extends('layouts.admin')
@section('title', 'Quick Share')

@section('content')

<div class="card" style="margin-bottom:20px;">
    <div class="card-header"><div class="card-title"><span class="dot">•</span> Quick Share Settings <span class="dot">•</span></div></div>
    <form action="{{ route('admin.quickshare.scheme') }}" method="POST">
        @csrf @method('PUT')
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:24px;">
            <div>
                <label>App Scheme (Android &amp; iOS)</label>
                <input type="text" name="app_scheme" value="{{ $settings->app_scheme }}" placeholder="Vedha">
                <div style="color:#9CA3AF; font-size:12px; margin-top:4px;">Custom URL scheme for deep linking</div>
            </div>
            <div>
                <label>Play Store Download Link</label>
                <input type="url" name="play_store_link" value="{{ $settings->play_store_link }}" placeholder="http://localhost/">
            </div>
            <div>
                <label>App Store Download Link</label>
                <input type="url" name="app_store_link" value="{{ $settings->app_store_link }}" placeholder="http://localhost/">
            </div>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:22px;">Save</button>
    </form>
</div>

<div class="card" style="margin-bottom:20px;">
    <div class="card-header"><div class="card-title"><span class="dot">•</span> Android <span class="dot">•</span></div></div>
    <form action="{{ route('admin.quickshare.android') }}" method="POST">
        @csrf @method('PUT')
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px;">
            <div>
                <label>Package Name</label>
                <input type="text" name="android_package_name" value="{{ $settings->android_package_name }}" placeholder="com.company.app">
            </div>
            <div>
                <label>SHA 256 Keys</label>
                @foreach($shaKeys as $key)
                    <div style="display:flex; gap:8px; margin-bottom:8px;">
                        <input type="text" value="{{ $key->sha256_key }}" readonly style="background:#F9FAFB;">
                        <form action="{{ route('admin.quickshare.sha.delete', $key) }}" method="POST" onsubmit="return confirm('Remove this key?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="icon-btn delete">🗑️</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
        <div style="display:flex; gap:10px; margin-top:6px;">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-outline" onclick="checkValidation('android')">Check Validation</button>
        </div>
    </form>

    <form action="{{ route('admin.quickshare.sha.add') }}" method="POST" style="margin-top:16px; display:flex; gap:10px;">
        @csrf
        <input type="text" name="sha256_key" placeholder="Add new SHA 256 key" required>
        <button type="submit" class="btn btn-outline" style="white-space:nowrap;">Add SHA 256 Key</button>
    </form>
</div>

<div class="card">
    <div class="card-header"><div class="card-title"><span class="dot">•</span> iOS <span class="dot">•</span></div></div>
    <form action="{{ route('admin.quickshare.ios') }}" method="POST">
        @csrf @method('PUT')
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px;">
            <div>
                <label>Bundle ID / Package Name</label>
                <input type="text" name="ios_bundle_id" value="{{ $settings->ios_bundle_id }}" placeholder="com.company.app">
            </div>
            <div>
                <label>Team ID</label>
                <input type="text" name="ios_team_id" value="{{ $settings->ios_team_id }}" placeholder="ABCDE12345">
            </div>
        </div>
        <div style="display:flex; gap:10px; margin-top:22px;">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-outline" onclick="checkValidation('ios')">Check Validation</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function checkValidation(platform) {
        const url = platform === 'android'
            ? `{{ route('admin.quickshare.android.validate') }}`
            : `{{ route('admin.quickshare.ios.validate') }}`;
        fetch(url).then(r => r.json()).then(d => alert(d.valid ? 'Configuration looks valid ✅' : 'Missing required fields ❌'));
    }
</script>
@endsection
