<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fatura extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'vencimento' => 'date',
        'dados_bancarios' => 'array',
        'comprovante_enviado_em' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Retorna o valor formatado em BRL (R$ 1.500,00)
     */
    public function getValorFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor, 2, ',', '.');
    }

    /**
     * Verifica se a fatura tem nota fiscal anexada
     */
    public function getTemNotaFiscalAttribute()
    {
        return !empty($this->nota_fiscal_path);
    }

    /**
     * Verifica se o cliente já enviou comprovante
     */
    public function getTemComprovanteAttribute()
    {
        return !empty($this->comprovante_path);
    }
}
