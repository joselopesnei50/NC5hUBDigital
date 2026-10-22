<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class SocialAccount extends Model
{
    protected $fillable = [
        'cliente_id',
        'platform',
        'page_id',
        'page_name',
        'page_picture',
        'access_token',
        'token_expires_at',
        'instagram_business_id',
        'instagram_username',
        'is_active',
    ];

    protected $hidden = [
        'access_token',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'is_active'        => 'boolean',
    ];

    public function getAccessTokenAttribute(?string $value): ?string
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

    public function setAccessTokenAttribute(?string $value): void
    {
        $this->attributes['access_token'] = ($value === null || $value === '')
            ? $value
            : Crypt::encryptString($value);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function posts()
    {
        return $this->hasMany(ScheduledPost::class, 'social_account_id');
    }

    public function isTokenExpired(): bool
    {
        return $this->token_expires_at && $this->token_expires_at->isPast();
    }

    public function isTokenExpiringSoon(): bool
    {
        return $this->token_expires_at
            && $this->token_expires_at->diffInDays(now()) <= 7
            && !$this->isTokenExpired();
    }
}
