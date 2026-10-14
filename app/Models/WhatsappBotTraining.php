<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappBotTraining extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_bot_training';

    protected $guarded = [];

    protected $casts = [
        'horario_comercial' => 'array',
    ];

    public const DIAS_SEMANA = ['seg', 'ter', 'qua', 'qui', 'sex', 'sab', 'dom'];
    public const DIAS_SEMANA_LABEL = [
        'seg' => 'Segunda', 'ter' => 'Terça', 'qua' => 'Quarta',
        'qui' => 'Quinta', 'sex' => 'Sexta', 'sab' => 'Sábado', 'dom' => 'Domingo',
    ];

    public function getHorarioDoDia(string $dia): ?array
    {
        $h = $this->horario_comercial[$dia] ?? null;
        if (!is_array($h) || empty($h['aberto'])) {
            return null;
        }
        return $h;
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
