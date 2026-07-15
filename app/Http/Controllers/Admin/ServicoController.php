<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Servico;
use Illuminate\Http\Request;

class ServicoController extends Controller
{
    public function index()
    {
        $servicos = Servico::latest()->paginate(10);
        return view('admin.servicos.index', compact('servicos'));
    }

    public function create()
    {
        return view('admin.servicos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric',
        ]);

        Servico::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'preco' => $request->preco,
            'status' => 'ativo'
        ]);

        return redirect()->route('admin.servicos.index')->with('success', 'Serviço adicionado ao catálogo!');
    }

    public function edit($id)
    {
        $servico = Servico::findOrFail($id);
        return view('admin.servicos.edit', compact('servico'));
    }

    public function update(Request $request, $id)
    {
        $servico = Servico::findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric',
            'status' => 'required|in:ativo,inativo',
        ]);

        $servico->update($request->only(['nome', 'descricao', 'preco', 'status']));

        return redirect()->route('admin.servicos.index')->with('success', 'Serviço atualizado.');
    }

    public function destroy($id)
    {
        Servico::findOrFail($id)->delete();
        return redirect()->route('admin.servicos.index')->with('success', 'Serviço removido.');
    }
}
