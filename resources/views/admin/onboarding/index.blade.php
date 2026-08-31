{{-- resources/views/admin/onboarding/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Onboarding Screens')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title"><span class="dot">•</span> Onboarding Screens <span class="dot">•</span></div>
        <button class="btn btn-primary" onclick="openModal('addOnboardingModal')">Add Onboarding Screen</button>
    </div>

    <div id="onboardingList">
        @foreach($screens as $screen)
            <div class="onb-row" data-id="{{ $screen->id }}"
                 style="display:flex; align-items:center; gap:16px; padding:14px 8px; border-bottom:1px solid #E5E7EB; cursor:grab;">
                <span style="color:#9CA3AF;">⠿⠿</span>
                <span style="width:20px; font-weight:600;">{{ $loop->iteration }}</span>
                <img src="{{ $screen->image_url }}" style="width:56px;height:56px;border-radius:10px;object-fit:cover;">
                <div style="flex:1;">
                    <div style="font-weight:600;">{{ $screen->title }}</div>
                    <div style="color:#9CA3AF; font-size:13px;">{{ $screen->description }}</div>
                </div>
                <button class="icon-btn edit" onclick='editScreen(@json($screen))'>✏️</button>
                <form action="{{ route('admin.onboarding.destroy', $screen) }}" method="POST" onsubmit="return confirm('Delete this screen?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="icon-btn delete">🗑️</button>
                </form>
            </div>
        @endforeach
    </div>
</div>

<!-- Add Onboarding Screen Modal -->
<div class="modal-backdrop" id="addOnboardingModal" style="display:none;">
    <div class="modal">
        <div class="modal-header">
            <div class="card-title"><span class="dot">•</span> Add Onboarding Screen <span class="dot">•</span></div>
            <span style="cursor:pointer;" onclick="closeModal('addOnboardingModal')">✕</span>
        </div>
        <form action="{{ route('admin.onboarding.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label>Title</label>
            <input type="text" name="title" placeholder="Enter Title" required>
            <label>Description</label>
            <textarea name="description" rows="3" placeholder="Enter Description"></textarea>
            <label>Image</label>
            <input type="file" name="image" accept="image/*" required>
            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:22px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addOnboardingModal')">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Onboarding Screen Modal -->
<div class="modal-backdrop" id="editOnboardingModal" style="display:none;">
    <div class="modal">
        <div class="modal-header">
            <div class="card-title"><span class="dot">•</span> Edit Onboarding Screen <span class="dot">•</span></div>
            <span style="cursor:pointer;" onclick="closeModal('editOnboardingModal')">✕</span>
        </div>
        <form id="editOnboardingForm" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <label>Title</label>
            <input type="text" name="title" id="editOnbTitle" required>
            <label>Description</label>
            <textarea name="description" id="editOnbDescription" rows="3"></textarea>
            <label>Image (leave empty to keep current)</label>
            <input type="file" name="image" accept="image/*">
            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:22px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editOnboardingModal')">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function editScreen(screen) {
        document.getElementById('editOnboardingForm').action = `/admin/onboarding-screens/${screen.id}`;
        document.getElementById('editOnbTitle').value = screen.title;
        document.getElementById('editOnbDescription').value = screen.description ?? '';
        openModal('editOnboardingModal');
    }

    let dragEl = null;
    document.querySelectorAll('.onb-row').forEach(row => {
        row.draggable = true;
        row.addEventListener('dragstart', () => dragEl = row);
        row.addEventListener('dragover', e => e.preventDefault());
        row.addEventListener('drop', function () {
            if (dragEl && dragEl !== this) {
                this.parentNode.insertBefore(dragEl, this.nextSibling);
                const order = [...document.querySelectorAll('.onb-row')].map(r => r.dataset.id);
                fetch(`{{ route('admin.onboarding.reorder') }}`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken },
                    body: JSON.stringify({ order }),
                });
            }
        });
    });
</script>
@endsection
