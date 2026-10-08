<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TestBatchAccess extends Model {
    protected $fillable = ['test_batch_id','customer_id','starts_at','expires_at','status','source'];
    protected $casts = ['starts_at'=>'datetime','expires_at'=>'datetime'];
    public function batch(){ return $this->belongsTo(TestBatch::class,'test_batch_id'); }
    public function customer(){ return $this->belongsTo(Customer::class); }
}
