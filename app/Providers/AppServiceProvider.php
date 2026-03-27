<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            // Railway terminates TLS at its reverse proxy and forwards requests
            // to the container over plain HTTP. Trusting the X-Forwarded-Proto
            // header lets Laravel detect the original HTTPS scheme correctly
            // without forcing it unconditionally, which breaks URL generation
            // when the internal transport is HTTP.
            Request::setTrustedProxies(
                ['*'],
                Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_PROTO |
                Request::HEADER_X_FORWARDED_PREFIX
            );
            
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
