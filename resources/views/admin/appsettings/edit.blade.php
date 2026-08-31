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
@endsection
