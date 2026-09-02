<?php

namespace App\Providers;

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
    }
}
