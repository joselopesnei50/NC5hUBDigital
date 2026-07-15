<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Briefing;
use App\Models\Cliente;
use Illuminate\Http\Request;

class BriefingController extends Controller
{
    public function store(Request $request, Cliente $cliente)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'arquivo_admin' => 'nullable|file|max:10240' // 10MB max
        ]);

        $path = null;
        if ($request->hasFile('arquivo_admin')) {
            $path = $request->file('arquivo_admin')->store('briefings', 'public');
        }

        Briefing::create([
            'cliente_id' => $cliente->id,
            'titulo' => $request->titulo,
            'descricao' => $request->descricao,
            'anexo_admin_path' => $path,
            'status' => 'pendente'
        ]);

        return back()->with('success', 'Briefing solicitado com sucesso ao cliente.');
    }
}
