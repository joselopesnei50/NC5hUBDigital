<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoCliente extends Model
{
    use HasFactory;

    protected $table = 'pedidos_clientes';

    protected $fillable = [
        'cliente_id',
        'cliente_final_id',
        'titulo',
        'descricao',
        'valor',
        'status',
        'data_pedido',
        'data_entrega',
    ];

    protected $casts = [
        'data_pedido' => 'date',
        'data_entrega' => 'date',
        'valor' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function clienteFinal()
    {
        return $this->belongsTo(ClienteFinal::class, 'cliente_final_id');
    }
}
