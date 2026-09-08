<?php

namespace App\Http\Controllers;

use App\Services\GoogleBusinessProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoogleBusinessController extends Controller
{
    protected $googleService;

    public function __construct(GoogleBusinessProfileService $googleService)
    {
        $this->googleService = $googleService;
    }

    public function index()
    {
        $cliente = Auth::user()->cliente;
        $isConnected = !empty($cliente->google_refresh_token);
        
        $locations = [];
        if ($isConnected) {
            try {
                $locations = $this->googleService->getLocations($cliente);
            } catch (\Exception $e) {
                $isConnected = false;
            }
        }

        return view('customer.google-business.index', compact('isConnected', 'locations', 'cliente'));
    }

    public function redirectToGoogle()
    {
        $authUrl = $this->googleService->getAuthUrl();
        return redirect()->away($authUrl);
    }

    public function handleGoogleCallback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('customer.google-business.index')
                ->with('error', 'A autorização foi recusada.');
        }

        if (!$request->has('code')) {
            return redirect()->route('customer.google-business.index')
                ->with('error', 'Código de autorização não recebido.');
        }

        $cliente = Auth::user()->cliente;
        
        try {
            $this->googleService->authenticateAndSaveTokens($request->code, $cliente);
            return redirect()->route('customer.google-business.index')
                ->with('success', 'Conta do Google Meu Negócio conectada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('customer.google-business.index')
                ->with('error', 'Falha ao conectar: ' . $e->getMessage());
        }
    }

    public function disconnect()
    {
        $cliente = Auth::user()->cliente;
        
        $cliente->update([
            'google_access_token' => null,
            'google_refresh_token' => null,
            'google_token_expires_at' => null,
            'google_location_id' => null,
        ]);

        return redirect()->route('customer.google-business.index')
            ->with('success', 'Conta desconectada com sucesso.');
    }
}
