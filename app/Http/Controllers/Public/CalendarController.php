<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Cliente;
use App\Models\ClienteFinal;
use App\Services\IcsBuilder;
use Illuminate\Http\Response;

class CalendarController extends Controller
{
    public function __construct(protected IcsBuilder $ics) {}

    public function cliente(string $token): Response
    {
        abort_if(strlen($token) < 32, 404);
        $cliente = Cliente::where('calendar_token', $token)->first();
        abort_unless($cliente, 404);

        $appointments = Appointment::where('cliente_id', $cliente->id)
            ->where('scheduled_at', '>=', now()->subMonths(3))
            ->orderBy('scheduled_at')
            ->with('resource')
            ->get();

        $name = ($cliente->razao_social ?: 'Agenda') . ' — NC5';
        $body = $this->ics->build($appointments, $name);

        return $this->icsResponse($body, 'agenda.ics');
    }

    public function clienteFinal(string $token): Response
    {
        abort_if(strlen($token) < 32, 404);
        $cf = ClienteFinal::where('calendar_token', $token)->first();
        abort_unless($cf, 404);

        $appointments = Appointment::where('cliente_id', $cf->cliente_id)
            ->where('cliente_final_id', $cf->id)
            ->where('scheduled_at', '>=', now()->subMonths(3))
            ->orderBy('scheduled_at')
            ->with('resource')
            ->get();

        $name = 'Seus agendamentos — ' . ($cf->nome_responsavel ?: $cf->nome_empresa ?: 'cliente');
        $body = $this->ics->build($appointments, $name);

        return $this->icsResponse($body, 'meus-agendamentos.ics');
    }

    protected function icsResponse(string $body, string $filename): Response
    {
        return response($body, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
