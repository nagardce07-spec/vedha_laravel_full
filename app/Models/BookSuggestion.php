<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookSuggestion extends Model
{
    protected $fillable = ['book_name', 'author_name', 'description', 'suggested_by', 'customer_id'];

    public function customer() { return $this->belongsTo(Customer::class); }
}
