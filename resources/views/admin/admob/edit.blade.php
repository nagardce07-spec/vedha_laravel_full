{{-- resources/views/admin/admob/edit.blade.php --}}
@extends('layouts.admin')
@section('title', 'Admob')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title"><span class="dot">•</span> Admob <span class="dot">•</span></div>
    </div>

    <form action="{{ route('admin.admob.update') }}" method="POST">
        @csrf @method('PUT')
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px;">
            <div class="card" style="background:#FAFAFC;">
                <div style="font-weight:600; margin-bottom:10px;">Android</div>
                <label>Banner Id</label>
                <input type="text" name="android_banner_id" value="{{ $settings->android_banner_id }}" placeholder="ca-app-pub-...">
                <label>Interstitial Id</label>
                <input type="text" name="android_interstitial_id" value="{{ $settings->android_interstitial_id }}" placeholder="ca-app-pub-...">
            </div>
            <div class="card" style="background:#FAFAFC;">
                <div style="font-weight:600; margin-bottom:10px;">iOS</div>
                <label>Banner Id</label>
                <input type="text" name="ios_banner_id" value="{{ $settings->ios_banner_id }}" placeholder="ca-app-pub-...">
                <label>Interstitial Id</label>
                <input type="text" name="ios_interstitial_id" value="{{ $settings->ios_interstitial_id }}" placeholder="ca-app-pub-...">
            </div>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:22px;">Save</button>
    </form>
</div>
@endsection
