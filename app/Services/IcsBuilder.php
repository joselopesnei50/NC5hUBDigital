<?php

namespace App\Services;

use App\Models\Appointment;
use Illuminate\Support\Collection;

class IcsBuilder
{
    public const PROD_ID = '-//NC5HubDigital//Agenda//PT';

    /**
     * Gera o conteudo ICS (RFC 5545) a partir de uma colecao de Appointments.
     */
    public function build(Collection $appointments, string $calendarName): string
    {
        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:' . self::PROD_ID,
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'X-WR-CALNAME:' . $this->escape($calendarName),
            'X-WR-TIMEZONE:America/Sao_Paulo',
        ];

        $now = now()->utc()->format('Ymd\THis\Z');

        foreach ($appointments as $apt) {
            $start = $apt->scheduled_at->copy()->utc()->format('Ymd\THis\Z');
            $end = $apt->scheduled_at->copy()->addMinutes($apt->duration_minutes)->utc()->format('Ymd\THis\Z');

            $description = $apt->descricao ?: '';
            if ($apt->contato_nome) $description .= ($description ? "\n\n" : '') . "Contato: {$apt->contato_nome}";
            if ($apt->contato_telefone) $description .= ($description ? "\n" : '') . "Telefone: {$apt->contato_telefone}";

            $location = $apt->resource?->nome ?: '';

            $status = match ($apt->status) {
                'cancelado' => 'CANCELLED',
                'concluido' => 'CONFIRMED',
                'confirmado' => 'CONFIRMED',
                default => 'TENTATIVE',
            };

            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'UID:appointment-' . $apt->id . '@nc5hubdigital';
            $lines[] = 'DTSTAMP:' . $now;
            $lines[] = 'DTSTART:' . $start;
            $lines[] = 'DTEND:' . $end;
            $lines[] = 'SUMMARY:' . $this->escape($apt->titulo);
            if ($description) {
                $lines[] = 'DESCRIPTION:' . $this->escape($description);
            }
            if ($location) {
                $lines[] = 'LOCATION:' . $this->escape($location);
            }
            $lines[] = 'STATUS:' . $status;
            $lines[] = 'END:VEVENT';
        }

        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", $lines) . "\r\n";
    }

    /**
     * Escapa caracteres especiais do ICS (RFC 5545 secao 3.3.11).
     */
    protected function escape(string $value): string
    {
        $value = str_replace(['\\', ',', ';', "\n"], ['\\\\', '\\,', '\\;', '\\n'], $value);
        return $value;
    }
}
