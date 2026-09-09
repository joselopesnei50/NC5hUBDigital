<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Agent\Contracts\LlmDriver;
use App\Agent\LlmManager;

class AgentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Registra o Manager no container como Singleton
        $this->app->singleton(LlmManager::class, function ($app) {
            return new LlmManager($app);
        });

        // Binda a Interface ao Driver atual configurado.
        // Assim, qualquer classe pode pedir um LlmDriver pelo construtor e receberá o driver ativo magicamente.
        $this->app->bind(LlmDriver::class, function ($app) {
            return $app->make(LlmManager::class)->driver();
        });
    }

    public function boot(): void
    {
        //
    }
}
