<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappBotTraining extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_bot_training';

    protected $guarded = [];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
