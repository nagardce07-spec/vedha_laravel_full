<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\PaymentSetting;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\TestAttemptAnswer;
use App\Models\TestBatch;
use App\Models\TestBatchAccess;
use App\Models\TestBatchOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api as RazorpayApi;

class TestBatchController extends Controller
{
    private function accessFor(int $customerId, int $batchId): ?TestBatchAccess {
        return TestBatchAccess::where('customer_id',$customerId)->where('test_batch_id',$batchId)->where('status','active')->where(function($q){$q->whereNull('expires_at')->orWhere('expires_at','>',now());})->first();
    }

    public function index(Request $request) {
        $customerId=$request->user()?->id;
        $batches=TestBatch::with('category')->withCount('tests')->where('status','published')->latest()->get()->map(function($b) use($customerId){
            $access=$customerId?$this->accessFor($customerId,$b->id):null;
            $questionCount=$b->tests()->sum('question_count');
            return ['id'=>$b->id,'title'=>$b->title,'description'=>$b->description,'price'=>(float)$b->price,'validity_days'=>$b->validity_days,'is_paid'=>(bool)$b->is_paid,'status'=>$b->status,'category'=>$b->category?['id'=>$b->category->id,'name'=>$b->category->name]:null,'tests_count'=>$b->tests_count,'question_count'=>$questionCount,'has_access'=>(bool)$access,'expires_at'=>$access?->expires_at];
        });
        return response()->json($batches);
    }

    public function show(Request $request, TestBatch $batch) {
        abort_unless($batch->status==='published',404);
        $customerId=$request->user()?->id; $access=$customerId?$this->accessFor($customerId,$batch->id):null;
        $tests=$batch->tests()->where('status','published')->orderBy('id')->get()->map(fn($t)=>['id'=>$t->id,'title'=>$t->title,'duration_minutes'=>$t->duration_minutes,'marks_per_question'=>(float)$t->marks_per_question,'negative_mark'=>(float)$t->negative_mark,'question_count'=>$t->question_count,'status'=>$t->status,'has_attempt'=>$customerId?TestAttempt::where('customer_id',$customerId)->where('test_id',$t->id)->where('status','submitted')->exists():false]);
        return response()->json(['batch'=>['id'=>$batch->id,'title'=>$batch->title,'description'=>$batch->description,'price'=>(float)$batch->price,'validity_days'=>$batch->validity_days,'is_paid'=>(bool)$batch->is_paid,'category'=>$batch->category?['id'=>$batch->category->id,'name'=>$batch->category->name]:null,'has_access'=>(bool)$access,'expires_at'=>$access?->expires_at],'tests'=>$tests]);
    }

    public function enroll(Request $request, TestBatch $batch) {
        abort_unless($batch->status==='published',404); $customer=$request->user();
        if($batch->is_paid) return response()->json(['message'=>'This is a paid batch. Please use Buy Now.'],402);
        $expires=now()->addDays($batch->validity_days);
        $access=TestBatchAccess::updateOrCreate(['test_batch_id'=>$batch->id,'customer_id'=>$customer->id],['starts_at'=>now(),'expires_at'=>$expires,'status'=>'active','source'=>'free']);
        return response()->json(['enrolled'=>true,'expires_at'=>$access->expires_at]);
    }

    public function createOrder(Request $request, TestBatch $batch) {
        abort_unless($batch->status==='published',404); if(!$batch->is_paid) return $this->enroll($request,$batch);
        if($this->accessFor($request->user()->id,$batch->id)) return response()->json(['message'=>'Already enrolled.'],409);
        $settings=PaymentSetting::current()->makeVisible('razorpay_key_secret'); $api=new RazorpayApi($settings->razorpay_key_id,$settings->razorpay_key_secret);
        $amount=(int)round(((float)$batch->price)*100); $order=$api->order->create(['receipt'=>'batch_'.$batch->id.'_user_'.$request->user()->id.'_'.time(),'amount'=>$amount,'currency'=>$settings->currency ?: 'INR']);
        $row=TestBatchOrder::create(['test_batch_id'=>$batch->id,'customer_id'=>$request->user()->id,'razorpay_order_id'=>$order['id'],'amount'=>$batch->price,'currency'=>$settings->currency ?: 'INR','status'=>'pending']);
        return response()->json(['order_id'=>$order['id'],'order_row_id'=>$row->id,'amount'=>$amount,'currency'=>$settings->currency ?: 'INR','key_id'=>$settings->razorpay_key_id,'batch_title'=>$batch->title]);
    }

