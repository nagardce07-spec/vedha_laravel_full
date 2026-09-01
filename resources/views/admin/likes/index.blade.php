{{-- resources/views/admin/likes/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Book Likes')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title"><span class="dot">•</span> Book Likes <span class="dot">•</span></div>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
        <div style="color:#6B7280; font-size:14px;">Show 10 entries</div>
        <input type="text" class="search-input" placeholder="Search:" onkeyup="filterTable(this, 'likesTable')">
    </div>

    <table id="likesTable">
        <thead><tr><th>Book Name</th><th>User Name</th><th>Liked At</th></tr></thead>
        <tbody>
            @foreach($likes as $like)
                <tr>
                    <td>{{ optional($like->book)->name }}</td>
                    <td>{{ $like->user_name ?? optional($like->customer)->name }}</td>
                    <td>{{ $like->liked_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:14px;">
        <span style="color:#6B7280; font-size:13px;">Showing {{ $likes->firstItem() }} to {{ $likes->lastItem() }} of {{ $likes->total() }} entries</span>
        <div class="pagination">{{ $likes->links() }}</div>
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
