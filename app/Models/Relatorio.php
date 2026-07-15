<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Relatorio extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'data_referencia' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
