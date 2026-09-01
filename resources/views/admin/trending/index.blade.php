{{-- resources/views/admin/trending/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Trending Books')

@section('content')
<div style="display:flex; gap:18px;">
    <div class="card" style="flex:1;">
        <div class="card-header">
            <div class="card-title"><span class="dot">•</span> Trending Books <span class="dot">•</span></div>
            <button class="btn btn-primary" onclick="togglePanel()">Add Trending Book</button>
        </div>

        <div id="trendingList">
            @foreach($trending as $item)
                <div class="trending-row" data-id="{{ $item->id }}"
                     style="display:flex; align-items:center; gap:14px; padding:12px 8px; border-bottom:1px solid #E5E7EB; cursor:grab;">
                    <span style="color:#9CA3AF;">⠿⠿</span>
                    <span style="width:20px; font-weight:600;">{{ $loop->iteration }}</span>
                    <img src="{{ optional($item->book)->image_url }}" style="width:40px;height:52px;border-radius:6px;object-fit:cover;">
                    <div style="flex:1;">
                        <div style="font-weight:600;">{{ optional($item->book)->name }}</div>
                        <div style="color:#9CA3AF; font-size:12.5px;">{{ optional(optional($item->book)->author)->name }}</div>
                    </div>
                    <form action="{{ route('admin.trending.destroy', $item) }}" method="POST" onsubmit="return confirm('Remove from trending?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="icon-btn delete">🗑️</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Add Trending Book search panel -->
    <div class="card" id="searchPanel" style="flex:1; display:none;">
        <div class="card-header">
            <div class="card-title"><span class="dot">•</span> Add Trending Book <span class="dot">•</span></div>
            <span style="cursor:pointer;" onclick="togglePanel()">✕</span>
        </div>
        <input type="text" id="trendingSearch" placeholder="Search books by name or author..." oninput="searchBooks()">
        <div id="searchResults" style="display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-top:18px;"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function togglePanel() {
        const panel = document.getElementById('searchPanel');
        const visible = panel.style.display === 'block';
        panel.style.display = visible ? 'none' : 'block';
        if (!visible) searchBooks();
    }

    function searchBooks() {
        const q = document.getElementById('trendingSearch').value;
        fetch(`{{ route('admin.trending.search') }}?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(books => {
                const container = document.getElementById('searchResults');
                container.innerHTML = books.map(b => `
                    <div style="text-align:center;">
                        <img src="${b.image_url}" style="width:100%; aspect-ratio:3/4; object-fit:cover; border-radius:10px;">
                        <div style="font-weight:600; margin-top:8px; font-size:13.5px;">${b.name}</div>
                        <div style="color:#9CA3AF; font-size:12px;">${b.author ? b.author.name : ''}</div>
                        <button class="btn btn-primary" style="width:100%; margin-top:8px; padding:8px;" onclick="addTrending(${b.id}, this)">Add to Trending</button>
                    </div>
                `).join('');
            });
    }

    function addTrending(bookId, btn) {
        fetch(`{{ route('admin.trending.store') }}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken },
            body: JSON.stringify({ book_id: bookId }),
        }).then(() => location.reload());
    }

    // Simple drag-and-drop re-order using the native HTML5 drag API.
    let dragEl = null;
    document.querySelectorAll('.trending-row').forEach(row => {
        row.draggable = true;
        row.addEventListener('dragstart', () => dragEl = row);
        row.addEventListener('dragover', e => e.preventDefault());
        row.addEventListener('drop', function () {
            if (dragEl && dragEl !== this) {
                this.parentNode.insertBefore(dragEl, this.nextSibling);
                saveOrder();
            }
        });
    });

    function saveOrder() {
        const order = [...document.querySelectorAll('.trending-row')].map(r => r.dataset.id);
        fetch(`{{ route('admin.trending.reorder') }}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken },
            body: JSON.stringify({ order }),
        });
    }
</script>
@endsection
