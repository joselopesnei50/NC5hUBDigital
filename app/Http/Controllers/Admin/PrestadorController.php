<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prestador;
use Illuminate\Http\Request;

class PrestadorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Prestador::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('cpf_cnpj', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telefone', 'like', "%{$search}%")
                  ->orWhere('categoria', 'like', "%{$search}%");
            });
        }

        $prestadores = $query->orderBy('nome')->paginate(15)->withQueryString();

        return view('admin.prestadores.index', compact('prestadores', 'search'));
    }

    public function create()
    {
        return view('admin.prestadores.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validatePrestador($request);

        Prestador::create($validated);

        return redirect()->route('admin.prestadores.index')
            ->with('success', 'Prestador cadastrado com sucesso.');
    }

    public function show(Prestador $prestador)
    {
        $prestador->load(['contratos' => fn ($q) => $q->latest()]);
        return view('admin.prestadores.show', compact('prestador'));
    }

    public function edit(Prestador $prestador)
    {
        return view('admin.prestadores.edit', compact('prestador'));
    }

    public function update(Request $request, Prestador $prestador)
    {
        $validated = $this->validatePrestador($request);

        $prestador->update($validated);

        return redirect()->route('admin.prestadores.index')
            ->with('success', 'Prestador atualizado.');
    }

    public function destroy(Prestador $prestador)
    {
        $prestador->delete();

        return redirect()->route('admin.prestadores.index')
            ->with('success', 'Prestador removido.');
    }

    protected function validatePrestador(Request $request): array
    {
        return $request->validate([
            'nome'        => 'required|string|max:255',
            'tipo_pessoa' => 'required|in:PF,PJ',
            'cpf_cnpj'    => 'nullable|string|max:20',
            'rg_ie'       => 'nullable|string|max:30',
            'email'       => 'nullable|email|max:255',
            'telefone'    => 'nullable|string|max:30',
            'endereco'    => 'nullable|string|max:255',
            'cidade'      => 'nullable|string|max:120',
            'uf'          => 'nullable|string|size:2',
            'cep'         => 'nullable|string|max:10',
            'categoria'   => 'nullable|string|max:80',
            'banco'       => 'nullable|string|max:80',
            'agencia'     => 'nullable|string|max:20',
            'conta'       => 'nullable|string|max:30',
            'chave_pix'   => 'nullable|string|max:120',
            'notas'       => 'nullable|string',
            'ativo'       => 'nullable|boolean',
        ]) + ['ativo' => (bool) $request->input('ativo', true)];
    }
}
