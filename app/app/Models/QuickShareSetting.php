<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuickShareSetting extends Model
{
    protected $fillable = [
        'app_scheme', 'play_store_link', 'app_store_link',
        'android_package_name', 'ios_bundle_id', 'ios_team_id',
    ];

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }
}
