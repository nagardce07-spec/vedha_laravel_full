<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = ['theme_color', 'theme_light_color', 'theme_background_color'];

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }
}
