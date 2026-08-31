{{-- resources/views/admin/reviews/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Review')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title"><span class="dot">•</span> Review <span class="dot">•</span></div>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
        <div style="color:#6B7280; font-size:14px;">Show 10 entries</div>
        <input type="text" class="search-input" placeholder="Search:" onkeyup="filterTable(this, 'reviewTable')">
    </div>

    <table id="reviewTable">
        <thead><tr><th>Book Name</th><th>User Name</th><th>Rating</th><th>Review</th><th style="text-align:right;">Action</th></tr></thead>
        <tbody>
            @foreach($reviews as $review)
                <tr>
                    <td>{{ optional($review->book)->name }}</td>
                    <td>{{ $review->user_name ?? optional($review->customer)->name }}</td>
                    <td><span class="badge">⭐ {{ $review->rating }}</span></td>
                    <td>{{ $review->review }}</td>
                    <td style="text-align:right;">
                        <button class="icon-btn edit" onclick='editReview(@json($review))'>✏️</button>
                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this review?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="icon-btn delete">🗑️</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:14px;">
        <span style="color:#6B7280; font-size:13px;">Showing {{ $reviews->firstItem() }} to {{ $reviews->lastItem() }} of {{ $reviews->total() }} entries</span>
        <div class="pagination">{{ $reviews->links() }}</div>
    </div>
</div>

<!-- Edit Review Modal -->
<div class="modal-backdrop" id="editReviewModal" style="display:none;">
    <div class="modal">
        <div class="modal-header">
            <div class="card-title"><span class="dot">•</span> Edit Review <span class="dot">•</span></div>
            <span style="cursor:pointer;" onclick="closeModal('editReviewModal')">✕</span>
        </div>
        <form id="editReviewForm" method="POST">
            @csrf @method('PUT')
            <label>Rating (1–5)</label>
            <input type="number" name="rating" id="editReviewRating" min="1" max="5" required>
            <label>Review</label>
            <textarea name="review" id="editReviewText" rows="3"></textarea>
            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:22px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editReviewModal')">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function editReview(review) {
        document.getElementById('editReviewForm').action = `/admin/reviews/${review.id}`;
        document.getElementById('editReviewRating').value = review.rating;
        document.getElementById('editReviewText').value = review.review ?? '';
        openModal('editReviewModal');
    }
    function filterTable(input, tableId) {
        const filter = input.value.toLowerCase();
        document.querySelectorAll(`#${tableId} tbody tr`).forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
        });
    }
</script>
@endsection
