<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pagina;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaginaController extends Controller
{
    public function index()
    {
        $paginas = Pagina::latest()->paginate(10);
        return view('admin.paginas.index', compact('paginas'));
    }

    public function create()
    {
        return view('admin.paginas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'conteudo' => 'nullable|string',
            'status' => 'required|in:rascunho,publicado'
        ]);

        Pagina::create([
            'titulo' => $request->titulo,
            'slug' => Str::slug($request->titulo),
            'conteudo' => $request->conteudo,
            'status' => $request->status
        ]);

        return redirect()->route('admin.paginas.index')->with('success', 'Página criada com sucesso!');
    }

    public function edit($id)
    {
        $pagina = Pagina::findOrFail($id);
        return view('admin.paginas.edit', compact('pagina'));
    }

    public function update(Request $request, $id)
    {
        $pagina = Pagina::findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'conteudo' => 'nullable|string',
            'status' => 'required|in:rascunho,publicado'
        ]);

        $pagina->update([
            'titulo' => $request->titulo,
            'slug' => Str::slug($request->titulo),
            'conteudo' => $request->conteudo,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.paginas.index')->with('success', 'Página atualizada.');
    }

    public function destroy($id)
    {
        Pagina::findOrFail($id)->delete();
        return redirect()->route('admin.paginas.index')->with('success', 'Página removida.');
    }
}
