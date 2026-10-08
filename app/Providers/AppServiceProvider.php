<?php

namespace App\Providers;

use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Railway terminates HTTPS at its edge and forwards plain HTTP
        // internally, so without this Laravel generates http:// links
        // (form actions, asset URLs) even though the site is served over
        // https:// — triggering the browser's "not secure" warning.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        $this->applyDynamicMailSettings();
    }

    // The admin panel's Settings > Email Settings page saves SMTP details
    // into the `general_settings` table (not the .env file), so Laravel's
    // default config/mail.php (which only reads env vars) never sees them.
    // This pulls those DB-stored values into the live mail config on every
    // request, so Mail::send() actually uses whatever the admin configured.
    private function applyDynamicMailSettings(): void
    {
        try {
            $settings = GeneralSetting::current();
        } catch (\Throwable $e) {
            // DB not migrated yet (e.g. during the very first deploy) — skip silently.
            return;
        }

        if (blank($settings->mail_host)) {
            return; // admin hasn't configured email yet, keep .env defaults
        }

        Config::set('mail.default', $settings->mail_driver ?: 'smtp');
        Config::set('mail.mailers.smtp.host', $settings->mail_host);
        Config::set('mail.mailers.smtp.port', $settings->mail_port);
        Config::set('mail.mailers.smtp.username', $settings->mail_username);
        Config::set('mail.mailers.smtp.password', $settings->mail_password);
        Config::set('mail.mailers.smtp.scheme', $settings->mail_encryption);
        Config::set('mail.from.address', $settings->mail_from_address ?: 'hello@example.com');
        Config::set('mail.from.name', $settings->mail_from_name ?: $settings->title);
    }
}
