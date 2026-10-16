<?php

namespace App\Http\Controllers\Admin\Hub;

use App\Http\Controllers\Customer\WhatsappDashboardController as BaseController;
use App\Services\SystemCliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WhatsappDashboardController extends BaseController
{
    public function index(Request $request)
    {
        $systemUser = SystemCliente::user();
        Auth::setUser($systemUser);

        $response = parent::index($request);

        if ($response instanceof \Illuminate\View\View) {
            return view('admin.hub.whatsapp.dashboard', $response->getData());
        }

        return $response;
    }
}
