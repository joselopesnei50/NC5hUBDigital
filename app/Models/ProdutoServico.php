<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoServico extends Model
{
    use HasFactory;

    protected $table = 'produtos_servicos';

    protected $fillable = [
        'cliente_id',
        'tipo',
        'nome',
        'descricao',
        'preco_padrao',
    ];

    protected $casts = [
        'preco_padrao' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
