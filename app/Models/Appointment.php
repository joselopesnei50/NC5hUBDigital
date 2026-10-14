<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'duration_minutes' => 'integer',
    ];

    public const STATUSES = ['agendado', 'confirmado', 'concluido', 'cancelado'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function clienteFinal()
    {
        return $this->belongsTo(ClienteFinal::class);
    }

    public function chat()
    {
        return $this->belongsTo(WhatsappChat::class, 'chat_id');
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelado';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'concluido';
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['agendado', 'confirmado']);
    }

    public function endsAt(): \Carbon\Carbon
    {
        return $this->scheduled_at->copy()->addMinutes($this->duration_minutes);
    }
}
