<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppUpdateSetting extends Model
{
    protected $fillable = [
        'latest_version', 'latest_version_code', 'apk_url', 'release_notes', 'force_update',
    ];

    protected $casts = ['force_update' => 'boolean'];

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }
}
