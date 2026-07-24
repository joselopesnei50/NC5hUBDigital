<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Cliente;
use App\Mail\MaterialPendenteMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MaterialController extends Controller
{
    public function index()
    {
        $materiais = Material::with('cliente.user')->latest()->paginate(12);
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
            'tipo' => 'nullable|string|max:255',
            'arquivo_path' => 'nullable|url',
            'descricao' => 'nullable|string',
            'anexo_admin' => 'nullable|file|max:25600', // max 25MB
        ]);

        $anexoPath = null;
        if ($request->hasFile('anexo_admin')) {
            $anexoPath = $request->file('anexo_admin')->store('materials', 'public');
        }

        $material = Material::create([
            'cliente_id' => $request->cliente_id,
            'titulo' => $request->titulo,
            'tipo' => $request->tipo,
            'arquivo_path' => $request->arquivo_path,
            'anexo_admin_path' => $anexoPath,
            'descricao' => $request->descricao,
            'status_aprovacao' => 'pendente'
        ]);

        // Dispara e-mail de notificação para o cliente
        try {
            $material->load('cliente.user');
            $email = $material->cliente->user->email ?? null;
            if ($email) {
                Mail::to($email)->send(new MaterialPendenteMail($material));
            }
        } catch (\Throwable $e) {
            Log::error('Falha ao enviar e-mail de material pendente #' . $material->id . ': ' . $e->getMessage());
        }

        return redirect()->route('admin.materiais.index')->with('success', 'Material cadastrado e enviado para aprovação do cliente com sucesso!');
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
            'tipo' => 'nullable|string|max:255',
            'arquivo_path' => 'nullable|url',
            'descricao' => 'nullable|string',
            'status_aprovacao' => 'required|in:pendente,aprovado,ajustes_solicitados,reprovado',
            'anexo_admin' => 'nullable|file|max:25600',
        ]);

        $data = $request->only([
            'cliente_id', 'titulo', 'tipo', 'arquivo_path', 'descricao', 'status_aprovacao', 'comentario_cliente'
        ]);

        if ($request->hasFile('anexo_admin')) {
            $data['anexo_admin_path'] = $request->file('anexo_admin')->store('materials', 'public');
        }

        $material->update($data);

        return redirect()->route('admin.materiais.index')->with('success', 'Material atualizado com sucesso.');
    }

    public function destroy($id)
    {
        Material::findOrFail($id)->delete();
        return redirect()->route('admin.materiais.index')->with('success', 'Material removido.');
    }
}
