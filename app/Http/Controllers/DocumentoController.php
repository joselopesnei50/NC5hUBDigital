<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    public function index()
    {
        $documentos = Documento::with('cliente')->latest()->get();
        return view('customer.documentos.index', compact('documentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'arquivo' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg|max:10240', // max 10MB
            'tipo' => 'nullable|string',
        ]);

        $path = $request->file('arquivo')->store('documentos', 'public');

        Documento::create([
            'cliente_id' => $request->cliente_id ?? null, // Assuming you might link it to a client if admin, or hardcode if customer
            'nome' => $request->nome,
            'arquivo_path' => $path,
            'tipo' => $request->tipo ?? 'outro',
            'enviado_por' => 'cliente',
        ]);

        return back()->with('success', 'Documento enviado com sucesso!');
    }

    public function destroy(Documento $documento)
    {
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($documento->arquivo_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($documento->arquivo_path);
        }
        
        $documento->delete();

        return back()->with('success', 'Documento excluído.');
    }
}
