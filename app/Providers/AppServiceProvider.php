<?php

namespace App\Providers;

use App\Services\KiloClaw\HttpKiloClawClient;
use App\Services\KiloClaw\KiloClawHttpClient;
use App\Services\OnChainOS\OnChainOSClient;
use App\Services\OnChainOS\WebhookSignatureVerifier;
use App\Services\OnChainOS\XLayer\HttpXLayerTransport;
use App\Services\OnChainOS\XLayer\XLayerHttpTransport;
use App\Services\OnChainOS\XLayer\XLayerOnChainOSClient;
use App\Services\Telegram\HttpTelegramClient;
use App\Services\Telegram\TelegramHttpClient;
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

        $this->app->singleton(XLayerHttpTransport::class, fn () => new HttpXLayerTransport(
            (string) config('services.onchainos.base_url', ''),
        ));

        $this->app->singleton(OnChainOSClient::class, fn ($app) => new XLayerOnChainOSClient(
            $app->make(XLayerHttpTransport::class),
            (string) config('services.onchainos.api_key', ''),
            (string) config('services.onchainos.secret_key', ''),
            (string) config('services.onchainos.passphrase', ''),
        ));

        $this->app->singleton(KiloClawHttpClient::class, fn () => new HttpKiloClawClient(
            (string) config('services.kiloclaw.base_url', ''),
            (string) config('services.kiloclaw.api_key', ''),
        ));

        $this->app->singleton(TelegramHttpClient::class, fn () => new HttpTelegramClient());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
