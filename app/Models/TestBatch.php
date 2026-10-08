<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TestBatch extends Model {
    protected $fillable = ['title','category_id','description','price','validity_days','is_paid','status','start_at','end_at'];
    protected $casts = ['price'=>'decimal:2','is_paid'=>'boolean','start_at'=>'datetime','end_at'=>'datetime'];
    public function category(){ return $this->belongsTo(Category::class); }
    public function tests(){ return $this->hasMany(Test::class); }
    public function accesses(){ return $this->hasMany(TestBatchAccess::class,'test_batch_id'); }
    public function orders(){ return $this->hasMany(TestBatchOrder::class,'test_batch_id'); }
}
