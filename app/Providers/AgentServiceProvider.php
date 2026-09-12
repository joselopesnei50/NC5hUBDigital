<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Agent\Contracts\LlmDriver;
use App\Agent\LlmManager;
use App\Agent\Chat\ToolRegistry;
use App\Agent\Chat\Tools\DraftCustomerMessageTool;
use App\Agent\Chat\Tools\GetCashFlowTool;

class AgentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LlmManager::class, function ($app) {
            return new LlmManager($app);
        });

        $this->app->bind(LlmDriver::class, function ($app) {
            return $app->make(LlmManager::class)->driver();
        });

        // Registry precisa ser singleton para que o mesmo conjunto de tools
        // seja usado em toda requisição — e para o Bruce conseguir listá-las.
        $this->app->singleton(ToolRegistry::class, function ($app) {
            $registry = new ToolRegistry();
            $registry->register($app->make(GetCashFlowTool::class));
            $registry->register($app->make(DraftCustomerMessageTool::class));
            return $registry;
        });
    }

    public function boot(): void
    {
        //
    }
}
