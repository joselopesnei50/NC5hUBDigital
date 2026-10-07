<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappChat extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_group' => 'boolean',
        'last_message_at' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function clienteFinal()
    {
        return $this->belongsTo(ClienteFinal::class);
    }

    public function messages()
    {
        return $this->hasMany(WhatsappMessage::class, 'chat_id');
    }

    public function latestMessage()
    {
        return $this->hasOne(WhatsappMessage::class, 'chat_id')->latestOfMany();
    }
}
