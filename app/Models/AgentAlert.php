<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentAlert extends Model
{
    use HasFactory;

    protected $table = 'agent_alerts';

    protected $guarded = [];

    protected $casts = [
        'detalhes' => 'array',
        'lido_em' => 'datetime',
        'dispensado_em' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /** Alertas ainda pendentes (não dispensados). */
    public function scopeAtivos(Builder $q): Builder
    {
        return $q->whereNull('dispensado_em');
    }

    public function scopeNaoLidos(Builder $q): Builder
    {
        return $q->whereNull('lido_em');
    }

    public function scopeDoCliente(Builder $q, int $clienteId): Builder
    {
        return $q->where('cliente_id', $clienteId);
    }
}
