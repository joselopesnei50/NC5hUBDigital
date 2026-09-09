<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoClienteItem extends Model
{
    use HasFactory;

    protected $table = 'pedido_cliente_itens';

    protected $fillable = [
        'pedido_cliente_id',
        'produto_servico_id',
        'nome_item',
        'quantidade',
        'valor_unitario',
        'valor_total',
    ];

    protected $casts = [
        'quantidade' => 'decimal:2',
        'valor_unitario' => 'decimal:2',
        'valor_total' => 'decimal:2',
    ];

    public function pedido()
    {
        return $this->belongsTo(PedidoCliente::class, 'pedido_cliente_id');
    }

    public function produtoServico()
    {
        return $this->belongsTo(ProdutoServico::class, 'produto_servico_id');
    }
}
