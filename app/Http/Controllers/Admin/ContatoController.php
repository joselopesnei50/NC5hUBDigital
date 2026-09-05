<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contato;
use Illuminate\Http\Request;

class ContatoController extends Controller
{
    public function index()
    {
        $contatos = Contato::latest()->paginate(15);
        return view('admin.contatos.index', compact('contatos'));
    }

    public function show(Contato $contato)
    {
        if ($contato->status === 'novo') {
            $contato->update(['status' => 'lido']);
        }

        return view('admin.contatos.show', compact('contato'));
    }

    public function updateStatus(Request $request, Contato $contato)
    {
        $request->validate(['status' => 'required|in:novo,lido,respondido']);
        $contato->update(['status' => $request->status]);

        return back()->with('success', 'Status atualizado com sucesso.');
    }

    public function destroy(Contato $contato)
    {
        $contato->delete();
        return redirect()->route('admin.contatos.index')->with('success', 'Contato removido com sucesso.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'integer|exists:contatos,id',
        ]);

        Contato::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.contatos.index')->with('success', 'Contatos selecionados removidos com sucesso.');
    }
}
