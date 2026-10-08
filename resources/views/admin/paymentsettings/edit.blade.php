{{-- resources/views/admin/paymentsettings/edit.blade.php --}}
@extends('layouts.admin')
@section('title', 'Payment Settings')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title"><span class="dot">•</span> Razorpay Payment Settings <span class="dot">•</span></div>
    </div>

    <form action="{{ route('admin.paymentsettings.update') }}" method="POST">
        @csrf @method('PUT')

        <label>Razorpay Key ID</label>
        <input type="text" name="razorpay_key_id" value="{{ $settings->razorpay_key_id }}" placeholder="rzp_test_xxxxxxxxxxxx" required>

        <label>Razorpay Key Secret</label>
        <input type="password" name="razorpay_key_secret" placeholder="{{ $settings->razorpay_key_secret ? 'Leave blank to keep current secret' : 'Enter secret key' }}">
        <div style="color:#9CA3AF; font-size:12px; margin-top:4px;">Leave blank when updating other fields to keep the existing secret.</div>

        <label>Currency</label>
        <input type="text" name="currency" value="{{ $settings->currency }}" maxlength="3" required style="max-width:120px;">

        <label style="display:flex; align-items:center; gap:8px; margin-top:14px;">
            <input type="checkbox" name="test_mode" value="1" {{ $settings->test_mode ? 'checked' : '' }}>
            Test Mode (use Razorpay test keys — no real money charged)
        </label>

        <div style="margin-top:20px; padding:14px; background:#FAFAFC; border-radius:10px; font-size:13px; color:#6B7280;">
            Get your keys from <a href="https://dashboard.razorpay.com/app/keys" target="_blank" style="color:var(--purple);">Razorpay Dashboard → Settings → API Keys</a>.
            Use <strong>Test Mode</strong> keys while developing, switch to <strong>Live</strong> keys (and uncheck Test Mode) when ready to accept real payments.
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top:20px;">Save</button>
    </form>
</div>
@endsection
