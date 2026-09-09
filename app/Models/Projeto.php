<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projeto extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'data_inicio' => 'date',
        'data_previsao' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function pedidoCliente()
    {
        return $this->belongsTo(PedidoCliente::class);
    }

    public function tarefas()
    {
        return $this->hasMany(ProjetoTarefa::class);
    }

    public function clienteFinal()
    {
        return $this->belongsTo(ClienteFinal::class, 'cliente_final_id');
    }
}
