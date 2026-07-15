<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Cliente;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        $materiais = Material::with('cliente')->latest()->paginate(10);
        return view('admin.materiais.index', compact('materiais'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        return view('admin.materiais.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'titulo' => 'required|string|max:255',
            'tipo' => 'nullable|string',
            'arquivo_path' => 'nullable|url',
        ]);

        Material::create([
            'cliente_id' => $request->cliente_id,
            'titulo' => $request->titulo,
            'tipo' => $request->tipo,
            'arquivo_path' => $request->arquivo_path,
            'status_aprovacao' => 'pendente'
        ]);

        return redirect()->route('admin.materiais.index')->with('success', 'Material enviado para o cliente!');
    }

    public function show($id)
    {
        $material = Material::with('cliente.user')->findOrFail($id);
        return view('admin.materiais.show', compact('material'));
    }

    public function edit($id)
    {
        $material = Material::findOrFail($id);
        $clientes = Cliente::all();
        return view('admin.materiais.edit', compact('material', 'clientes'));
    }

    public function update(Request $request, $id)
    {
        $material = Material::findOrFail($id);

        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'titulo' => 'required|string|max:255',
            'tipo' => 'nullable|string',
            'arquivo_path' => 'nullable|url',
            'status_aprovacao' => 'required|in:pendente,aprovado,reprovado',
        ]);

        $material->update($request->only([
            'cliente_id', 'titulo', 'tipo', 'arquivo_path', 'status_aprovacao'
        ]));

        return redirect()->route('admin.materiais.index')->with('success', 'Material atualizado.');
    }

    public function destroy($id)
    {
        Material::findOrFail($id)->delete();
        return redirect()->route('admin.materiais.index')->with('success', 'Material removido.');
    }
}
