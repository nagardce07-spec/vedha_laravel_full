<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class CustomerController extends Controller
{
    public function index(){ 
        $customers=Customer::select('customers.*')->selectSub(function($q){ $q->from('customer_subscriptions')->whereColumn('customer_subscriptions.customer_id','customers.id')->where('status','active')->whereNotNull('expires_at')->where('expires_at','>',now())->selectRaw('1')->limit(1); }, 'premium_active')->withCount(['likes','reviews','testAttempts'])->latest()->paginate(10); return view('admin.customers.index',compact('customers')); }
    public function show(Customer $customer){
        $customer->load(['subscriptions.plan','testBatchAccesses.batch.category','testAttempts.test.batch','testAttempts.answers.question.topic']);
        $premium=$customer->subscriptions->contains(fn($s)=>$s->isCurrentlyActive());
        $attempts=$customer->testAttempts->sortByDesc('created_at');
        $topicStats=[];
        foreach($attempts as $attempt){ foreach($attempt->answers as $ans){ $topic=$ans->question?->topic?->name ?? 'Uncategorized'; if(!isset($topicStats[$topic]))$topicStats[$topic]=['correct'=>0,'wrong'=>0,'skipped'=>0,'total'=>0]; $topicStats[$topic]['total']++; if($ans->selected_answer===null)$topicStats[$topic]['skipped']++; elseif($ans->is_correct)$topicStats[$topic]['correct']++; else $topicStats[$topic]['wrong']++; }}
        return view('admin.customers.show',compact('customer','premium','attempts','topicStats'));
    }
}
