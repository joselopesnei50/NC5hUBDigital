<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contato;

class ContatoController extends Controller
{
    public function index()
    {
        return view('public.contato');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome'     => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'assunto'  => 'required|string|max:255',
            'mensagem' => 'required|string|max:5000',
        ]);

        Contato::create($request->only(['nome', 'email', 'whatsapp', 'assunto', 'mensagem']));

        return back()->with('success', 'Mensagem enviada com sucesso! Nossa equipe entrará em contato em breve.');
    }
}
