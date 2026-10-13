<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContratoPrestador extends Model
{
    use HasFactory;

    protected $table = 'contratos_prestadores';

    protected $fillable = [
        'prestador_id',
        'titulo',
        'objeto',
        'valor',
        'forma_pagamento',
        'periodicidade',
        'data_inicio',
        'data_fim',
        'prazo_entrega',
        'clausula_confidencialidade',
        'clausula_exclusividade',
        'clausula_propriedade_intelectual',
        'clausula_nao_vinculo_empregaticio',
        'clausulas_extras',
        'conteudo',
        'status',
        'status_assinatura',
        'assinatura_url',
        'assinado_em',
        'pdf_path',
        'signature_token',
        'enviado_em',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'assinado_em' => 'datetime',
        'enviado_em' => 'datetime',
        'valor' => 'decimal:2',
        'clausula_confidencialidade' => 'boolean',
        'clausula_exclusividade' => 'boolean',
        'clausula_propriedade_intelectual' => 'boolean',
        'clausula_nao_vinculo_empregaticio' => 'boolean',
    ];

    public function ensureSignatureToken(): string
    {
        if (!$this->signature_token) {
            $this->signature_token = \Illuminate\Support\Str::random(48);
            $this->save();
        }
        return $this->signature_token;
    }

    public function publicSignUrl(): string
    {
        return route('public.contrato-prestador.show', $this->ensureSignatureToken());
    }

    public function prestador()
    {
        return $this->belongsTo(Prestador::class);
    }

    public function getFormaPagamentoLabelAttribute(): string
    {
        return [
            'mensal' => 'Mensal',
            'por_entrega' => 'Por entrega',
            'unico' => 'Pagamento único',
            'hora' => 'Por hora',
            'outro' => 'Outro',
        ][$this->forma_pagamento] ?? $this->forma_pagamento;
    }
}
