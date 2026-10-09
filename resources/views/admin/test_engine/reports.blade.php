@extends('layouts.admin')
@section('title','Test Reports')
@section('content')
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:18px">
    <div class="card"><div style="color:#6B7280">Submitted Attempts</div><div style="font-size:30px;font-weight:700">{{ $submitted }}</div></div>
    <div class="card"><div style="color:#6B7280">Average Score</div><div style="font-size:30px;font-weight:700">{{ round($avgScore??0,2) }}</div></div>
    <div class="card"><div style="color:#6B7280">Categories With Questions</div><div style="font-size:30px;font-weight:700">{{ $categoryTotal }}</div></div>
    <div class="card"><div style="color:#6B7280">Topics</div><div style="font-size:30px;font-weight:700">{{ $topicTotal }}</div></div>
</div>
<div class="card" style="margin-bottom:18px">
    <div class="card-title" style="margin-bottom:12px">Submitted Tests — Last 30 Days</div>
    <div style="height:220px;display:flex;align-items:end;gap:4px;padding:12px 4px 24px;border-bottom:1px solid #E5E7EB;overflow-x:auto">
        @php($maxDaily = max(1, max($dailyData ?: [0])))
        @foreach($dailyData as $i=>$value)
            <div title="{{ $dailyLabels[$i] }}: {{ $value }} attempts" style="min-width:10px;flex:1;height:{{ max(2, ($value/$maxDaily)*170) }}px;background:#7C3AED;border-radius:4px 4px 0 0;position:relative"></div>
        @endforeach
    </div>
    <div style="display:flex;justify-content:space-between;color:#6B7280;font-size:12px;margin-top:8px"><span>{{ $dailyLabels[0] ?? '' }}</span><span>{{ $dailyLabels[count($dailyLabels)-1] ?? '' }}</span></div>
</div>
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:18px;margin-bottom:18px">
    <div class="card"><div class="card-title" style="margin-bottom:12px">Most Attempted Categories</div><table><thead><tr><th>Category</th><th>Answers</th><th>Share</th></tr></thead><tbody>
    @forelse($categoryRows as $row)<tr><td>{{ $row->label }}</td><td>{{ $row->total }}</td><td style="min-width:100px"><div style="background:#EDE9FE;height:8px;border-radius:8px"><div style="background:#7C3AED;width:{{ min(100,($row->total/max(1,$categoryRows->max('total')))*100) }}%;height:8px;border-radius:8px"></div></div></td></tr>@empty<tr><td colspan="3">No submitted answer data yet.</td></tr>@endforelse
    </tbody></table></div>
    <div class="card"><div class="card-title" style="margin-bottom:12px">Most Attempted Topics</div><table><thead><tr><th>Topic</th><th>Answers</th><th>Share</th></tr></thead><tbody>
    @forelse($topicRows as $row)<tr><td>{{ $row->label }}</td><td>{{ $row->total }}</td><td style="min-width:100px"><div style="background:#EDE9FE;height:8px;border-radius:8px"><div style="background:#7C3AED;width:{{ min(100,($row->total/max(1,$topicRows->max('total')))*100) }}%;height:8px;border-radius:8px"></div></div></td></tr>@empty<tr><td colspan="3">No submitted answer data yet.</td></tr>@endforelse
    </tbody></table></div>
</div>
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:18px">
    <div class="card"><div class="card-title" style="margin-bottom:12px">Top Tests by Attempts</div><table><thead><tr><th>Test</th><th>Attempts</th></tr></thead><tbody>@forelse($topTests as $t)<tr><td>{{ $t->title }}</td><td>{{ $t->attempts_count }}</td></tr>@empty<tr><td colspan="2">No submitted attempts yet.</td></tr>@endforelse</tbody></table></div>
    <div class="card"><div class="card-title" style="margin-bottom:12px">Most Wrong Questions</div><table><thead><tr><th>Question</th><th>Wrong</th></tr></thead><tbody>@forelse($questionStats as $q)<tr><td>{{ \Illuminate\Support\Str::limit($q->question_text,80) }}</td><td>{{ $q->wrong_answer_count }}</td></tr>@empty<tr><td colspan="2">No answer data yet.</td></tr>@endforelse</tbody></table></div>
</div>
@endsection
