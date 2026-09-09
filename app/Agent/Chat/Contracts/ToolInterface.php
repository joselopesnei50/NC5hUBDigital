<?php

declare(strict_types=1);

namespace App\Agent\Chat\Contracts;

interface ToolInterface
{
    /**
     * O nome técnico exato que o LLM usará para chamar a ferramenta.
     */
    public function getName(): string;

    /**
     * A descrição clara de QUANDO e PARA QUE o LLM deve usar esta ferramenta.
     */
    public function getDescription(): string;

    /**
     * O Schema JSON (no formato OpenAPI/JSON Schema) dos parâmetros que a função aceita.
     */
    public function getParametersSchema(): array;

    /**
     * O núcleo de execução blindado por Tenant.
     */
    public function execute(int $tenantId, array $arguments): array;
}
