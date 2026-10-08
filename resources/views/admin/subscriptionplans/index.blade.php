{{-- resources/views/admin/subscriptionplans/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Subscription Plans')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title"><span class="dot">•</span> Subscription Plans <span class="dot">•</span></div>
        <button class="btn btn-primary" onclick="openModal('addPlanModal')">Add Plan</button>
    </div>

    <table>
        <thead><tr><th>Name</th><th>Price</th><th>Duration</th><th>Best Value</th><th>Active</th><th style="text-align:right;">Action</th></tr></thead>
        <tbody>
            @foreach($plans as $plan)
                <tr>
                    <td>{{ $plan->name }}</td>
                    <td>{{ $plan->currency }} {{ number_format($plan->price, 2) }}</td>
                    <td>{{ $plan->duration_days }} days</td>
                    <td>{{ $plan->is_best_value ? 'Yes' : '-' }}</td>
                    <td>
                        <span class="toggle {{ $plan->is_active ? 'on' : '' }}" onclick="togglePlan({{ $plan->id }}, this)">
                            <span class="knob"></span>
                        </span>
                    </td>
                    <td style="text-align:right;">
                        <button class="icon-btn edit" onclick='editPlan(@json($plan))'>✏️</button>
                        <form action="{{ route('admin.subscriptionplans.destroy', $plan) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this plan?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="icon-btn delete">🗑️</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Add Plan Modal -->
<div class="modal-backdrop" id="addPlanModal" style="display:none;">
    <div class="modal">
        <div class="modal-header">
            <div class="card-title"><span class="dot">•</span> Add Plan <span class="dot">•</span></div>
            <span style="cursor:pointer;" onclick="closeModal('addPlanModal')">✕</span>
        </div>
        <form action="{{ route('admin.subscriptionplans.store') }}" method="POST">
            @csrf
            <label>Name</label>
            <input type="text" name="name" placeholder="Monthly / Yearly" required>
            <label>Price</label>
            <input type="number" step="0.01" name="price" placeholder="299.00" required>
            <label>Currency</label>
            <input type="text" name="currency" value="INR" maxlength="3" required>
            <label>Duration (days)</label>
            <input type="number" name="duration_days" placeholder="30 for monthly, 365 for yearly" required>
            <label>Description</label>
            <input type="text" name="description" placeholder="e.g. Full access to all books">
            <label style="display:flex; align-items:center; gap:8px; margin-top:14px;">
                <input type="checkbox" name="is_best_value" value="1"> Mark as "Best Value"
            </label>
            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:22px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addPlanModal')">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Plan Modal -->
<div class="modal-backdrop" id="editPlanModal" style="display:none;">
    <div class="modal">
        <div class="modal-header">
            <div class="card-title"><span class="dot">•</span> Edit Plan <span class="dot">•</span></div>
            <span style="cursor:pointer;" onclick="closeModal('editPlanModal')">✕</span>
        </div>
        <form id="editPlanForm" method="POST">
            @csrf @method('PUT')
            <label>Name</label>
            <input type="text" name="name" id="editPlanName" required>
            <label>Price</label>
            <input type="number" step="0.01" name="price" id="editPlanPrice" required>
            <label>Currency</label>
            <input type="text" name="currency" id="editPlanCurrency" maxlength="3" required>
            <label>Duration (days)</label>
            <input type="number" name="duration_days" id="editPlanDuration" required>
            <label>Description</label>
            <input type="text" name="description" id="editPlanDescription">
            <label style="display:flex; align-items:center; gap:8px; margin-top:14px;">
                <input type="checkbox" name="is_best_value" id="editPlanBestValue" value="1"> Mark as "Best Value"
            </label>
            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:22px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editPlanModal')">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function editPlan(plan) {
        document.getElementById('editPlanForm').action = `/admin/subscription-plans/${plan.id}`;
        document.getElementById('editPlanName').value = plan.name;
        document.getElementById('editPlanPrice').value = plan.price;
        document.getElementById('editPlanCurrency').value = plan.currency;
        document.getElementById('editPlanDuration').value = plan.duration_days;
        document.getElementById('editPlanDescription').value = plan.description ?? '';
        document.getElementById('editPlanBestValue').checked = plan.is_best_value;
        openModal('editPlanModal');
    }

    function togglePlan(id, el) {
        const turningOn = !el.classList.contains('on');
        el.classList.toggle('on');
        fetch(`/admin/subscription-plans/${id}/toggle`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken },
            body: JSON.stringify({ value: turningOn }),
        }).catch(() => el.classList.toggle('on'));
    }
</script>
@endsection
