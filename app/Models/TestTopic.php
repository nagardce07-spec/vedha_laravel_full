<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TestTopic extends Model {
    protected $fillable = ['category_id','name','status'];
    protected $casts = ['status'=>'boolean'];
    public function category(){ return $this->belongsTo(Category::class); }
    public function questions(){ return $this->hasMany(TestQuestion::class,'topic_id'); }
}
