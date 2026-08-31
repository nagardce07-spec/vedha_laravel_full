{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:18px; margin-bottom:18px;">
    <div class="card">
        <div style="width:44px;height:44px;background:#F3E8FF;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">🗂️</div>
        <div style="color:#6B7280;font-size:14px;">Categories</div>
        <div style="font-size:28px;font-weight:700;">{{ $stats['categories'] }}</div>
    </div>
    <div class="card">
        <div style="width:44px;height:44px;background:#F3E8FF;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">📖</div>
        <div style="color:#6B7280;font-size:14px;">Books</div>
        <div style="font-size:28px;font-weight:700;">{{ $stats['books'] }}</div>
    </div>
    <div class="card">
        <div style="width:44px;height:44px;background:#F3E8FF;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">👤</div>
        <div style="color:#6B7280;font-size:14px;">Authors</div>
        <div style="font-size:28px;font-weight:700;">{{ $stats['authors'] }}</div>
    </div>
    <div class="card">
        <div style="width:44px;height:44px;background:#F3E8FF;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">💬</div>
        <div style="color:#6B7280;font-size:14px;">Book Reviews</div>
        <div style="font-size:28px;font-weight:700;">{{ $stats['reviews'] }}</div>
    </div>
</div>

<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:18px; margin-bottom:18px;">
    <div class="card">
        <div style="width:44px;height:44px;background:#F3E8FF;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">📈</div>
        <div style="color:#6B7280;font-size:14px;">Trending Books</div>
        <div style="font-size:28px;font-weight:700;">{{ $stats['trending_books'] }}</div>
    </div>
    <div class="card">
        <div style="width:44px;height:44px;background:#F3E8FF;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">👍</div>
        <div style="color:#6B7280;font-size:14px;">Total Likes</div>
        <div style="font-size:28px;font-weight:700;">{{ $stats['total_likes'] }}</div>
    </div>
    <div class="card">
        <div style="width:44px;height:44px;background:#F3E8FF;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">👥</div>
        <div style="color:#6B7280;font-size:14px;">Users</div>
        <div style="font-size:28px;font-weight:700;">{{ $stats['users'] }}</div>
    </div>
    <div class="card">
        <div style="width:44px;height:44px;background:#F3E8FF;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">⭐</div>
        <div style="color:#6B7280;font-size:14px;">Average Rating</div>
        <div style="font-size:28px;font-weight:700;">{{ $stats['average_rating'] }} <span style="font-size:14px;color:#6B7280;">/5</span></div>
    </div>
</div>

<div class="card" style="margin-bottom:18px; max-width: 380px;">
    <div style="width:44px;height:44px;background:#F3E8FF;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">🏆</div>
    <div style="color:#6B7280;font-size:14px;">Most Popular Category</div>
    <div style="font-size:24px;font-weight:700;">{{ optional($mostPopular)->name ?? '—' }}</div>
    <div style="color:#9CA3AF;font-size:13px;">{{ optional($mostPopular)->books_count ?? 0 }} books</div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title"><span class="dot">•</span> Daily Customer Registrations <span class="dot">•</span></div>
        <form method="GET">
            <input type="month" name="month" value="{{ $month->format('Y-m') }}" onchange="this.form.submit()"
                   style="border:1px solid #E5E7EB;border-radius:8px;padding:8px 12px;">
        </form>
    </div>
    <canvas id="regChart" height="90"></canvas>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
<script>
    new Chart(document.getElementById('regChart'), {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Number of Customers',
                data: @json($chartData),
                borderColor: '#3B82F6',
                tension: 0.4,
                pointRadius: 0,
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { title: { display: true, text: 'Number of Customers' } }, x: { title: { display: true, text: 'Date' } } }
        }
    });
</script>
@endsection
