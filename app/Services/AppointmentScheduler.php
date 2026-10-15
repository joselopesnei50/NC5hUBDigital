<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\AppointmentResource;
use App\Models\WhatsappBotTraining;
use Carbon\Carbon;

class AppointmentScheduler
{
    public const DIAS_SEMANA_MAP = [
        1 => 'seg', 2 => 'ter', 3 => 'qua', 4 => 'qui',
        5 => 'sex', 6 => 'sab', 0 => 'dom',
    ];

    /**
     * Retorna true se o intervalo [when, when + duration) cai inteiro dentro
     * do horario comercial do dia. Se o recurso tem horario proprio
     * (use_custom_hours=true), ele tem prioridade. Senao, usa o do training.
     * Se nenhum ha config, aceita (fail-open).
     */
    public function isWithinBusinessHours(Carbon $when, int $durationMinutes, ?WhatsappBotTraining $training, ?AppointmentResource $resource = null): bool
    {
        $diaChave = self::DIAS_SEMANA_MAP[$when->dayOfWeek] ?? null;
        if (!$diaChave) {
            return true;
        }

        if ($resource && $resource->hasCustomHours()) {
            $dia = $resource->getHorarioDoDia($diaChave);
            if (!$dia) {
                return false;
            }
            return $this->intervaloCabe($when, $durationMinutes, $dia);
        }

        if (!$training || empty($training->horario_comercial)) {
            return true;
        }

        $dia = $training->getHorarioDoDia($diaChave);
        if (!$dia) {
            return false;
        }

        return $this->intervaloCabe($when, $durationMinutes, $dia);
    }

    protected function intervaloCabe(Carbon $when, int $durationMinutes, array $dia): bool
    {
        $abre = Carbon::createFromFormat('H:i', $dia['abre'])->setDateFrom($when);
        $fecha = Carbon::createFromFormat('H:i', $dia['fecha'])->setDateFrom($when);
        $end = $when->copy()->addMinutes($durationMinutes);

        return $when->greaterThanOrEqualTo($abre) && $end->lessThanOrEqualTo($fecha);
    }

    /**
     * Verifica se existe appointment ativo que colida com o intervalo proposto.
     * Retorna o Appointment conflitante ou null.
     */
    public function findConflict(int $clienteId, Carbon $when, int $durationMinutes, ?int $ignoreId = null, ?int $resourceId = null): ?Appointment
    {
        $end = $when->copy()->addMinutes($durationMinutes);

        return Appointment::where('cliente_id', $clienteId)
            ->whereIn('status', ['agendado', 'confirmado'])
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->when($resourceId !== null, fn ($q) => $q->where('resource_id', $resourceId))
            ->where(function ($q) use ($when, $end) {
                $q->where(function ($inner) use ($when, $end) {
                    $inner->where('scheduled_at', '<', $end)
                        ->whereRaw('DATE_ADD(scheduled_at, INTERVAL duration_minutes MINUTE) > ?', [$when]);
                });
            })
            ->orderBy('scheduled_at')
            ->first();
    }

    /**
     * Sugere ate N proximos horarios livres a partir de $from, dentro de
     * horario comercial, em multiplos da duracao. Varre ate 14 dias.
     */
    public function suggestNextSlots(int $clienteId, ?WhatsappBotTraining $training, Carbon $from, int $durationMinutes, int $count = 3, ?int $resourceId = null, ?AppointmentResource $resource = null): array
    {
        $slots = [];
        $cursor = $from->copy()->setSecond(0);
        if ($cursor->minute % 15 !== 0) {
            $cursor->addMinutes(15 - ($cursor->minute % 15));
        }
        $deadline = $from->copy()->addDays(14);

        while (count($slots) < $count && $cursor->lessThan($deadline)) {
            if (!$this->isWithinBusinessHours($cursor, $durationMinutes, $training, $resource)) {
                $cursor = $this->jumpToNextOpenMoment($cursor, $training, $resource);
                continue;
            }

            $conflict = $this->findConflict($clienteId, $cursor, $durationMinutes, null, $resourceId);
            if ($conflict) {
                $cursor = $conflict->scheduled_at->copy()->addMinutes($conflict->duration_minutes);
                continue;
            }

            $slots[] = $cursor->copy();
            $cursor->addMinutes($durationMinutes);
        }

        return $slots;
    }

    /**
     * Retorna o primeiro resource_id disponivel no horario, OU null se nenhum.
     * Usado quando o cliente final diz "qualquer um" e tem multiplos recursos.
     * @param int[] $resourceIds
     */
    public function findFirstAvailableResource(int $clienteId, array $resourceIds, Carbon $when, int $durationMinutes): ?int
    {
        foreach ($resourceIds as $rid) {
            if (!$this->findConflict($clienteId, $when, $durationMinutes, null, $rid)) {
                return $rid;
            }
        }
        return null;
    }

    protected function jumpToNextOpenMoment(Carbon $cursor, ?WhatsappBotTraining $training, ?AppointmentResource $resource = null): Carbon
    {
        $usingResource = $resource && $resource->hasCustomHours();
        $hasAnyConfig = $usingResource || ($training && !empty($training->horario_comercial));

        if (!$hasAnyConfig) {
            return $cursor->addDay()->setTime(9, 0);
        }

        for ($i = 0; $i < 7; $i++) {
            $diaChave = self::DIAS_SEMANA_MAP[$cursor->dayOfWeek] ?? null;
            if ($diaChave) {
                $dia = $usingResource
                    ? $resource->getHorarioDoDia($diaChave)
                    : ($training ? $training->getHorarioDoDia($diaChave) : null);
                if ($dia) {
                    $abre = Carbon::createFromFormat('H:i', $dia['abre'])->setDateFrom($cursor);
                    if ($cursor->lessThan($abre)) {
                        return $abre;
                    }
                }
            }
            $cursor = $cursor->copy()->addDay()->setTime(0, 1);
        }

        return $cursor->addDay()->setTime(9, 0);
    }
}
