{{-- resources/views/admin/books/chapters.blade.php --}}
@extends('layouts.admin')
@section('title', $book->name . ' — Chapters')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title"><span class="dot">•</span> {{ $book->name }} <span class="dot">•</span></div>
        <button class="btn btn-primary" onclick="openModal('addChapterModal')">Add Chapter</button>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
        <div style="color:#6B7280; font-size:14px;">Show 10 entries</div>
        <input type="text" class="search-input" placeholder="Search:">
    </div>

    <table>
        <thead><tr><th>Chapter Number</th><th>Resource</th><th style="text-align:right;">Action</th></tr></thead>
        <tbody>
            @foreach($chapters as $chapter)
                <tr>
                    <td>{{ $chapter->chapter_number }}</td>
                    <td>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <audio controls src="{{ $chapter->resource_full_url }}" style="height:32px;"></audio>
                            <span style="color:#9CA3AF; font-size:12.5px;">{{ $chapter->duration }}</span>
                        </div>
                    </td>
                    <td style="text-align:right;">
                        <button class="icon-btn edit" onclick='editChapter(@json($chapter))'>✏️</button>
                        <form action="{{ route('admin.books.chapters.destroy', [$book, $chapter]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this chapter?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="icon-btn delete">🗑️</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div style="color:#6B7280; font-size:13px; margin-top:14px;">Showing 1 to {{ $chapters->count() }} of {{ $chapters->count() }} entries</div>
</div>

<!-- Add Chapter Modal -->
<div class="modal-backdrop" id="addChapterModal" style="display:none;">
    <div class="modal">
        <div class="modal-header">
            <div class="card-title"><span class="dot">•</span> Add Chapter <span class="dot">•</span></div>
            <span style="cursor:pointer;" onclick="closeModal('addChapterModal')">✕</span>
        </div>
        <form action="{{ route('admin.books.chapters.store', $book) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label>Upload Type</label>
            <div style="display:flex; gap:10px;">
                <label style="display:flex;align-items:center;gap:6px;font-weight:400;margin:0;">
                    <input type="radio" name="upload_type" value="url" onchange="toggleUploadType(this,'add')"> URL
                </label>
                <label style="display:flex;align-items:center;gap:6px;font-weight:400;margin:0;">
                    <input type="radio" name="upload_type" value="file" checked onchange="toggleUploadType(this,'add')"> File
                </label>
            </div>

            <label>Title</label>
            <input type="text" name="title" placeholder="Enter Title">

            <label>Chapter Number</label>
            <input type="number" name="chapter_number" min="1" placeholder="Enter Chapter Number" required>

            <div id="add-urlField" style="display:none;">
                <label>Resource URL</label>
                <input type="url" name="resource_url" placeholder="https://...">
            </div>
            <div id="add-fileField">
                <label>Upload Audio</label>
                <input type="file" name="resource_file" accept="audio/*">
            </div>

            <label>Duration (MM:SS)</label>
            <div style="display:flex; gap:8px; align-items:center;">
                <input type="number" name="duration_min" min="0" placeholder="00" style="width:70px;">
                <span>:</span>
                <input type="number" name="duration_sec" min="0" max="59" placeholder="00" style="width:70px;">
            </div>
            <div style="color:#9CA3AF; font-size:12px; margin-top:4px;">Duration will be auto-detected for uploaded files</div>

            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:22px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addChapterModal')">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Chapter Modal -->
<div class="modal-backdrop" id="editChapterModal" style="display:none;">
    <div class="modal">
        <div class="modal-header">
            <div class="card-title"><span class="dot">•</span> Edit Chapter <span class="dot">•</span></div>
            <span style="cursor:pointer;" onclick="closeModal('editChapterModal')">✕</span>
        </div>
        <form id="editChapterForm" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <label>Upload Type</label>
            <div style="display:flex; gap:10px;">
                <label style="display:flex;align-items:center;gap:6px;font-weight:400;margin:0;">
                    <input type="radio" name="upload_type" value="url" onchange="toggleUploadType(this,'edit')"> URL
                </label>
                <label style="display:flex;align-items:center;gap:6px;font-weight:400;margin:0;">
                    <input type="radio" name="upload_type" value="file" onchange="toggleUploadType(this,'edit')"> File
                </label>
            </div>

            <label>Title</label>
            <input type="text" name="title" id="editChapterTitle">

            <label>Chapter Number</label>
            <input type="number" name="chapter_number" id="editChapterNumber" min="1" required>

            <div id="edit-urlField" style="display:none;">
                <label>Resource URL</label>
                <input type="url" name="resource_url" id="editChapterUrl">
            </div>
            <div id="edit-fileField">
                <label>Current Resource</label>
                <div id="editChapterCurrentAudio" style="margin-bottom:8px;"></div>
                <label>Upload New Audio (optional)</label>
                <input type="file" name="resource_file" accept="audio/*">
                <div style="color:#9CA3AF; font-size:12px; margin-top:4px;">Upload new file to update</div>
            </div>

            <label>Duration (MM:SS)</label>
            <div style="display:flex; gap:8px; align-items:center;">
                <input type="number" name="duration_min" id="editChapterMin" min="0" style="width:70px;">
                <span>:</span>
                <input type="number" name="duration_sec" id="editChapterSec" min="0" max="59" style="width:70px;">
            </div>
            <div style="color:#9CA3AF; font-size:12px; margin-top:4px;">Duration will be auto-detected for uploaded files</div>

            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:22px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editChapterModal')">Close</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleUploadType(radio, prefix) {
        document.getElementById(`${prefix}-urlField`).style.display  = radio.value === 'url'  ? 'block' : 'none';
        document.getElementById(`${prefix}-fileField`).style.display = radio.value === 'file' ? 'block' : 'none';
    }

    function editChapter(chapter) {
        const bookId = {{ $book->id }};
        document.getElementById('editChapterForm').action = `/admin/books/${bookId}/chapters/${chapter.id}`;
        document.getElementById('editChapterTitle').value = chapter.title ?? '';
        document.getElementById('editChapterNumber').value = chapter.chapter_number;
        document.getElementById('editChapterUrl').value = chapter.resource_url ?? '';

        const [min, sec] = (chapter.duration || '0:0').split(':');
        document.getElementById('editChapterMin').value = min;
        document.getElementById('editChapterSec').value = sec;

        document.getElementById('editChapterCurrentAudio').innerHTML =
            chapter.resource_full_url ? `<audio controls src="${chapter.resource_full_url}" style="height:32px;"></audio>` : 'None';

        document.querySelector('#editChapterForm input[value="' + chapter.upload_type + '"]').checked = true;
        toggleUploadType(document.querySelector('#editChapterForm input[value="' + chapter.upload_type + '"]'), 'edit');

        openModal('editChapterModal');
    }
</script>
@endsection