    public function verifyOrder(Request $request) {
        $data=$request->validate(['order_row_id'=>'required|exists:test_batch_orders,id','razorpay_order_id'=>'required','razorpay_payment_id'=>'required','razorpay_signature'=>'required']);
        $row=TestBatchOrder::where('id',$data['order_row_id'])->where('customer_id',$request->user()->id)->firstOrFail(); $settings=PaymentSetting::current()->makeVisible('razorpay_key_secret'); $api=new RazorpayApi($settings->razorpay_key_id,$settings->razorpay_key_secret);
        try{$api->utility->verifyPaymentSignature(['razorpay_order_id'=>$data['razorpay_order_id'],'razorpay_payment_id'=>$data['razorpay_payment_id'],'razorpay_signature'=>$data['razorpay_signature']]);}catch(\Throwable $e){$row->update(['status'=>'failed']);return response()->json(['verified'=>false,'message'=>'Signature verification failed.'],422);}
        $starts=now();$expires=$starts->copy()->addDays($row->batch->validity_days); DB::transaction(function() use($row,$data,$starts,$expires){$row->update(['razorpay_payment_id'=>$data['razorpay_payment_id'],'razorpay_signature'=>$data['razorpay_signature'],'status'=>'paid','starts_at'=>$starts,'expires_at'=>$expires]);TestBatchAccess::updateOrCreate(['test_batch_id'=>$row->test_batch_id,'customer_id'=>$row->customer_id],['starts_at'=>$starts,'expires_at'=>$expires,'status'=>'active','source'=>'razorpay']);});
        return response()->json(['verified'=>true,'expires_at'=>$expires]);
    }

    public function test(Request $request, Test $test) {
        $batch=$test->batch; abort_unless($test->status==='published' && $batch->status==='published',404); $customer=$request->user();
        if(!$this->accessFor($customer->id,$batch->id)) return response()->json(['message'=>'You do not have access to this batch.'],403);
        $attempt=TestAttempt::firstOrCreate(['test_id'=>$test->id,'customer_id'=>$customer->id,'status'=>'in_progress'],['started_at'=>now()]);
        $questions=$test->questions()->get()->map(fn($q)=>['id'=>$q->id,'question_text'=>$q->question_text,'options'=>$test->shuffle_options?collect($q->options??[])->shuffle()->values()->all():($q->options??[]),'question_type'=>$q->question_type,'topic_id'=>$q->topic_id,'difficulty'=>$q->difficulty]);
        if($test->shuffle_questions) $questions=$questions->shuffle()->values();
        return response()->json(['attempt_id'=>$attempt->id,'attempt_started_at'=>$attempt->started_at,'test'=>['id'=>$test->id,'title'=>$test->title,'duration_minutes'=>$test->duration_minutes,'marks_per_question'=>(float)$test->marks_per_question,'negative_mark'=>(float)$test->negative_mark,'question_count'=>$test->question_count],'questions'=>$questions]);
    }

