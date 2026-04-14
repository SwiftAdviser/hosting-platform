<?php

namespace App\Providers;

use App\Services\OnChainOS\WebhookSignatureVerifier;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(WebhookSignatureVerifier::class, fn () => new WebhookSignatureVerifier(
            (string) config('services.onchainos.webhook_secret', ''),
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
