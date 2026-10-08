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
                <th>Customer ID</th><th>Customer Info</th><th>Username</th><th>Phone</th><th>Device Type</th><th>Premium</th><th>Tests</th><th>Joined</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
                @php
                    $loginColors = ['Email' => '#059669', 'Google' => '#059669', 'Apple' => '#059669'];
                    $loginColor = $loginColors[$customer->login_type] ?? '#6B7280';
                @endphp
                <tr>
                    <td>{{ $customer->id }}</td>
                    <td style="display:flex; align-items:center; gap:10px;">
                        <div style="width:36px;height:36px;border-radius:50%;background:#EDE9FE;color:#7C3AED;display:flex;align-items:center;justify-content:center;">👤</div>
                        <div>
                            <div style="font-weight:600;"><a href="{{ route('admin.customers.show',$customer) }}" style="color:#7C3AED;">{{ $customer->name }}</a></div>
                            <div style="color:#9CA3AF; font-size:12.5px;">{{ $customer->email }}</div>
                        </div>
                    </td>
                    <td>{{ $customer->username ?? '—' }}</td>
                    <td>{{ $customer->phone ?? '—' }}</td>
                    <td><span class="badge" style="background:#EDE9FE;color:#7C3AED;">{{ $customer->device_type }}</span></td>
                    <td><span class="badge" style="background:{{ $customer->premium_active ? '#ECFDF5' : '#F3F4F6' }};color:{{ $customer->premium_active ? '#059669' : '#6B7280' }};">{{ $customer->premium_active ? 'Active' : 'No' }}</span></td>
                    <td><a href="{{ route('admin.customers.show',$customer) }}" class="badge" style="background:#EDE9FE;color:#7C3AED;">{{ $customer->testAttempts_count }} Tests</a></td>
                    <td>{{ $customer->created_at->format('M d, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:14px;">
        <span style="color:#6B7280; font-size:13px;">Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} entries</span>
        <div class="pagination">{{ $customers->links('vendor.pagination.custom') }}</div>
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
