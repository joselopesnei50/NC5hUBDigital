<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SystemCliente
{
    public const USER_EMAIL = 'nc5-interno@system.local';

    public static function get(): Cliente
    {
        $cliente = Cliente::where('is_system', true)->first();
        if ($cliente) {
            self::ensureUser($cliente);
            return $cliente->fresh();
        }

        $user = self::ensureSystemUser(null);

        $cliente = Cliente::create([
            'is_system' => true,
            'user_id' => $user->id,
            'razao_social' => 'NC5 Interno',
        ]);

        $user->update(['cliente_id' => $cliente->id]);

        return $cliente->fresh();
    }

    public static function user(): User
    {
        return User::where('email', self::USER_EMAIL)->firstOrFail();
    }

    protected static function ensureUser(Cliente $cliente): void
    {
        $user = self::ensureSystemUser($cliente->id);
        if ($cliente->user_id !== $user->id) {
            $cliente->update(['user_id' => $user->id]);
        }
    }

    protected static function ensureSystemUser(?int $clienteId): User
    {
        $user = User::where('email', self::USER_EMAIL)->first();
        if (!$user) {
            return User::create([
                'name' => 'NC5 Interno',
                'email' => self::USER_EMAIL,
                'password' => Hash::make(Str::random(60)),
                'role' => 'cliente',
                'cliente_id' => $clienteId,
            ]);
        }

        if ($user->role !== 'cliente' || ($clienteId && $user->cliente_id !== $clienteId)) {
            $user->update([
                'role' => 'cliente',
                'cliente_id' => $clienteId ?? $user->cliente_id,
            ]);
        }
        return $user;
    }
}
