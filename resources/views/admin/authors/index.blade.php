{{-- resources/views/admin/authors/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Author')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title"><span class="dot">•</span> Author <span class="dot">•</span></div>
        <button class="btn btn-primary" onclick="openModal('addAuthorModal')">Add Author</button>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
        <div style="color:#6B7280; font-size:14px;">Show 10 entries</div>
        <input type="text" class="search-input" placeholder="Search:" onkeyup="filterTable(this, 'authorTable')">
    </div>

    <table id="authorTable">
        <thead><tr><th>Image</th><th>Books</th><th style="text-align:right;">Action</th></tr></thead>
        <tbody>
            @foreach($authors as $author)
                <tr>
                    <td style="display:flex; align-items:center; gap:10px;">
                        <img src="{{ $author->image_url ?? 'https://ui-avatars.com/api/?name='.urlencode($author->name) }}"
                             style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                        {{ $author->name }}
                    </td>
                    <td>{{ $author->books_count }}</td>
                    <td style="text-align:right;">
                        <button class="icon-btn edit" onclick="editAuthor({{ $author->id }}, '{{ addslashes($author->name) }}')">✏️</button>
                        <form action="{{ route('admin.authors.destroy', $author) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this author?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="icon-btn delete">🗑️</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:14px;">
        <span style="color:#6B7280; font-size:13px;">Showing {{ $authors->firstItem() }} to {{ $authors->lastItem() }} of {{ $authors->total() }} entries</span>
        <div class="pagination">{{ $authors->links() }}</div>
    </div>
</div>

<!-- Add Author Modal -->
<div class="modal-backdrop" id="addAuthorModal" style="display:none;">
    <div class="modal">
        <div class="modal-header">
            <div class="card-title"><span class="dot">•</span> Add Author <span class="dot">•</span></div>
            <span style="cursor:pointer;" onclick="closeModal('addAuthorModal')">✕</span>
        </div>
        <form action="{{ route('admin.authors.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label>Name</label>
            <input type="text" name="name" placeholder="Enter Name" required>
            <label>Image</label>
            <input type="file" name="image" accept="image/*">
            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:22px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addAuthorModal')">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Author Modal -->
<div class="modal-backdrop" id="editAuthorModal" style="display:none;">
    <div class="modal">
        <div class="modal-header">
            <div class="card-title"><span class="dot">•</span> Edit Author <span class="dot">•</span></div>
            <span style="cursor:pointer;" onclick="closeModal('editAuthorModal')">✕</span>
        </div>
        <form id="editAuthorForm" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <label>Name</label>
            <input type="text" name="name" id="editAuthorName" required>
            <label>Image (leave empty to keep current)</label>
            <input type="file" name="image" accept="image/*">
            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:22px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editAuthorModal')">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function editAuthor(id, name) {
        document.getElementById('editAuthorForm').action = `/admin/authors/${id}`;
        document.getElementById('editAuthorName').value = name;
        openModal('editAuthorModal');
    }
    function filterTable(input, tableId) {
        const filter = input.value.toLowerCase();
        document.querySelectorAll(`#${tableId} tbody tr`).forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
        });
    }
</script>
@endsection
