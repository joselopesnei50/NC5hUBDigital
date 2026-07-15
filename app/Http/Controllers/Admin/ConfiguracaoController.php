<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Configuracao;

class ConfiguracaoController extends Controller
{
    public function index()
    {
        $configuracoes = Configuracao::todas();
        return view('admin.configuracoes.index', compact('configuracoes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brevo_smtp_login' => 'nullable|string',
            'brevo_api_key' => 'nullable|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string|max:255',
            'deepseek_api_key' => 'nullable|string',
            'abacatepay_api_key' => 'nullable|string',
        ]);

        foreach ($validated as $chave => $valor) {
            Configuracao::updateOrCreate(
                ['chave' => $chave],
                ['valor' => $valor]
            );
        }

        return redirect()->route('admin.configuracoes.index')
                         ->with('success', 'Configurações globais salvas com sucesso!');
    }
}
