<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClienteFinal extends Model
{
    use HasFactory;

    protected $table = 'clientes_finais';

    protected $fillable = [
        'cliente_id',
        'nome_empresa',
        'nome_responsavel',
        'telefone',
        'email',
        'cidade',
        'ultimos_pedidos',
        'data_aniversario_empresa',
        'data_aniversario_responsavel',
    ];

    protected $casts = [
        'data_aniversario_empresa' => 'date',
        'data_aniversario_responsavel' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
