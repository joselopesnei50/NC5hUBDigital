<?php

declare(strict_types=1);

namespace App\Agent;

use Illuminate\Support\Manager;
use App\Agent\Drivers\DeepSeekDriver;
use App\Agent\Drivers\NullDriver;

class LlmManager extends Manager
{
    /**
     * Define qual driver deve ser carregado por padrão.
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('agent.default_driver', 'null');
    }

    /**
     * Instancia e configura o driver DeepSeek.
     */
    public function createDeepseekDriver(): DeepSeekDriver
    {
        $config = $this->config->get('agent.drivers.deepseek');
        $timeout = $this->config->get('agent.timeout', 120);

        return new DeepSeekDriver(
            apiKey: $config['api_key'] ?? '',
            baseUrl: $config['base_url'] ?? 'https://api.deepseek.com',
            model: $config['model'] ?? 'deepseek-chat',
            costPer1kIn: (float) ($config['cost_per_1k_in'] ?? 0.001),
            costPer1kOut: (float) ($config['cost_per_1k_out'] ?? 0.002),
            timeout: (int) $timeout
        );
    }

    /**
     * Instancia o driver falso para ambiente de testes.
     */
    public function createNullDriver(): NullDriver
    {
        return new NullDriver();
    }
}
