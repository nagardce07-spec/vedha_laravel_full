<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\TestBatch;
use App\Models\TestBatchAccess;
use App\Models\TestQuestion;
use App\Models\TestTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TestBatchEngineController extends Controller
{
    public function index()
    {
        $stats = [
            'categories' => Category::count(),
            'questions' => TestQuestion::count(),
            'tests' => Test::count(),
            'students' => Customer::count(),
            'batches' => TestBatch::count(),
            'attempts' => TestAttempt::count(),
        ];
        $recentBatches = TestBatch::with(['category','tests'])->withCount('tests')->latest()->limit(8)->get();
        $topTests = Test::with('batch')->withCount('attempts')->orderByDesc('attempts_count')->limit(5)->get();
        $attemptsByDay = TestAttempt::selectRaw('DATE(created_at) day, COUNT(*) total')->where('created_at','>=',now()->subDays(29))->groupBy('day')->pluck('total','day');
        $labels=[]; $data=[];
        for($d=now()->subDays(29)->startOfDay(); $d->lte(now()); $d->addDay()){ $labels[]=$d->format('M d'); $data[]=$attemptsByDay[$d->format('Y-m-d')]??0; }
        return view('admin.test_engine.index', compact('stats','recentBatches','topTests','labels','data'));
    }

    public function topics()
    {
        $topics=TestTopic::with('category')->latest()->paginate(15); $categories=Category::orderBy('name')->get();
        return view('admin.test_engine.topics',compact('topics','categories'));
    }
    public function topicStore(Request $r){ $d=$r->validate(['category_id'=>'nullable|exists:categories,id','name'=>'required|string|max:255']); TestTopic::create($d); return back()->with('success','Topic added.'); }
    public function topicUpdate(Request $r, TestTopic $topic){ $d=$r->validate(['category_id'=>'nullable|exists:categories,id','name'=>'required|string|max:255']); $topic->update($d); return back()->with('success','Topic updated.'); }
    public function topicDestroy(TestTopic $topic){ $topic->delete(); return back()->with('success','Topic deleted.'); }

    public function questions(Request $r)
    {
        $q=TestQuestion::with(['category','topic'])->latest();
        if($r->filled('category_id')) $q->where('category_id',$r->category_id);
        if($r->filled('topic_id')) $q->where('topic_id',$r->topic_id);
        if($r->filled('difficulty')) $q->where('difficulty',$r->difficulty);
        if($r->filled('search')) $q->where('question_text','like','%'.$r->search.'%');
        $questions=$q->paginate(20)->withQueryString(); $categories=Category::orderBy('name')->get(); $topics=TestTopic::orderBy('name')->get();
        return view('admin.test_engine.questions',compact('questions','categories','topics'));
    }
    public function questionStore(Request $r){
        $d=$r->validate(['category_id'=>'nullable|exists:categories,id','topic_id'=>'nullable|exists:test_topics,id','question_type'=>['required',Rule::in(['mcq','true_false'])],'question_text'=>'required|string','options'=>'nullable|array','options.*'=>'nullable|string|max:500','correct_answer'=>'required|string|max:500','explanation'=>'nullable|string','difficulty'=>['required',Rule::in(['easy','medium','hard'])],'tags'=>'nullable|string|max:1000','image_url'=>'nullable|url|max:2048','status'=>'nullable|boolean']);
        $d['status']=$r->boolean('status'); if($d['question_type']==='true_false') $d['options']=['True','False']; TestQuestion::create($d); return back()->with('success','Question added.');
    }
    public function questionUpdate(Request $r, TestQuestion $question){
        $d=$r->validate(['category_id'=>'nullable|exists:categories,id','topic_id'=>'nullable|exists:test_topics,id','question_type'=>['required',Rule::in(['mcq','true_false'])],'question_text'=>'required|string','options'=>'nullable|array','options.*'=>'nullable|string|max:500','correct_answer'=>'required|string|max:500','explanation'=>'nullable|string','difficulty'=>['required',Rule::in(['easy','medium','hard'])],'tags'=>'nullable|string|max:1000','image_url'=>'nullable|url|max:2048','status'=>'nullable|boolean']);
        $d['status']=$r->boolean('status'); if($d['question_type']==='true_false') $d['options']=['True','False']; $question->update($d); return back()->with('success','Question updated.');
    }
    public function questionDestroy(TestQuestion $question){ $question->delete(); return back()->with('success','Question deleted.'); }
    public function questionImport(Request $r){
        $r->validate(['file'=>'required|file|mimes:csv,txt|max:10240']);
        $h=fopen($r->file('file')->getRealPath(),'r'); $header=fgetcsv($h); if(!$header) return back()->withErrors(['file'=>'CSV is empty.']);
        $header=array_map(fn($v)=>strtolower(trim($v)), $header); $count=0;
        while(($row=fgetcsv($h))!==false){ if(count($row)<count($header)) continue; $x=array_combine($header,array_slice($row,count($header))); if(empty(trim($x['question_text']??''))||empty(trim($x['correct_answer']??''))) continue;
            $opts=[]; foreach(['option_a','option_b','option_c','option_d'] as $k) if(!empty($x[$k])) $opts[]=$x[$k];
            TestQuestion::create(['question_type'=>in_array(($x['question_type']??'mcq'),['mcq','true_false'],true)?($x['question_type']??'mcq'):'mcq','question_text'=>$x['question_text'],'options'=>$opts,'correct_answer'=>$x['correct_answer'],'explanation'=>$x['explanation']??null,'difficulty'=>in_array(($x['difficulty']??'medium'),['easy','medium','hard'],true)?($x['difficulty']??'medium'):'medium','tags'=>$x['tags']??null,'status'=>true]); $count++; }
        fclose($h); return back()->with('success',"$count questions imported.");
    }

    public function batches(){ $batches=TestBatch::with('category')->withCount('tests')->latest()->paginate(15); $categories=Category::orderBy('name')->get(); return view('admin.test_engine.batches',compact('batches','categories')); }
    public function batchStore(Request $r){ $d=$r->validate(['title'=>'required|string|max:255','category_id'=>'nullable|exists:categories,id','description'=>'nullable|string','price'=>'nullable|numeric|min:0','validity_days'=>'required|integer|min:1','is_paid'=>'nullable|boolean','status'=>['required',Rule::in(['draft','published','archived'])],'start_at'=>'nullable|date','end_at'=>'nullable|date|after_or_equal:start_at']); $d['is_paid']=$r->boolean('is_paid'); TestBatch::create($d); return back()->with('success','Test batch created.'); }
    public function batchUpdate(Request $r, TestBatch $batch){ $d=$r->validate(['title'=>'required|string|max:255','category_id'=>'nullable|exists:categories,id','description'=>'nullable|string','price'=>'nullable|numeric|min:0','validity_days'=>'required|integer|min:1','is_paid'=>'nullable|boolean','status'=>['required',Rule::in(['draft','published','archived'])],'start_at'=>'nullable|date','end_at'=>'nullable|date|after_or_equal:start_at']); $d['is_paid']=$r->boolean('is_paid'); $batch->update($d); return back()->with('success','Test batch updated.'); }
    public function batchDestroy(TestBatch $batch){ $batch->delete(); return back()->with('success','Test batch deleted.'); }

    public function tests(Request $r){ $tests=Test::with('batch')->withCount('questions','attempts')->latest()->paginate(15); $batches=TestBatch::orderBy('title')->get(); return view('admin.test_engine.tests',compact('tests','batches')); }
    public function testStore(Request $r){
        $d=$r->validate(['test_batch_id'=>'required|exists:test_batches,id','title'=>'required|string|max:255','duration_minutes'=>'required|integer|min:1','marks_per_question'=>'required|numeric|min:0','negative_mark'=>'required|numeric|min:0','status'=>[Rule::in(['draft','published','archived'])],'start_at'=>'nullable|date','end_at'=>'nullable|date|after_or_equal:start_at','shuffle_questions'=>'nullable|boolean','shuffle_options'=>'nullable|boolean','easy_count'=>'nullable|integer|min:0','medium_count'=>'nullable|integer|min:0','hard_count'=>'nullable|integer|min:0']);
        $d['shuffle_questions']=$r->boolean('shuffle_questions'); $d['shuffle_options']=$r->boolean('shuffle_options');
        $dist=['easy'=>(int)($r->easy_count??0),'medium'=>(int)($r->medium_count??0),'hard'=>(int)($r->hard_count??0)]; $d['difficulty_distribution']=$dist; unset($d['easy_count'],$d['medium_count'],$d['hard_count']);
        $test=Test::create($d);
        $query=TestQuestion::where('status',true); $batch=$test->batch; if($batch && $batch->category_id) $query->where('category_id',$batch->category_id);
        $picked=[]; foreach($dist as $difficulty=>$count){ if($count>0){ $ids=(clone $query)->where('difficulty',$difficulty)->inRandomOrder()->limit($count)->pluck('id')->all(); $picked=array_merge($picked,$ids); }}
        if($picked){ $sync=[]; foreach(array_values(array_unique($picked)) as $i=>$id)$sync[$id]=['sort_order'=>$i+1]; $test->questions()->sync($sync); $test->update(['question_count'=>count($sync)]); }
        return back()->with('success','Test created and question selection applied.');
    }
    public function testUpdate(Request $r, Test $test){
        $d=$r->validate(['test_batch_id'=>'required|exists:test_batches,id','title'=>'required|string|max:255','duration_minutes'=>'required|integer|min:1','marks_per_question'=>'required|numeric|min:0','negative_mark'=>'required|numeric|min:0','status'=>[Rule::in(['draft','published','archived'])],'start_at'=>'nullable|date','end_at'=>'nullable|date|after_or_equal:start_at','shuffle_questions'=>'nullable|boolean','shuffle_options'=>'nullable|boolean','easy_count'=>'nullable|integer|min:0','medium_count'=>'nullable|integer|min:0','hard_count'=>'nullable|integer|min:0']);
        $d['shuffle_questions']=$r->boolean('shuffle_questions'); $d['shuffle_options']=$r->boolean('shuffle_options'); $d['difficulty_distribution']=['easy'=>(int)($r->easy_count??0),'medium'=>(int)($r->medium_count??0),'hard'=>(int)($r->hard_count??0)]; unset($d['easy_count'],$d['medium_count'],$d['hard_count']); $test->update($d); return back()->with('success','Test updated.');
    }
    public function testDestroy(Test $test){ $test->delete(); return back()->with('success','Test deleted.'); }
    public function testQuestions(Test $test){ $test->load('batch'); $questions=TestQuestion::with(['category','topic'])->where('status',true)->orderBy('id')->paginate(25); $selected=$test->questions()->pluck('test_questions.id')->all(); return view('admin.test_engine.test_questions',compact('test','questions','selected')); }
    public function syncTestQuestions(Request $r, Test $test){ $ids=$r->input('question_ids',[]); $ids=array_values(array_unique(array_map('intval',$ids))); $valid=TestQuestion::whereIn('id',$ids)->where('status',true)->pluck('id')->all(); $sync=[]; foreach($valid as $i=>$id) $sync[$id]=['sort_order'=>$i+1]; $test->questions()->sync($sync); $test->update(['question_count'=>count($sync)]); return back()->with('success','Test questions updated.'); }

    public function attempts(Request $r){ $q=TestAttempt::with(['customer','test.batch'])->latest(); if($r->filled('status'))$q->where('status',$r->status); if($r->filled('test_id'))$q->where('test_id',$r->test_id); if($r->filled('search'))$q->whereHas('customer',fn($x)=>$x->where('name','like','%'.$r->search.'%')->orWhere('email','like','%'.$r->search.'%')); $attempts=$q->paginate(20)->withQueryString(); $tests=Test::orderBy('title')->get(); return view('admin.test_engine.attempts',compact('attempts','tests')); }
    public function attemptShow(TestAttempt $attempt){ $attempt->load(['customer.subscriptions.plan','test.batch','test.questions','answers.question.topic']); $rank=TestAttempt::where('test_id',$attempt->test_id)->where('status','submitted')->where(function($q)use($attempt){$q->where('score','>',$attempt->score)->orWhere(function($q)use($attempt){$q->where('score',$attempt->score)->where('submitted_at','<',$attempt->submitted_at);});})->count()+1; return view('admin.test_engine.attempt_show',compact('attempt','rank')); }

    public function access(TestBatch $batch){ $batch->load('category'); $accesses=TestBatchAccess::with('customer')->where('test_batch_id',$batch->id)->latest()->paginate(20); $customers=Customer::orderBy('name')->get(); return view('admin.test_engine.access',compact('batch','accesses','customers')); }
    public function accessStore(Request $r, TestBatch $batch){ $d=$r->validate(['customer_id'=>'required|exists:customers,id','starts_at'=>'nullable|date','expires_at'=>'nullable|date|after_or_equal:starts_at','source'=>'nullable|string|max:50']); TestBatchAccess::updateOrCreate(['test_batch_id'=>$batch->id,'customer_id'=>$d['customer_id']],array_merge($d,['status'=>'active'])); return back()->with('success','Batch access assigned.'); }
    public function accessRevoke(TestBatchAccess $access){ $access->update(['status'=>'revoked']); return back()->with('success','Batch access revoked.'); }

    public function reports(){
        $topTests=Test::with('batch')->withCount('attempts')->orderByDesc('attempts_count')->limit(10)->get();
        $questionStats=TestQuestion::with(['topic'])->withCount(['answers as answer_count'=>fn($q)=>$q->whereNotNull('selected_answer')])->withCount(['answers as wrong_answer_count'=>fn($q)=>$q->where('is_correct',false)])->orderByDesc('wrong_answer_count')->limit(15)->get();
        $avgScore=TestAttempt::where('status','submitted')->avg('score'); $submitted=TestAttempt::where('status','submitted')->count();
        return view('admin.test_engine.reports',compact('topTests','questionStats','avgScore','submitted'));
    }

    public function exportAttempts(){
        $name='test_attempts_'.now()->format('Ymd_His').'.csv';
        return response()->streamDownload(function(){ $out=fopen('php://output','w'); fputcsv($out,['Attempt ID','Customer ID','Customer Name','Email','Test','Batch','Status','Score','Correct','Wrong','Skipped','Time Taken (sec)','Started','Submitted']); TestAttempt::with(['customer','test.batch'])->orderBy('id')->chunk(500,function($rows)use($out){ foreach($rows as $a) fputcsv($out,[$a->id,$a->customer_id,$a->customer?->name,$a->customer?->email,$a->test?->title,$a->test?->batch?->title,$a->status,$a->score,$a->correct_count,$a->wrong_count,$a->skipped_count,$a->time_taken_seconds,$a->started_at,$a->submitted_at]); }); fclose($out); },$name,['Content-Type'=>'text/csv']);
    }
}
