<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentResource extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'use_custom_hours' => 'boolean',
        'horario_comercial' => 'array',
    ];

    public function getHorarioDoDia(string $dia): ?array
    {
        if (!$this->use_custom_hours) {
            return null;
        }
        $h = $this->horario_comercial[$dia] ?? null;
        if (!is_array($h) || empty($h['aberto'])) {
            return null;
        }
        return $h;
    }

    public function hasCustomHours(): bool
    {
        return (bool) $this->use_custom_hours && !empty($this->horario_comercial);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'resource_id');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('ordem')->orderBy('nome');
    }
}
