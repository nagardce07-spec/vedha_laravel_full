{{-- resources/views/admin/categories/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Categories')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title"><span class="dot">•</span> Categories <span class="dot">•</span></div>
        <button class="btn btn-primary" onclick="openModal('addCategoryModal')">Add Category</button>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
        <div style="color:#6B7280; font-size:14px;">Show 10 entries</div>
        <input type="text" class="search-input" placeholder="Search:" onkeyup="filterTable(this, 'catTable')">
    </div>

    <table id="catTable">
        <thead><tr><th>Name</th><th style="text-align:right;">Action</th></tr></thead>
        <tbody>
            @foreach($categories as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td style="text-align:right;">
                        <button class="icon-btn edit" onclick="editCategory({{ $category->id }}, '{{ addslashes($category->name) }}')">✏️</button>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this category?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="icon-btn delete">🗑️</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:14px;">
        <span style="color:#6B7280; font-size:13px;">Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} of {{ $categories->total() }} entries</span>
        <div class="pagination">{{ $categories->links() }}</div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal-backdrop" id="addCategoryModal" style="display:none;">
    <div class="modal">
        <div class="modal-header">
            <div class="card-title"><span class="dot">•</span> Add Category <span class="dot">•</span></div>
            <span style="cursor:pointer;" onclick="closeModal('addCategoryModal')">✕</span>
        </div>
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            <label>Name</label>
            <input type="text" name="name" placeholder="Enter Name" required>
            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:22px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addCategoryModal')">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal-backdrop" id="editCategoryModal" style="display:none;">
    <div class="modal">
        <div class="modal-header">
            <div class="card-title"><span class="dot">•</span> Edit Category <span class="dot">•</span></div>
            <span style="cursor:pointer;" onclick="closeModal('editCategoryModal')">✕</span>
        </div>
        <form id="editCategoryForm" method="POST">
            @csrf @method('PUT')
            <label>Name</label>
            <input type="text" name="name" id="editCategoryName" required>
            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:22px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editCategoryModal')">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function editCategory(id, name) {
        document.getElementById('editCategoryForm').action = `/admin/categories/${id}`;
        document.getElementById('editCategoryName').value = name;
        openModal('editCategoryModal');
    }
    function filterTable(input, tableId) {
        const filter = input.value.toLowerCase();
        document.querySelectorAll(`#${tableId} tbody tr`).forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
        });
    }
</script>
@endsection
