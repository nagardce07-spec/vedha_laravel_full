{{-- resources/views/admin/customers/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Customers')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title"><span class="dot">•</span> Customers <span class="dot">•</span></div>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
        <div style="color:#6B7280; font-size:14px;">Show 10 entries</div>
        <input type="text" class="search-input" placeholder="Search:" onkeyup="filterTable(this, 'custTable')">
    </div>

    <table id="custTable">
        <thead>
            <tr>
                <th>Customer Info</th><th>Device Type</th><th>Login Type</th>
                <th>Books Liked</th><th>Reviews</th><th>Joined</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
                @php
                    $loginColors = ['Email' => '#059669', 'Google' => '#059669', 'Apple' => '#059669'];
                    $loginColor = $loginColors[$customer->login_type] ?? '#6B7280';
                @endphp
                <tr>
                    <td style="display:flex; align-items:center; gap:10px;">
                        <div style="width:36px;height:36px;border-radius:50%;background:#EDE9FE;color:#7C3AED;display:flex;align-items:center;justify-content:center;">👤</div>
                        <div>
                            <div style="font-weight:600;">{{ $customer->name }}</div>
                            <div style="color:#9CA3AF; font-size:12.5px;">{{ $customer->email }}</div>
                        </div>
                    </td>
                    <td><span class="badge" style="background:#EDE9FE;color:#7C3AED;">{{ $customer->device_type }}</span></td>
                    <td><span class="badge" style="background:#ECFDF5;color:{{ $loginColor }};">{{ $customer->login_type }}</span></td>
                    <td>{{ $customer->likes_count }}</td>
                    <td>{{ $customer->reviews_count }}</td>
                    <td>{{ $customer->created_at->format('M d, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:14px;">
        <span style="color:#6B7280; font-size:13px;">Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} entries</span>
        <div class="pagination">{{ $customers->links() }}</div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function filterTable(input, tableId) {
        const filter = input.value.toLowerCase();
        document.querySelectorAll(`#${tableId} tbody tr`).forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
        });
    }
</script>
@endsection
