<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappInstance extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'connected_at' => 'datetime',
        'last_status_check_at' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function isConnected(): bool
    {
        return $this->status === 'open';
    }
}
