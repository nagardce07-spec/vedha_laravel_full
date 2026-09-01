{{-- resources/views/admin/suggestions/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Book Suggestions')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title"><span class="dot">•</span> Book Suggestions <span class="dot">•</span></div>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
        <div style="color:#6B7280; font-size:14px;">Show 10 entries</div>
        <input type="text" class="search-input" placeholder="Search:" onkeyup="filterTable(this, 'suggTable')">
    </div>

    <div style="overflow-x:auto;">
    <table id="suggTable">
        <thead><tr><th>Book Name</th><th>Author Name</th><th>Description</th><th>Suggested By</th><th style="text-align:right;">Action</th></tr></thead>
        <tbody>
            @foreach($suggestions as $s)
                <tr>
                    <td>{{ $s->book_name }}</td>
                    <td>{{ $s->author_name }}</td>
                    <td style="max-width:420px;">{{ $s->description ?? 'N/A' }}</td>
                    <td>{{ $s->suggested_by ?? optional($s->customer)->name }}</td>
                    <td style="text-align:right; white-space:nowrap;">
                        <form action="{{ route('admin.suggestions.approve', $s) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="icon-btn" title="Create draft book">➕</button>
                        </form>
                        <form action="{{ route('admin.suggestions.destroy', $s) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this suggestion?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="icon-btn delete">🗑️</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:14px;">
        <span style="color:#6B7280; font-size:13px;">Showing {{ $suggestions->firstItem() }} to {{ $suggestions->lastItem() }} of {{ $suggestions->total() }} entries</span>
        <div class="pagination">{{ $suggestions->links() }}</div>
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
