<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GeneralSetting extends Model
{
    protected $fillable = [
        'title', 'favicon_path', 'logo_light_path', 'login_image_path', 'storage_provider',
        'mail_driver', 'mail_host', 'mail_port', 'mail_encryption',
        'mail_username', 'mail_password', 'mail_from_address', 'mail_from_name',
    ];

    protected $appends = ['favicon_url', 'logo_light_url', 'login_image_url'];

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], ['title' => 'Vedha']);
    }

    public function getFaviconUrlAttribute(): ?string
    {
        return $this->favicon_path ? Storage::url($this->favicon_path) : null;
    }

    public function getLogoLightUrlAttribute(): ?string
    {
        return $this->logo_light_path ? Storage::url($this->logo_light_path) : null;
    }

    public function getLoginImageUrlAttribute(): ?string
    {
        return $this->login_image_path ? Storage::url($this->login_image_path) : null;
    }
}
