<?php

namespace App\Providers;

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
        URL::forceRootUrl(Config::get('https://catatku-eight.vercel.app/'));
    if (str_contains(Config::get('https://catatku-eight.vercel.app/'), 'https://')) {
        URL::forceScheme('https');
    }
    }

    
}
