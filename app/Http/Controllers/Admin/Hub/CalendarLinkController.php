<?php

namespace App\Http\Controllers\Admin\Hub;

use App\Http\Controllers\Controller;
use App\Services\CalendarTokenService;
use App\Services\SystemCliente;
use Illuminate\Http\Request;

class CalendarLinkController extends Controller
{
    public function __construct(protected CalendarTokenService $tokens) {}

    public function regenerateCliente(Request $request)
    {
        $cliente = SystemCliente::get();
        $this->tokens->regenerate($cliente);

        return back()->with('success', 'Link da agenda regenerado. O link antigo não funciona mais.');
    }
}
