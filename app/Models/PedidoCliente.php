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
        'produto_servico_id',
        'titulo',
        'descricao',
        'valor',
        'status',
        'data_pedido',
        'data_entrega',
        'token_publico',
        'ip_aprovacao',
        'nome_aprovacao',
        'data_aprovacao',
    ];

    protected $casts = [
        'data_pedido' => 'date',
        'data_entrega' => 'date',
        'data_aprovacao' => 'datetime',
        'valor' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->token_publico)) {
                $model->token_publico = \Illuminate\Support\Str::uuid()->toString();
            }
        });
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function clienteFinal()
    {
        return $this->belongsTo(ClienteFinal::class, 'cliente_final_id');
    }

    public function produtoServico()
    {
        return $this->belongsTo(ProdutoServico::class, 'produto_servico_id');
    }
}
