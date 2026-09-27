<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Configuracao extends Model
{
    protected $table = 'configuracoes';
    
    protected $fillable = [
        'chave',
        'valor',
        'tipo',
    ];

    /**
     * Limpa o cache sempre que uma configuração for salva, atualizada ou excluída.
     */
    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('configuracoes_globais');
        });

        static::deleted(function () {
            Cache::forget('configuracoes_globais');
        });
    }

    /**
     * Retorna todas as configurações como um array [chave => valor] a partir do cache.
     */
    public static function todas()
    {
        return Cache::rememberForever('configuracoes_globais', function () {
            return self::pluck('valor', 'chave')->toArray();
        });
    }

    /**
     * Retorna uma configuração específica pelo nome da chave.
     */
    public static function get($chave, $default = null)
    {
        $configs = self::todas();
        // Usar ?? antes do ?: pra evitar "Undefined array key" quando a chave nunca
        // foi salva (500 em prod). O ?: continua caindo no default pra string vazia
        // (form salvo em branco), mantendo o fallback pro .env.
        return ($configs[$chave] ?? null) ?: $default;
    }
}
