<?php

declare(strict_types=1);

namespace App\Agent\Contracts;

use App\Agent\DTOs\PromptPayload;
use App\Agent\DTOs\LlmResponse;

interface LlmDriver
{
    /**
     * Executa a requisição ao modelo LLM isolando o provedor concreto.
     */
    public function complete(PromptPayload $payload): LlmResponse;
}
