<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Test extends Model {
    protected $fillable = ['test_batch_id','title','duration_minutes','marks_per_question','negative_mark','question_count','difficulty_distribution','shuffle_questions','shuffle_options','status','start_at','end_at'];
    protected $casts = ['difficulty_distribution'=>'array','marks_per_question'=>'decimal:2','negative_mark'=>'decimal:2','shuffle_questions'=>'boolean','shuffle_options'=>'boolean','start_at'=>'datetime','end_at'=>'datetime'];
    public function batch(){ return $this->belongsTo(TestBatch::class,'test_batch_id'); }
    public function questions(){ return $this->belongsToMany(TestQuestion::class,'test_question')->withPivot('sort_order')->orderBy('sort_order'); }
    public function attempts(){ return $this->hasMany(TestAttempt::class); }
}
