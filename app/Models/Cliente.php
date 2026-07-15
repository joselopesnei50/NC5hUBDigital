<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contratos()
    {
        return $this->hasMany(Contrato::class);
    }

    public function faturas()
    {
        return $this->hasMany(Fatura::class);
    }

    public function materiais()
    {
        return $this->hasMany(Material::class);
    }

    public function relatorios()
    {
        return $this->hasMany(Relatorio::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function briefings()
    {
        return $this->hasMany(Briefing::class);
    }

    public function pedidos()
    {
        return $this->hasMany(PedidoServico::class);
    }
}
