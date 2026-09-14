<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AgentKnowledge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KnowledgeController extends Controller
{
    protected function requireCliente()
    {
        $cliente = Auth::user()->cliente ?? null;
        if (!$cliente) {
            abort(403, 'Sua conta ainda não está vinculada a um cadastro empresarial.');
        }
        return $cliente;
    }

    protected function authorizeOwnership(AgentKnowledge $item, $cliente): void
    {
        if ((int) $item->cliente_id !== (int) $cliente->id) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $cliente = $this->requireCliente();

        $search = trim((string) $request->input('search', ''));

        $query = AgentKnowledge::where('cliente_id', $cliente->id);

        if ($search !== '') {
            $like = '%' . $search . '%';
            $query->where(function ($q) use ($like) {
                $q->where('titulo', 'like', $like)
                  ->orWhere('conteudo', 'like', $like);
            });
        }

        $itens = $query->latest()->paginate(12);

        return view('customer.conhecimento.index', compact('itens', 'search'));
    }

    public function create()
    {
        $this->requireCliente();
        return view('customer.conhecimento.create');
    }

    public function store(Request $request)
    {
        $cliente = $this->requireCliente();

        $validated = $request->validate([
            'titulo' => 'required|string|max:200',
            'conteudo' => 'required|string|max:20000',
            'ativo' => 'sometimes|boolean',
        ]);

        $validated['ativo'] = (bool) ($validated['ativo'] ?? true);
        $validated['cliente_id'] = $cliente->id;

        AgentKnowledge::create($validated);

        return redirect()->route('customer.conhecimento.index')
            ->with('success', 'Documento adicionado à base de conhecimento do Bruce.');
    }

    public function edit(AgentKnowledge $conhecimento)
    {
        $cliente = $this->requireCliente();
        $this->authorizeOwnership($conhecimento, $cliente);

        return view('customer.conhecimento.edit', ['item' => $conhecimento]);
    }

    public function update(Request $request, AgentKnowledge $conhecimento)
    {
        $cliente = $this->requireCliente();
        $this->authorizeOwnership($conhecimento, $cliente);

        $validated = $request->validate([
            'titulo' => 'required|string|max:200',
            'conteudo' => 'required|string|max:20000',
            'ativo' => 'sometimes|boolean',
        ]);

        $validated['ativo'] = (bool) ($validated['ativo'] ?? false);

        $conhecimento->update($validated);

        return redirect()->route('customer.conhecimento.index')
            ->with('success', 'Documento atualizado.');
    }

    public function destroy(AgentKnowledge $conhecimento)
    {
        $cliente = $this->requireCliente();
        $this->authorizeOwnership($conhecimento, $cliente);

        $conhecimento->delete();

        return redirect()->route('customer.conhecimento.index')
            ->with('success', 'Documento removido da base de conhecimento.');
    }
}
