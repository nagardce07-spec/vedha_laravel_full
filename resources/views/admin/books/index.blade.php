{{-- resources/views/admin/books/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Book')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title"><span class="dot">•</span> Book <span class="dot">•</span></div>
        <button class="btn btn-primary" onclick="openModal('addBookModal')">Add Book</button>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
        <div style="color:#6B7280; font-size:14px;">Show 10 entries</div>
        <input type="text" class="search-input" placeholder="Search:" onkeyup="filterTable(this, 'bookTable')">
    </div>

    <table id="bookTable">
        <thead>
            <tr>
                <th>Info</th><th>Category</th><th>Rating</th><th>Likes</th><th>Views</th>
                <th>Visibility</th><th>Premium</th><th>Featured</th><th style="text-align:right;">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($books as $book)
                <tr>
                    <td style="display:flex; align-items:center; gap:10px;">
                        <img src="{{ $book->image_url }}" style="width:40px;height:52px;border-radius:6px;object-fit:cover;background:#F3F4F6;">
                        <div>
                            <div style="font-weight:600;">{{ $book->name }}</div>
                            <div style="color:#9CA3AF; font-size:12.5px;">{{ optional($book->author)->name }}</div>
                        </div>
                    </td>
                    <td>{{ optional($book->category)->name }}</td>
                    <td><span class="badge">⭐ {{ $book->average_rating }} ({{ $book->reviews_count }})</span></td>
                    <td>{{ $book->likes_count }}</td>
                    <td>{{ $book->views }}</td>
                    <td>{!! toggleHtml($book, 'is_visible') !!}</td>
                    <td>{!! toggleHtml($book, 'is_premium') !!}</td>
                    <td>{!! toggleHtml($book, 'is_featured') !!}</td>
                    <td style="text-align:right; white-space:nowrap;">
                        @if($book->type === 'chapter')
                            <a href="{{ route('admin.books.chapters', $book) }}" class="icon-btn" title="Chapters">➕</a>
                        @endif
                        <button class="icon-btn edit" onclick='editBook(@json($book))'>✏️</button>
                        <form action="{{ route('admin.books.destroy', $book) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this book?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="icon-btn delete">🗑️</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:14px;">
        <span style="color:#6B7280; font-size:13px;">Showing {{ $books->firstItem() }} to {{ $books->lastItem() }} of {{ $books->total() }} entries</span>
        <div class="pagination">{{ $books->links() }}</div>
    </div>
</div>

@php
    // Renders one on/off pill switch; used for Visibility / Premium / Featured columns.
    function toggleHtml($book, $field) {
        $on = $book->$field ? 'on' : '';
        return "<span class='toggle {$on}' onclick=\"toggleField({$book->id}, '{$field}', this)\"><span class='knob'></span></span>";
    }
@endphp

<!-- Add Book Modal -->
<div class="modal-backdrop" id="addBookModal" style="display:none;">
    <div class="modal">
        <div class="modal-header">
            <div class="card-title"><span class="dot">•</span> Add Book <span class="dot">•</span></div>
            <span style="cursor:pointer;" onclick="closeModal('addBookModal')">✕</span>
        </div>
        <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label>Name</label>
            <input type="text" name="name" placeholder="Enter Name" required>

            <label>Image</label>
            <input type="file" name="image" accept="image/*" required>

            <label>Category</label>
            <select name="category_id" required>
                <option value="">Select Category</option>
                @foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
            </select>

            <label>Author</label>
            <select name="author_id" required>
                <option value="">Select Author</option>
                @foreach($authors as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach
            </select>

            <label>Description</label>
            <textarea name="description" rows="3" placeholder="Enter Description"></textarea>

            <label>Type</label>
            <div style="display:flex; gap:10px;">
                <label style="display:flex;align-items:center;gap:6px;font-weight:400;margin:0;">
                    <input type="radio" name="type" value="chapter" onchange="toggleBookType(this)"> Chapter
                </label>
                <label style="display:flex;align-items:center;gap:6px;font-weight:400;margin:0;">
                    <input type="radio" name="type" value="full" checked onchange="toggleBookType(this)"> Full Book
                </label>
            </div>

            <div id="fullBookFields">
                <label>Upload Type</label>
                <div style="display:flex; gap:10px;">
                    <label style="display:flex;align-items:center;gap:6px;font-weight:400;margin:0;">
                        <input type="radio" name="upload_type" value="url" onchange="toggleUploadType(this)"> URL
                    </label>
                    <label style="display:flex;align-items:center;gap:6px;font-weight:400;margin:0;">
                        <input type="radio" name="upload_type" value="file" checked onchange="toggleUploadType(this)"> File
                    </label>
                </div>

                <div id="urlField" style="display:none;">
                    <label>Resource URL</label>
                    <input type="url" name="resource_url" placeholder="https://...">
                </div>
                <div id="fileField">
                    <label>Upload Book</label>
                    <input type="file" name="resource_file" accept="audio/*">
                </div>

                <label>Duration (MM:SS)</label>
                <div style="display:flex; gap:8px; align-items:center;">
                    <input type="number" name="duration_min" min="0" placeholder="00" style="width:70px;">
                    <span>:</span>
                    <input type="number" name="duration_sec" min="0" max="59" placeholder="00" style="width:70px;">
                </div>
                <div style="color:#9CA3AF; font-size:12px; margin-top:4px;">Duration will be auto-detected for uploaded files</div>
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:22px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addBookModal')">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Book Modal -->
<div class="modal-backdrop" id="editBookModal" style="display:none;">
    <div class="modal">
        <div class="modal-header">
            <div class="card-title"><span class="dot">•</span> Edit Book <span class="dot">•</span></div>
            <span style="cursor:pointer;" onclick="closeModal('editBookModal')">✕</span>
        </div>
        <form id="editBookForm" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <label>Name</label>
            <input type="text" name="name" id="editBookName" required>

            <label>Image</label>
            <input type="file" name="image" accept="image/*">
            <div id="editBookCurrentImage" style="margin-top:8px; font-size:12.5px; color:#6B7280;"></div>

            <label>Category</label>
            <select name="category_id" id="editBookCategory" required>
                @foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
            </select>

            <label>Author</label>
            <select name="author_id" id="editBookAuthor" required>
                @foreach($authors as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach
            </select>

            <label>Description</label>
            <textarea name="description" id="editBookDescription" rows="3"></textarea>

            <label>Type</label>
            <div>
                <span class="badge" id="editBookTypeBadge"></span>
                <div style="color:#9CA3AF; font-size:12px; margin-top:6px;">Book type cannot be changed after creation.</div>
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:22px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editBookModal')">Close</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleBookType(radio) {
        document.getElementById('fullBookFields').style.display = radio.value === 'full' ? 'block' : 'none';
    }
    function toggleUploadType(radio) {
        document.getElementById('urlField').style.display  = radio.value === 'url'  ? 'block' : 'none';
        document.getElementById('fileField').style.display = radio.value === 'file' ? 'block' : 'none';
    }

    function editBook(book) {
        document.getElementById('editBookForm').action = `/admin/books/${book.id}`;
        document.getElementById('editBookName').value = book.name;
        document.getElementById('editBookCategory').value = book.category_id;
        document.getElementById('editBookAuthor').value = book.author_id;
        document.getElementById('editBookDescription').value = book.description ?? '';
        document.getElementById('editBookTypeBadge').textContent = book.type === 'chapter' ? 'Chapter' : 'Full Book';
        document.getElementById('editBookCurrentImage').innerHTML =
            `<img src="${book.image_url}" style="width:40px;height:52px;object-fit:cover;border-radius:6px;"> Current image (leave file empty to keep)`;
        openModal('editBookModal');
    }

    function filterTable(input, tableId) {
        const filter = input.value.toLowerCase();
        document.querySelectorAll(`#${tableId} tbody tr`).forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
        });
    }

    // Instantly flips a Visibility/Premium/Featured pill without a full page reload.
    function toggleField(bookId, field, el) {
        const turningOn = !el.classList.contains('on');
        el.classList.toggle('on');

        fetch(`/admin/books/${bookId}/toggle/${field}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken },
            body: JSON.stringify({ value: turningOn }),
        }).catch(() => el.classList.toggle('on')); // revert on failure
    }
</script>
@endsection
