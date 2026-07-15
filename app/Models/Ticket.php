<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function mensagens()
    {
        return $this->hasMany(TicketMensagem::class);
    }
}
