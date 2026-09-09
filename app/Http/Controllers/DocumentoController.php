<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentoController extends Controller
{
    public function index()
    {
        $documentos = Documento::where('cliente_id', Auth::user()->cliente->id)
            ->latest()
            ->get();
            
        return view('customer.documentos.index', compact('documentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'arquivo' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg|max:10240',
            'tipo' => 'nullable|string',
        ]);

        $path = $request->file('arquivo')->store('documentos', 'public');

        Documento::create([
            'cliente_id' => Auth::user()->cliente->id, // Tenant isolation
            'nome' => $request->nome,
            'arquivo_path' => $path,
            'tipo' => $request->tipo ?? 'outro',
            'enviado_por' => 'cliente',
        ]);

        return back()->with('success', 'Documento enviado com sucesso!');
    }

    public function destroy(Documento $documento)
    {
        if ($documento->cliente_id !== Auth::user()->cliente->id) abort(403);

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($documento->arquivo_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($documento->arquivo_path);
        }
        
        $documento->delete();

        return back()->with('success', 'Documento excluído.');
    }
}
