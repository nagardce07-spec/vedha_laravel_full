<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TestBatchOrder extends Model {
    protected $fillable=['test_batch_id','customer_id','razorpay_order_id','razorpay_payment_id','razorpay_signature','amount','currency','status','starts_at','expires_at'];
    protected $casts=['amount'=>'decimal:2','starts_at'=>'datetime','expires_at'=>'datetime'];
    public function batch(){return $this->belongsTo(TestBatch::class,'test_batch_id');}
    public function customer(){return $this->belongsTo(Customer::class);}
}
