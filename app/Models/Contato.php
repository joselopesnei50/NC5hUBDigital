<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contato extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'email',
        'whatsapp',
        'assunto',
        'mensagem',
        'status',
        'lead_id',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
