<?php

namespace App\Http\Controllers;

use App\Models\AgentAlert;
use App\Services\SystemCliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Central de Notificações (sininho) — compartilhada entre o Hub NC5 (admin) e o BruceOS (cliente).
 */
class PanelNotificationController extends Controller
{
    protected function resolveClienteId(Request $request): int
    {
        if ($request->routeIs('admin.*')) {
            return (int) SystemCliente::get()->id;
        }

        $cliente = Auth::user()->cliente ?? null;
        abort_unless($cliente, 403);

        return (int) $cliente->id;
    }

    public static function buildFeed(int $clienteId, int $limit = 15): array
    {
        $items = AgentAlert::query()
            ->doCliente($clienteId)
            ->ativos()
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (AgentAlert $a) => [
                'id'         => $a->id,
                'tipo'       => $a->tipo,
                'severidade' => $a->severidade,
                'titulo'     => $a->titulo,
                'mensagem'   => $a->mensagem,
                'lido'       => (bool) $a->lido_em,
                'quando'     => $a->created_at?->diffForHumans(),
                'is_agenda'  => str_starts_with((string) $a->tipo, 'agenda_'),
            ])
            ->values()
            ->all();

        $unread = AgentAlert::query()->doCliente($clienteId)->ativos()->naoLidos()->count();

        return ['unread' => $unread, 'items' => $items];
    }

    public function feed(Request $request)
    {
        return response()->json(self::buildFeed($this->resolveClienteId($request)));
    }

    public function markAllRead(Request $request)
    {
        $clienteId = $this->resolveClienteId($request);

        AgentAlert::query()
            ->doCliente($clienteId)
            ->naoLidos()
            ->update(['lido_em' => now()]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back();
    }

    public function dismiss(Request $request, AgentAlert $alerta)
    {
        $clienteId = $this->resolveClienteId($request);
        abort_unless((int) $alerta->cliente_id === $clienteId, 403);

        $alerta->update([
            'lido_em'       => $alerta->lido_em ?: now(),
            'dispensado_em' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back();
    }
}
