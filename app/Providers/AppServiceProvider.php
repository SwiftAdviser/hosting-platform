<?php

namespace App\Providers;

use App\Services\KiloClaw\DemoKiloClawHttpClient;
use App\Services\KiloClaw\HttpKiloClawClient;
use App\Services\KiloClaw\KiloClawHttpClient;
use App\Services\OnChainOS\DemoOnChainOSClient;
use App\Services\OnChainOS\OnChainOSClient;
use App\Services\OnChainOS\WebhookSignatureVerifier;
use App\Services\OnChainOS\XLayer\HttpXLayerTransport;
use App\Services\OnChainOS\XLayer\XLayerHttpTransport;
use App\Services\OnChainOS\XLayer\XLayerOnChainOSClient;
use App\Services\Telegram\DemoTelegramHttpClient;
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

        $this->app->bind(OnChainOSClient::class, function ($app) {
            if ((bool) config('services.demo.enabled', false)) {
                return new DemoOnChainOSClient();
            }

            return new XLayerOnChainOSClient(
                $app->make(XLayerHttpTransport::class),
                (string) config('services.onchainos.api_key', ''),
                (string) config('services.onchainos.secret_key', ''),
                (string) config('services.onchainos.passphrase', ''),
            );
        });

        $this->app->bind(KiloClawHttpClient::class, function () {
            if ((bool) config('services.demo.enabled', false)) {
                return new DemoKiloClawHttpClient();
            }

            return new HttpKiloClawClient(
                (string) config('services.kiloclaw.base_url', ''),
                (string) config('services.kiloclaw.api_key', ''),
            );
        });

        $this->app->bind(TelegramHttpClient::class, function () {
            if ((bool) config('services.demo.enabled', false)) {
                return new DemoTelegramHttpClient();
            }

            return new HttpTelegramClient();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
