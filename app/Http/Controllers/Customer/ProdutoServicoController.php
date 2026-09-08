<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ProdutoServico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProdutoServicoController extends Controller
{
    public function index(Request $request)
    {
        $cliente = Auth::user()->cliente;
        $search = $request->input('search');

        $query = $cliente->produtosServicos();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('descricao', 'like', "%{$search}%");
            });
        }

        $produtos = $query->orderBy('tipo')->orderBy('nome')->paginate(15);

        return view('customer.produtos_servicos.index', compact('produtos', 'search'));
    }

    public function create()
    {
        return view('customer.produtos_servicos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|string|in:Produto,Serviço',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco_padrao' => 'required|numeric|min:0',
            'unidade_medida' => 'nullable|string|max:50',
        ]);

        $cliente = Auth::user()->cliente;
        $cliente->produtosServicos()->create($validated);

        return redirect()->route('customer.produtos.index')->with('success', 'Cadastrado com sucesso!');
    }

    public function edit(ProdutoServico $produto)
    {
        $cliente = Auth::user()->cliente;
        
        if ($produto->cliente_id !== $cliente->id) {
            abort(403);
        }

        return view('customer.produtos_servicos.edit', compact('produto'));
    }

    public function update(Request $request, ProdutoServico $produto)
    {
        $cliente = Auth::user()->cliente;
        
        if ($produto->cliente_id !== $cliente->id) {
            abort(403);
        }

        $validated = $request->validate([
            'tipo' => 'required|string|in:Produto,Serviço',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco_padrao' => 'required|numeric|min:0',
            'unidade_medida' => 'nullable|string|max:50',
        ]);

        $produto->update($validated);

        return redirect()->route('customer.produtos.index')->with('success', 'Atualizado com sucesso!');
    }

    public function destroy(ProdutoServico $produto)
    {
        $cliente = Auth::user()->cliente;
        
        if ($produto->cliente_id !== $cliente->id) {
            abort(403);
        }

        $produto->delete();

        return redirect()->route('customer.produtos.index')->with('success', 'Excluído com sucesso!');
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('csv_file');
        $csvData = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_shift($csvData);

        // Normalize header
        $header = array_map(function($val) { return strtolower(trim($val)); }, $header);

        $cliente = Auth::user()->cliente;
        $count = 0;

        foreach ($csvData as $row) {
            if (count($header) !== count($row)) {
                continue;
            }

            $rowAssociative = array_combine($header, $row);

            $tipo = isset($rowAssociative['tipo']) && in_array(ucfirst(trim($rowAssociative['tipo'])), ['Produto', 'Serviço']) ? ucfirst(trim($rowAssociative['tipo'])) : 'Serviço';
            $nome = isset($rowAssociative['nome']) ? trim($rowAssociative['nome']) : null;
            $descricao = isset($rowAssociative['descricao']) ? trim($rowAssociative['descricao']) : null;
            $preco = isset($rowAssociative['preco_padrao']) ? floatval(trim($rowAssociative['preco_padrao'])) : 0;
            $unidade = isset($rowAssociative['unidade_medida']) ? trim($rowAssociative['unidade_medida']) : null;

            if ($nome) {
                $cliente->produtosServicos()->create([
                    'tipo' => $tipo,
                    'nome' => $nome,
                    'descricao' => $descricao,
                    'preco_padrao' => $preco,
                    'unidade_medida' => $unidade,
                ]);
                $count++;
            }
        }

        return redirect()->route('customer.produtos.index')->with('success', "Importação concluída! {$count} itens foram adicionados.");
    }
}
