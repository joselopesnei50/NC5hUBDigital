<?php

namespace App\Http\Controllers;

use App\Models\Projeto;
use App\Models\ClienteFinal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjetoController extends Controller
{
    public function index()
    {
        $projetos = Projeto::with('clienteFinal', 'tarefas')
            ->where('cliente_id', Auth::user()->cliente->id)
            ->get();
        return view('customer.projetos.index', compact('projetos'));
    }

    public function create()
    {
        $clientesFinais = ClienteFinal::where('cliente_id', Auth::user()->cliente->id)->get();
        return view('customer.projetos.create', compact('clientesFinais'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cliente_final_id' => 'nullable|exists:clientes_finais,id',
            'status' => 'required|string',
        ]);

        $data = $request->all();
        $data['cliente_id'] = Auth::user()->cliente->id; // Tenant isolation

        Projeto::create($data);

        return redirect()->route('customer.projetos.index')
            ->with('success', 'Projeto criado com sucesso.');
    }

    public function show(Projeto $projeto)
    {
        if ($projeto->cliente_id !== Auth::user()->cliente->id) abort(403);

        $projeto->load('tarefas', 'clienteFinal');
        return view('customer.projetos.show', compact('projeto'));
    }

    public function edit(Projeto $projeto)
    {
        if ($projeto->cliente_id !== Auth::user()->cliente->id) abort(403);

        $clientesFinais = ClienteFinal::where('cliente_id', Auth::user()->cliente->id)->get();
        return view('customer.projetos.edit', compact('projeto', 'clientesFinais'));
    }

    public function update(Request $request, Projeto $projeto)
    {
        if ($projeto->cliente_id !== Auth::user()->cliente->id) abort(403);

        $request->validate([
            'nome' => 'required|string|max:255',
            'cliente_final_id' => 'nullable|exists:clientes_finais,id',
            'status' => 'required|string',
        ]);

        $projeto->update($request->all());

        return redirect()->route('customer.projetos.index')
            ->with('success', 'Projeto atualizado com sucesso.');
    }

    public function destroy(Projeto $projeto)
    {
        if ($projeto->cliente_id !== Auth::user()->cliente->id) abort(403);

        $projeto->delete();

        return redirect()->route('customer.projetos.index')
            ->with('success', 'Projeto excluído com sucesso.');
    }

    public function storeTarefa(Request $request, Projeto $projeto)
    {
        if ($projeto->cliente_id !== Auth::user()->cliente->id) abort(403);

        $request->validate(['titulo' => 'required|string|max:255']);
        $projeto->tarefas()->create(['titulo' => $request->titulo, 'concluida' => false]);
        return back()->with('success', 'Tarefa adicionada.');
    }

    public function toggleTarefa(\App\Models\ProjetoTarefa $tarefa)
    {
        if ($tarefa->projeto->cliente_id !== Auth::user()->cliente->id) abort(403);

        $tarefa->update(['concluida' => !$tarefa->concluida]);
        return back()->with('success', 'Status da tarefa atualizado.');
    }
}
