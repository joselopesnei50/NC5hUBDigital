<?php

declare(strict_types=1);

namespace App\Agent\Metrics;

use App\Agent\Contracts\MetricInterface;
use App\Models\WhatsappBotProfile;
use App\Models\WhatsappChat;
use App\Models\WhatsappInstance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WhatsappAtendimentoMetric implements MetricInterface
{
    public function getName(): string
    {
        return 'whatsapp_atendimento';
    }

    public function calculate(int $tenantId, int $periodDays): array
    {
        $startDate = Carbon::now()->subDays(max(1, $periodDays))->startOfDay();

        $instance = WhatsappInstance::where('cliente_id', $tenantId)->first();
        $profile = WhatsappBotProfile::where('cliente_id', $tenantId)->first();

        $instanciaConfigurada = $instance !== null;
        $instanciaConectada = $instance !== null && $instance->status === 'open';
        $botAtivo = $profile !== null && (bool) $profile->is_active;

        $conversasAtivas = WhatsappChat::where('cliente_id', $tenantId)
            ->where('is_group', false)
            ->where('last_message_at', '>=', $startDate)
            ->count();

        $conversasNaoLidas = WhatsappChat::where('cliente_id', $tenantId)
            ->where('is_group', false)
            ->where('unread_count', '>', 0)
            ->count();

        $aguardandoHumano = WhatsappChat::where('cliente_id', $tenantId)
            ->where('is_group', false)
            ->where('is_bot_active', false)
            ->where('unread_count', '>', 0)
            ->count();

        $botStats = DB::table('whatsapp_bot_interactions')
            ->where('cliente_id', $tenantId)
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('COUNT(*) as total_interacoes'),
                DB::raw('SUM(CASE WHEN escalated = 1 THEN 1 ELSE 0 END) as escalacoes'),
                DB::raw('SUM(CASE WHEN failed = 1 THEN 1 ELSE 0 END) as falhas'),
                DB::raw('AVG(NULLIF(response_time_ms, 0)) as tempo_medio_ms')
            )
            ->first();

        return [
            'instancia_configurada' => $instanciaConfigurada,
            'instancia_conectada' => $instanciaConectada,
            'status_instancia' => $instance?->status,
            'bot_ativo' => $botAtivo,
            'conversas_ativas_periodo' => $conversasAtivas,
            'conversas_nao_lidas' => $conversasNaoLidas,
            'conversas_aguardando_humano' => $aguardandoHumano,
            'interacoes_bot_periodo' => (int) ($botStats->total_interacoes ?? 0),
            'escalacoes_periodo' => (int) ($botStats->escalacoes ?? 0),
            'falhas_bot_periodo' => (int) ($botStats->falhas ?? 0),
            'tempo_medio_resposta_ms' => (int) round((float) ($botStats->tempo_medio_ms ?? 0)),
        ];
    }
}
