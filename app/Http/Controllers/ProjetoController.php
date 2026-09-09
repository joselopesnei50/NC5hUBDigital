<?php

namespace App\Http\Controllers;

use App\Models\Projeto;
use Illuminate\Http\Request;

class ProjetoController extends Controller
{
    public function index()
    {
        $projetos = Projeto::with('cliente', 'tarefas')->get();
        return view('customer.projetos.index', compact('projetos'));
    }

    public function create()
    {
        $clientes = \App\Models\Cliente::all();
        return view('customer.projetos.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cliente_id' => 'required|exists:clientes,id',
            'status' => 'required|string',
        ]);

        Projeto::create($request->all());

        return redirect()->route('customer.projetos.index')
            ->with('success', 'Projeto criado com sucesso.');
    }

    public function show(Projeto $projeto)
    {
        $projeto->load('tarefas', 'cliente');
        return view('customer.projetos.show', compact('projeto'));
    }

    public function edit(Projeto $projeto)
    {
        $clientes = \App\Models\Cliente::all();
        return view('customer.projetos.edit', compact('projeto', 'clientes'));
    }

    public function update(Request $request, Projeto $projeto)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'status' => 'required|string',
        ]);

        $projeto->update($request->all());

        return redirect()->route('customer.projetos.index')
            ->with('success', 'Projeto atualizado com sucesso.');
    }

    public function destroy(Projeto $projeto)
    {
        $projeto->delete();

        return redirect()->route('customer.projetos.index')
            ->with('success', 'Projeto excluído com sucesso.');
    }

    public function storeTarefa(Request $request, Projeto $projeto)
    {
        $request->validate(['titulo' => 'required|string|max:255']);
        $projeto->tarefas()->create(['titulo' => $request->titulo, 'concluida' => false]);
        return back()->with('success', 'Tarefa adicionada.');
    }

    public function toggleTarefa(\App\Models\ProjetoTarefa $tarefa)
    {
        $tarefa->update(['concluida' => !$tarefa->concluida]);
        return back()->with('success', 'Status da tarefa atualizado.');
    }
}
