<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TestAttemptAnswer extends Model {
    protected $fillable = ['attempt_id','question_id','selected_answer','is_correct','marks_awarded','time_taken_seconds'];
    protected $casts = ['is_correct'=>'boolean','marks_awarded'=>'decimal:2'];
    public function attempt(){ return $this->belongsTo(TestAttempt::class,'attempt_id'); }
    public function question(){ return $this->belongsTo(TestQuestion::class,'question_id'); }
}
