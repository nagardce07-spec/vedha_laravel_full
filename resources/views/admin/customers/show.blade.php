@extends('layouts.admin')
@section('title','Customer Details')
@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
 <div><div style="color:#6B7280;font-size:13px;">Customers / Details</div><h2 style="margin:4px 0;">Customer #{{ $customer->id }} — {{ $customer->name }}</h2></div>
 <a class="btn btn-secondary" href="{{ route('admin.customers.index') }}">← Back</a>
</div>
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:18px;">
 @foreach([['Customer ID',$customer->id],['Premium',$premium?'Active':'No'],['Tests Attempted',$attempts->count()],['Test Batches',$customer->testBatchAccesses->count()]] as $x)
 <div class="card"><div style="color:#6B7280;font-size:13px;">{{ $x[0] }}</div><div style="font-size:24px;font-weight:700;margin-top:5px;">{{ $x[1] }}</div></div>
 @endforeach
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:18px;">
 <div class="card"><div class="card-title">Customer Information</div>
  <table><tr><th>Customer ID</th><td>{{ $customer->id }}</td></tr><tr><th>Name</th><td>{{ $customer->name }}</td></tr><tr><th>Username</th><td>{{ $customer->username ?? '—' }}</td></tr><tr><th>Mobile</th><td>{{ $customer->phone ?? '—' }}</td></tr><tr><th>Email</th><td>{{ $customer->email }}</td></tr><tr><th>Device Type</th><td>{{ $customer->device_type }}</td></tr><tr><th>DOB</th><td>{{ $customer->dob ? \Illuminate\Support\Carbon::parse($customer->dob)->format('d M Y') : '—' }}</td></tr><tr><th>Gender</th><td>{{ $customer->gender ?? '—' }}</td></tr><tr><th>Premium</th><td><span class="badge">{{ $premium?'Active':'Inactive' }}</span></td></tr></table>
 </div>
 <div class="card"><div class="card-title">Purchased / Assigned Test Batches</div>
  <table><thead><tr><th>Batch</th><th>Status</th><th>Expires</th></tr></thead><tbody>@forelse($customer->testBatchAccesses as $a)<tr><td>{{ $a->batch->title ?? '—' }}</td><td>{{ ucfirst($a->status) }}</td><td>{{ optional($a->expires_at)->format('d M Y') ?? '—' }}</td></tr>@empty<tr><td colspan="3">No batch access.</td></tr>@endforelse</tbody></table>
 </div>
</div>
<div class="card" style="margin-bottom:18px;"><div class="card-header"><div class="card-title">Test History</div><a class="btn btn-outline" href="{{ route('admin.testengine.attempts',['search'=>$customer->email]) }}">View All Attempts</a></div>
<table><thead><tr><th>Test</th><th>Batch</th><th>Score</th><th>Correct</th><th>Wrong</th><th>Skipped</th><th>Time</th><th>Rank</th><th>Status</th></tr></thead><tbody>
@forelse($attempts as $a)<tr><td><a style="color:#7C3AED;font-weight:600;" href="{{ route('admin.testengine.attempts.show',$a) }}">{{ $a->test->title ?? '—' }}</a></td><td>{{ $a->test->batch->title ?? '—' }}</td><td>{{ $a->score }}</td><td>{{ $a->correct_count }}</td><td>{{ $a->wrong_count }}</td><td>{{ $a->skipped_count }}</td><td>{{ gmdate('H:i:s',(int)$a->time_taken_seconds) }}</td><td>{{ $a->rank ?? '—' }}</td><td>{{ ucfirst(str_replace('_',' ',$a->status)) }}</td></tr>@empty<tr><td colspan="9">No test attempts yet.</td></tr>@endforelse
</tbody></table></div>
<div class="card"><div class="card-title" style="margin-bottom:12px;">Topic-wise Performance</div><table><thead><tr><th>Topic</th><th>Correct</th><th>Wrong</th><th>Skipped</th><th>Total</th><th>Success %</th></tr></thead><tbody>@forelse($topicStats as $topic=>$v)<tr><td>{{ $topic }}</td><td>{{ $v['correct'] }}</td><td>{{ $v['wrong'] }}</td><td>{{ $v['skipped'] }}</td><td>{{ $v['total'] }}</td><td>{{ $v['total'] ? round(($v['correct']/$v['total'])*100,1) : 0 }}%</td></tr>@empty<tr><td colspan="6">No topic performance yet.</td></tr>@endforelse</tbody></table></div>
@endsection
