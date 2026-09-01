{{-- resources/views/admin/notifications/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Notifications')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title"><span class="dot">•</span> Notifications <span class="dot">•</span></div>
        <button class="btn btn-primary" onclick="openModal('addNotificationModal')">Add Notification</button>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
        <div style="color:#6B7280; font-size:14px;">Show 10 entries</div>
        <input type="text" class="search-input" placeholder="Search:" onkeyup="filterTable(this, 'notifTable')">
    </div>

    <table id="notifTable">
        <thead><tr><th>Notification</th><th style="text-align:right;">Action</th></tr></thead>
        <tbody>
            @foreach($notifications as $n)
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $n->title }}</div>
                        <div style="color:#9CA3AF; font-size:12.5px;">{{ \Illuminate\Support\Str::limit($n->description, 90) }}</div>
                    </td>
                    <td style="text-align:right; white-space:nowrap;">
                        <form action="{{ route('admin.notifications.send', $n) }}" method="POST" style="display:inline;" onsubmit="return confirm('Send this push notification to all users?')">
                            @csrf
                            <button type="submit" class="icon-btn" title="Send">📨</button>
                        </form>
                        <button class="icon-btn edit" onclick='editNotification(@json($n))'>✏️</button>
                        <form action="{{ route('admin.notifications.destroy', $n) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this notification?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="icon-btn delete">🗑️</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:14px;">
        <span style="color:#6B7280; font-size:13px;">Showing {{ $notifications->firstItem() }} to {{ $notifications->lastItem() }} of {{ $notifications->total() }} entries</span>
        <div class="pagination">{{ $notifications->links() }}</div>
    </div>
</div>

<!-- Add Notification Modal -->
<div class="modal-backdrop" id="addNotificationModal" style="display:none;">
    <div class="modal">
        <div class="modal-header">
            <div class="card-title"><span class="dot">•</span> Add Notification <span class="dot">•</span></div>
            <span style="cursor:pointer;" onclick="closeModal('addNotificationModal')">✕</span>
        </div>
        <form action="{{ route('admin.notifications.store') }}" method="POST">
            @csrf
            <label>Title</label>
            <input type="text" name="title" placeholder="Enter Title" required>
            <label>Description</label>
            <textarea name="description" rows="3" placeholder="Enter Description" required></textarea>

            <div style="display:flex; align-items:flex-start; gap:10px; background:#F5F3FF; border:1px solid #DDD6FE; border-radius:10px; padding:14px; margin-top:16px;">
                <input type="checkbox" name="send_now" value="1" checked style="margin-top:3px;">
                <div>
                    <div style="font-weight:600; font-size:14px;">Send Push Notification to All Users</div>
                    <div style="color:#6B7280; font-size:12.5px;">Check this to send instant push notification to all mobile app users</div>
                </div>
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:22px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addNotificationModal')">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Notification Modal -->
<div class="modal-backdrop" id="editNotificationModal" style="display:none;">
    <div class="modal">
        <div class="modal-header">
            <div class="card-title"><span class="dot">•</span> Edit Notification <span class="dot">•</span></div>
            <span style="cursor:pointer;" onclick="closeModal('editNotificationModal')">✕</span>
        </div>
        <form id="editNotificationForm" method="POST">
            @csrf @method('PUT')
            <label>Title</label>
            <input type="text" name="title" id="editNotifTitle" required>
            <label>Description</label>
            <textarea name="description" id="editNotifDescription" rows="3" required></textarea>
            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:22px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editNotificationModal')">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function editNotification(n) {
        document.getElementById('editNotificationForm').action = `/admin/notifications/${n.id}`;
        document.getElementById('editNotifTitle').value = n.title;
        document.getElementById('editNotifDescription').value = n.description;
        openModal('editNotificationModal');
    }
    function filterTable(input, tableId) {
        const filter = input.value.toLowerCase();
        document.querySelectorAll(`#${tableId} tbody tr`).forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
        });
    }
</script>
@endsection
