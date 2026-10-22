<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class WhatsappInstance extends Model
{
    use HasFactory;

    public const PROVIDER_EVOLUTION = 'evolution';
    public const PROVIDER_CLOUD_API = 'cloud_api';

    protected $guarded = [];

    protected $hidden = [
        'instance_token',
        'graph_access_token',
    ];

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

    public function isCloudApi(): bool
    {
        return $this->provider === self::PROVIDER_CLOUD_API;
    }

    public function isEvolution(): bool
    {
        return $this->provider === self::PROVIDER_EVOLUTION || empty($this->provider);
    }

    public function getGraphAccessTokenAttribute(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }
        try {
            return Crypt::decryptString($value);
        } catch (DecryptException) {
            return $value;
        }
    }

    public function setGraphAccessTokenAttribute(?string $value): void
    {
        $this->attributes['graph_access_token'] = ($value === null || $value === '')
            ? $value
            : Crypt::encryptString($value);
    }
}
