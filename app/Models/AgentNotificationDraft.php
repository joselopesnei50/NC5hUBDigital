<?php

declare(strict_types=1);

namespace App\Models;

use App\Agent\Enums\AgentNotificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentNotificationDraft extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'status' => AgentNotificationStatus::class,
        'sent_at' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function clienteFinal()
    {
        return $this->belongsTo(ClienteFinal::class);
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }
}
