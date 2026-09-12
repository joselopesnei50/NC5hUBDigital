<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Agent\Contracts\LlmDriver;
use App\Agent\LlmManager;
use App\Agent\SnapshotBuilder;
use App\Agent\Metrics\CashFlowMetric;
use App\Agent\Metrics\CustomerSegmentMetric;
use App\Agent\Metrics\InactiveCustomersMetric;
use App\Agent\Metrics\PendingInvoicesMetric;
use App\Agent\Metrics\ProductsPerformanceMetric;
use App\Agent\Metrics\ProjectDeliveryMetric;
use App\Agent\Metrics\TopCustomersMetric;
use App\Agent\Chat\ToolRegistry;
use App\Agent\Chat\Tools\DraftCustomerMessageTool;
use App\Agent\Chat\Tools\GetBusinessSnapshotTool;
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

        // SnapshotBuilder recebe todas as metricas do negocio. Adicionar aqui
        // qualquer metrica nova para que apareca automaticamente no snapshot.
        $this->app->singleton(SnapshotBuilder::class, function ($app) {
            return new SnapshotBuilder([
                $app->make(CashFlowMetric::class),
                $app->make(PendingInvoicesMetric::class),
                $app->make(CustomerSegmentMetric::class),
                $app->make(TopCustomersMetric::class),
                $app->make(InactiveCustomersMetric::class),
                $app->make(ProductsPerformanceMetric::class),
                $app->make(ProjectDeliveryMetric::class),
            ]);
        });

        // Registry precisa ser singleton para que o mesmo conjunto de tools
        // seja usado em toda requisição — e para o Bruce conseguir listá-las.
        $this->app->singleton(ToolRegistry::class, function ($app) {
            $registry = new ToolRegistry();
            $registry->register($app->make(GetBusinessSnapshotTool::class));
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
