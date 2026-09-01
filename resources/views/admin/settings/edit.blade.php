{{-- resources/views/admin/settings/edit.blade.php --}}
@extends('layouts.admin')
@section('title', 'Settings')

@section('content')
<div style="display:grid; grid-template-columns:2fr 1fr; gap:20px; margin-bottom:20px; align-items:start;">

    <div class="card">
        <div class="card-header"><div class="card-title"><span class="dot">•</span> Admin Setting <span class="dot">•</span></div></div>
        <form action="{{ route('admin.settings.admin') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <label>Title</label>
            <input type="text" name="title" value="{{ $settings->title }}">

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:16px;">
                <div>
                    <label style="margin-top:0;">Favicon</label>
                    <input type="file" name="favicon" accept="image/*">
                    @if($settings->favicon_url)
                        <img src="{{ $settings->favicon_url }}" style="height:44px; margin-top:8px; border-radius:6px;">
                    @endif
                </div>
                <div>
                    <label style="margin-top:0;">Logo Light</label>
                    <input type="file" name="logo_light" accept="image/*">
                    @if($settings->logo_light_url)
                        <img src="{{ $settings->logo_light_url }}" style="height:44px; margin-top:8px;">
                    @endif
                </div>
            </div>

            <label>Login Image</label>
            <input type="file" name="login_image" accept="image/*">
            @if($settings->login_image_url)
                <img src="{{ $settings->login_image_url }}" style="height:80px; margin-top:8px; border-radius:8px;">
            @endif

            <button type="submit" class="btn btn-primary" style="margin-top:20px;">Save</button>
        </form>
    </div>

    <div class="card">
        <div class="card-header"><div class="card-title"><span class="dot">•</span> Password <span class="dot">•</span></div></div>
        <form action="{{ route('admin.settings.password') }}" method="POST">
            @csrf @method('PUT')
            <label>Old Password</label>
            <input type="password" name="old_password" placeholder="Enter your password" required>
            <label>New Password</label>
            <input type="password" name="new_password" placeholder="Enter your new password" required>
            <label>Confirm New Password</label>
            <input type="password" name="new_password_confirmation" placeholder="Re-enter your new password" required>
            <button type="submit" class="btn btn-primary" style="margin-top:20px;">Save</button>
        </form>
    </div>
</div>

<div class="card" style="margin-bottom:20px;">
    <div class="card-header"><div class="card-title"><span class="dot">•</span> Storage Settings <span class="dot">•</span></div></div>
    <form action="{{ route('admin.settings.storage') }}" method="POST">
        @csrf @method('PUT')
        <label>Storage Provider</label>
        <select name="storage_provider" style="max-width:320px;">
            <option value="local" @selected($settings->storage_provider === 'local')>Local Storage</option>
            <option value="s3" @selected($settings->storage_provider === 's3')>Amazon S3</option>
            <option value="do_spaces" @selected($settings->storage_provider === 'do_spaces')>DigitalOcean Spaces</option>
        </select>
        <button type="submit" class="btn btn-primary" style="margin-top:20px;">Save Settings</button>
    </form>
</div>

<div class="card">
    <div class="card-header"><div class="card-title"><span class="dot">•</span> Email Settings <span class="dot">•</span></div></div>
    <form action="{{ route('admin.settings.email') }}" method="POST">
        @csrf @method('PUT')
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px;">
            <div>
                <label>Mail Driver</label>
                <input type="text" name="mail_driver" value="{{ $settings->mail_driver }}">
            </div>
            <div>
                <label>Mail Host</label>
                <input type="text" name="mail_host" value="{{ $settings->mail_host }}">
            </div>
            <div>
                <label>Mail Port</label>
                <input type="text" name="mail_port" value="{{ $settings->mail_port }}">
            </div>
            <div>
                <label>Mail Encryption</label>
                <input type="text" name="mail_encryption" value="{{ $settings->mail_encryption }}">
            </div>
            <div>
                <label>Mail Username</label>
                <input type="text" name="mail_username" value="{{ $settings->mail_username }}">
            </div>
            <div>
                <label>Mail Password</label>
                <input type="password" name="mail_password" placeholder="Leave empty to keep current">
            </div>
            <div>
                <label>From Email Address</label>
                <input type="email" name="mail_from_address" value="{{ $settings->mail_from_address }}">
            </div>
            <div>
                <label>From Name</label>
                <input type="text" name="mail_from_name" value="{{ $settings->mail_from_name }}">
            </div>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:20px;">Save</button>
    </form>
</div>
@endsection
