<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ClienteFinal;
use App\Services\CalendarTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarLinkController extends Controller
{
    public function __construct(protected CalendarTokenService $tokens) {}

    public function regenerateCliente(Request $request)
    {
        $cliente = Auth::user()->cliente;
        $this->tokens->regenerate($cliente);

        return back()->with('success', 'Link da agenda regenerado. O link antigo não funciona mais.');
    }

    public function regenerateClienteFinal(Request $request, ClienteFinal $clientes_finai)
    {
        $cliente = Auth::user()->cliente;
        abort_unless((int) $clientes_finai->cliente_id === (int) $cliente->id, 404);
        $this->tokens->regenerate($clientes_finai);

        return back()->with('success', 'Link regenerado. O antigo não funciona mais.');
    }
}
