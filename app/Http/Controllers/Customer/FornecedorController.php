<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Fornecedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FornecedorController extends Controller
{
    public function index(Request $request)
    {
        $cliente = Auth::user()->cliente;
        $search = $request->input('search');

        $query = $cliente->fornecedores();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('cnpj_cpf', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telefone', 'like', "%{$search}%")
                  ->orWhere('categoria', 'like', "%{$search}%");
            });
        }

        $fornecedores = $query->orderBy('nome')->paginate(15)->withQueryString();

        return view('customer.fornecedores.index', compact('fornecedores', 'search'));
    }

    public function create()
    {
        return view('customer.fornecedores.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome'      => 'required|string|max:255',
            'cnpj_cpf'  => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:255',
            'telefone'  => 'nullable|string|max:30',
            'endereco'  => 'nullable|string|max:255',
            'categoria' => 'nullable|string|max:60',
            'notas'     => 'nullable|string',
        ], [
            'nome.required' => 'O nome do fornecedor é obrigatório.',
        ]);

        Auth::user()->cliente->fornecedores()->create($validated);

        return redirect()->route('customer.fornecedores.index')
            ->with('success', 'Fornecedor cadastrado com sucesso.');
    }

    public function edit(Fornecedor $fornecedor)
    {
        $this->authorizeOwnership($fornecedor);
        return view('customer.fornecedores.edit', compact('fornecedor'));
    }

    public function update(Request $request, Fornecedor $fornecedor)
    {
        $this->authorizeOwnership($fornecedor);

        $validated = $request->validate([
            'nome'      => 'required|string|max:255',
            'cnpj_cpf'  => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:255',
            'telefone'  => 'nullable|string|max:30',
            'endereco'  => 'nullable|string|max:255',
            'categoria' => 'nullable|string|max:60',
            'notas'     => 'nullable|string',
        ]);

        $fornecedor->update($validated);

        return redirect()->route('customer.fornecedores.index')
            ->with('success', 'Fornecedor atualizado.');
    }

    public function destroy(Fornecedor $fornecedor)
    {
        $this->authorizeOwnership($fornecedor);
        $fornecedor->delete();

        return redirect()->route('customer.fornecedores.index')
            ->with('success', 'Fornecedor removido.');
    }

    protected function authorizeOwnership(Fornecedor $fornecedor): void
    {
        if ($fornecedor->cliente_id !== Auth::user()->cliente->id) {
            abort(403);
        }
    }
}
