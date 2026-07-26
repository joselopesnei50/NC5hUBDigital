<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campanha extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'assunto',
        'audience',
        'brevo_campaign_id',
        'status',
        'sent_at',
        'metrics',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'metrics' => 'array',
    ];
}
