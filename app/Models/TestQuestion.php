<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TestQuestion extends Model {
    protected $fillable = ['category_id','topic_id','question_type','question_text','options','correct_answer','explanation','difficulty','tags','image_url','status'];
    protected $casts = ['options'=>'array','status'=>'boolean'];
    public function category(){ return $this->belongsTo(Category::class); }
    public function topic(){ return $this->belongsTo(TestTopic::class,'topic_id'); }
    public function tests(){ return $this->belongsToMany(Test::class,'test_question')->withPivot('sort_order'); }
    public function answers(){ return $this->hasMany(TestAttemptAnswer::class,'question_id'); }
}
