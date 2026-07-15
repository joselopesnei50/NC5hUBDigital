<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Briefing extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'titulo',
        'descricao',
        'anexo_admin_path',
        'resposta_cliente',
        'anexo_cliente_path',
        'status', // pendente, respondido
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
