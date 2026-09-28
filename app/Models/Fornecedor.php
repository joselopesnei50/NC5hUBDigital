<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fornecedor extends Model
{
    use HasFactory;

    protected $table = 'fornecedores';

    protected $fillable = [
        'cliente_id',
        'nome',
        'cnpj_cpf',
        'email',
        'telefone',
        'endereco',
        'categoria',
        'notas',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
