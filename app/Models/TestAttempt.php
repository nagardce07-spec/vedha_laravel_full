<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TestAttempt extends Model {
    protected $fillable = ['test_id','customer_id','status','started_at','submitted_at','time_taken_seconds','score','correct_count','wrong_count','skipped_count','rank'];
    protected $casts = ['started_at'=>'datetime','submitted_at'=>'datetime','score'=>'decimal:2'];
    public function test(){ return $this->belongsTo(Test::class); }
    public function customer(){ return $this->belongsTo(Customer::class); }
    public function answers(){ return $this->hasMany(TestAttemptAnswer::class,'attempt_id'); }
}