    public function submit(Request $request, TestAttempt $attempt) {
        abort_unless($attempt->customer_id===$request->user()->id,403); if($attempt->status!=='in_progress') return response()->json(['message'=>'Attempt already submitted.'],409);
        $data=$request->validate(['answers'=>'required|array','answers.*.question_id'=>'required|integer|exists:test_questions,id','answers.*.selected_answer'=>'nullable|string','answers.*.time_taken_seconds'=>'nullable|integer|min:0']);
        $test=$attempt->test()->with('questions')->first(); $byId=$test->questions->keyBy('id'); $correct=0;$wrong=0;$skipped=0;$score=0;$time=0;
        DB::transaction(function() use($attempt,$data,$byId,$test,&$correct,&$wrong,&$skipped,&$score,&$time){foreach($data['answers'] as $a){$q=$byId->get($a['question_id']);if(!$q)continue;$selected=$a['selected_answer']??null;$t=(int)($a['time_taken_seconds']??0);$time+=$t;$isCorrect=$selected!==null&&trim($selected)==trim($q->correct_answer);if($selected===null||$selected===''){$skipped++;$marks=0;$isCorrect=null;}elseif($isCorrect){$correct++;$marks=(float)$test->marks_per_question;$score+=$marks;}else{$wrong++;$marks=-(float)$test->negative_mark;$score+=$marks;}TestAttemptAnswer::updateOrCreate(['attempt_id'=>$attempt->id,'question_id'=>$q->id],['selected_answer'=>$selected,'is_correct'=>$isCorrect,'marks_awarded'=>$marks,'time_taken_seconds'=>$t]);}$attempt->update(['status'=>'submitted','submitted_at'=>now(),'time_taken_seconds'=>$time,'score'=>$score,'correct_count'=>$correct,'wrong_count'=>$wrong,'skipped_count'=>$skipped]);});
        $rank=TestAttempt::where('test_id',$test->id)->where('status','submitted')->where('score','>', $attempt->score)->count()+1; $attempt->update(['rank'=>$rank]);
        return $this->resultData($attempt->fresh()->load('test.batch'));
    }

    private function resultData(TestAttempt $a){return ['attempt_id'=>$a->id,'test_id'=>$a->test_id,'test_title'=>$a->test->title,'batch_title'=>$a->test->batch->title,'score'=>(float)$a->score,'correct'=>$a->correct_count,'wrong'=>$a->wrong_count,'skipped'=>$a->skipped_count,'time_taken_seconds'=>$a->time_taken_seconds,'rank'=>$a->rank,'status'=>$a->status];}

    public function result(Request $request, TestAttempt $attempt){
        abort_unless($attempt->customer_id===$request->user()->id,403);
        $attempt->load(['test.batch','answers.question.topic']);
        $data=$this->resultData($attempt);
        $data['answers']=$attempt->answers->map(fn($a)=>['question_id'=>$a->question_id,'question_text'=>$a->question->question_text,'options'=>$a->question->options??[],'selected_answer'=>$a->selected_answer,'correct_answer'=>$a->question->correct_answer,'is_correct'=>$a->is_correct,'marks_awarded'=>(float)$a->marks_awarded,'explanation'=>$a->question->explanation,'topic'=>$a->question->topic->name??null]);
        return response()->json($data);
    }
    public function history(Request $request){return response()->json(TestAttempt::with('test.batch')->where('customer_id',$request->user()->id)->where('status','submitted')->latest()->limit(100)->get()->map(fn($a)=>$this->resultData($a)));}
    public function analytics(Request $request){$attempts=TestAttempt::with('answers.question.topic')->where('customer_id',$request->user()->id)->where('status','submitted')->get();$stats=[];foreach($attempts as $a){foreach($a->answers as $ans){$name=$ans->question->topic->name??'General';$stats[$name]??=['correct'=>0,'wrong'=>0,'skipped'=>0,'total'=>0];$stats[$name]['total']++;if($ans->is_correct===true)$stats[$name]['correct']++;elseif($ans->selected_answer===null||$ans->selected_answer==='')$stats[$name]['skipped']++;else $stats[$name]['wrong']++;}}foreach($stats as $k=>$v)$stats[$k]['percentage']=$v['total']?round($v['correct']/$v['total']*100,1):0;return response()->json(['topics'=>$stats]);}
    public function myBatches(Request $request){$rows=TestBatchAccess::with('batch.category')->where('customer_id',$request->user()->id)->latest()->get();return response()->json($rows->map(fn($a)=>['id'=>$a->batch->id,'title'=>$a->batch->title,'status'=>$a->status,'expires_at'=>$a->expires_at,'validity_days'=>$a->batch->validity_days,'tests_count'=>$a->batch->tests()->count()]));}
}
