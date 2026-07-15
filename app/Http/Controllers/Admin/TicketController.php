<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with('cliente')->latest()->paginate(10);
        return view('admin.tickets.index', compact('tickets'));
    }

    public function create()
    {
        return redirect()->route('admin.tickets.index');
    }

    public function show(Ticket $ticket)
    {
        $ticket->load('mensagens.user', 'cliente');
        return view('admin.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $request->validate(['mensagem' => 'required|string']);

        if ($ticket->status === 'fechado') {
            $ticket->update(['status' => 'aberto']);
        }

        \App\Models\TicketMensagem::create([
            'ticket_id' => $ticket->id,
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'mensagem' => $request->mensagem
        ]);

        return back()->with('success', 'Resposta enviada com sucesso!');
    }

    public function close(Ticket $ticket)
    {
        $ticket->update(['status' => 'fechado']);
        return back()->with('success', 'Chamado encerrado com sucesso.');
    }
}
