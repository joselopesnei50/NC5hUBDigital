<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappBotInteraction extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'cost' => 'decimal:6',
        'escalated' => 'boolean',
        'failed' => 'boolean',
    ];

    public function chat()
    {
        return $this->belongsTo(WhatsappChat::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
