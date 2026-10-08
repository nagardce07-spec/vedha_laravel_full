<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $fillable = ['title', 'description', 'sent', 'sent_at'];
    protected $casts = ['sent' => 'boolean', 'sent_at' => 'datetime'];
}
