<?php

namespace App\Providers;

use Http;
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
        Http::macro('personio', function () {
            return Http::baseUrl(config('personio.base_url'))
                ->withHeaders([
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                ]);
        });
    }
}
