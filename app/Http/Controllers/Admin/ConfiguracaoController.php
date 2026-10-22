<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Configuracao;

class ConfiguracaoController extends Controller
{
    /**
     * Chaves de segredos: só sobrescreve quando o admin digita um valor novo.
     * Se vier vazio no form, mantém o valor atual no banco (evita apagar sem querer).
     */
    private array $sensitiveKeys = [
        'brevo_api_key',
        'deepseek_api_key',
        'abacatepay_api_key',
        'abacatepay_webhook_secret',
        'apify_token',
        'google_client_secret',
        'evolution_global_key',
        'meta_app_secret',
    ];

    public function index()
    {
        $configuracoes = Configuracao::todas();
        return view('admin.configuracoes.index', compact('configuracoes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brevo_smtp_login'          => 'nullable|string',
            'brevo_api_key'             => 'nullable|string',
            'mail_from_address'         => 'required|email',
            'mail_from_name'            => 'required|string|max:255',
            'mail_reply_to'             => 'nullable|email',
            'mail_reply_to_name'        => 'nullable|string|max:255',
            'deepseek_api_key'          => 'nullable|string',
            'abacatepay_api_key'        => 'nullable|string',
            'abacatepay_webhook_secret' => 'nullable|string',
            'apify_token'               => 'nullable|string',
            'google_client_id'          => 'nullable|string',
            'google_client_secret'      => 'nullable|string',
            'google_redirect_uri'       => 'nullable|string',
            'evolution_api_url'         => 'nullable|string',
            'evolution_global_key'      => 'nullable|string',
            'meta_app_id'               => 'nullable|string|max:120',
            'meta_app_secret'           => 'nullable|string|max:255',
            'meta_cloud_config_id'      => 'nullable|string|max:120',
            'meta_webhook_verify_token' => 'nullable|string|max:255',
            'meta_webhook_forward_url'  => 'nullable|url|max:500',
        ]);

        foreach ($validated as $chave => $valor) {
            // Não zera segredos existentes se o campo veio vazio
            if (in_array($chave, $this->sensitiveKeys, true) && empty($valor)) {
                continue;
            }

            Configuracao::updateOrCreate(
                ['chave' => $chave],
                ['valor' => $valor]
            );
        }

        return redirect()->route('admin.configuracoes.index')
                         ->with('success', 'Configurações globais salvas com sucesso!');
    }
}
