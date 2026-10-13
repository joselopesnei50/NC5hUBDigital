<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestador extends Model
{
    use HasFactory;

    protected $table = 'prestadores';

    protected $fillable = [
        'nome',
        'tipo_pessoa',
        'cpf_cnpj',
        'rg_ie',
        'email',
        'telefone',
        'endereco',
        'cidade',
        'uf',
        'cep',
        'categoria',
        'banco',
        'agencia',
        'conta',
        'chave_pix',
        'notas',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function contratos()
    {
        return $this->hasMany(ContratoPrestador::class);
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }
}
