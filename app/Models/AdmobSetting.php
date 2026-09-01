<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmobSetting extends Model
{
    protected $fillable = [
        'android_banner_id', 'android_interstitial_id',
        'ios_banner_id', 'ios_interstitial_id',
    ];

    // Always operate on the single settings row (id = 1).
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }
}
