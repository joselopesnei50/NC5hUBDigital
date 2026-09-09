<?php

declare(strict_types=1);

namespace App\Agent\Chat;

use App\Agent\Chat\Contracts\ToolInterface;

class ToolRegistry
{
    /** @var array<string, ToolInterface> */
    private array $tools = [];

    /**
     * Registra uma ferramenta disponível para o IA.
     */
    public function register(ToolInterface $tool): void
    {
        $this->tools[$tool->getName()] = $tool;
    }

    /**
     * Busca a ferramenta solicitada pelo LLM.
     */
    public function getTool(string $name): ?ToolInterface
    {
        return $this->tools[$name] ?? null;
    }

    /**
     * Formata e compila todas as ferramentas no padrão "function calling" (OpenAI/DeepSeek spec)
     * para injetarmos no Payload antes do LLM pensar.
     */
    public function getToolsForLlm(): array
    {
        $schema = [];
        foreach ($this->tools as $tool) {
            $schema[] = [
                'type' => 'function',
                'function' => [
                    'name' => $tool->getName(),
                    'description' => $tool->getDescription(),
                    'parameters' => $tool->getParametersSchema()
                ]
            ];
        }
        return $schema;
    }
}
