<?php

declare(strict_types=1);

namespace App\Agent\Chat\Tools;

use App\Agent\Chat\Contracts\ToolInterface;
use App\Models\AppointmentResource;

class ListarRecursosWhatsappTool implements ToolInterface
{
    public function getName(): string
    {
        return 'listar_recursos';
    }

    public function getDescription(): string
    {
        return 'Retorna a lista de recursos (profissionais, cadeiras, salas, mecanicos, etc) ativos deste negocio. '
            . 'USE quando o cliente final pedir pra agendar e o negocio tem mais de um recurso, pra apresentar as opcoes. '
            . 'Se so tem 1 recurso ativo, nao precisa chamar — o criar_agendamento ja auto-preenche. '
            . 'Se nao tem nenhum, agendamento funciona em modo unico.';
    }

    public function getParametersSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => new \stdClass(),
        ];
    }

    public function execute(int $tenantId, array $arguments): array
    {
        $resources = AppointmentResource::where('cliente_id', $tenantId)
            ->active()
            ->ordered()
            ->get(['id', 'nome', 'descricao']);

        return [
            'total' => $resources->count(),
            'recursos' => $resources->map(fn ($r) => [
                'id' => $r->id,
                'nome' => $r->nome,
                'descricao' => $r->descricao,
            ])->values()->all(),
        ];
    }
}
