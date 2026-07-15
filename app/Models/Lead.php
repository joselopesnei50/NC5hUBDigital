<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'email',
        'whatsapp',
        'tipo_analise',
        'url_analise',
        'resultado_ia',
    ];
}
