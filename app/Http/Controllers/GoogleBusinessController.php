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

    protected function requireCliente()
    {
        $cliente = Auth::user()->cliente ?? null;
        if (!$cliente) {
            return redirect()->route('customer.index')
                ->with('error', 'Sua conta ainda não está vinculada a um cadastro empresarial. Fale com o suporte.');
        }
        return $cliente;
    }

    public function index()
    {
        $cliente = $this->requireCliente();
        if (!$cliente instanceof \App\Models\Cliente) return $cliente;

        $isConnected = !empty($cliente->google_refresh_token);

        $locations = [];
        $apiError = null;
        if ($isConnected) {
            try {
                $locations = $this->googleService->getLocations($cliente);
            } catch (\Exception $e) {
                $apiError = $e->getMessage();
            }
        }

        return view('customer.google-business.index', compact('isConnected', 'locations', 'cliente', 'apiError'));
    }

    public function redirectToGoogle()
    {
        $cliente = $this->requireCliente();
        if (!$cliente instanceof \App\Models\Cliente) return $cliente;

        if (
            empty(\App\Models\Configuracao::get('google_client_id')) ||
            empty(\App\Models\Configuracao::get('google_client_secret')) ||
            empty(\App\Models\Configuracao::get('google_redirect_uri'))
        ) {
            return redirect()->route('customer.google-business.index')
                ->with('error', 'O administrador do sistema ainda não configurou corretamente todas as chaves (ID, Secret e Redirect URI) do Google Meu Negócio.');
        }

        $authUrl = $this->googleService->getAuthUrl();
        return redirect()->away($authUrl);
    }

    public function handleGoogleCallback(Request $request)
    {
        $cliente = $this->requireCliente();
        if (!$cliente instanceof \App\Models\Cliente) return $cliente;

        if ($request->has('error')) {
            return redirect()->route('customer.google-business.index')
                ->with('error', 'A autorização foi recusada.');
        }

        if (!$request->has('code')) {
            return redirect()->route('customer.google-business.index')
                ->with('error', 'Código de autorização não recebido.');
        }

        try {
            $this->googleService->authenticateAndSaveTokens($request->code, $cliente);
            return redirect()->route('customer.google-business.index')
                ->with('success', 'Conta do Google Meu Negócio conectada com sucesso!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('[GoogleBusiness] Falha no callback do cliente ' . $cliente->id . ': ' . $e->getMessage());
            return redirect()->route('customer.google-business.index')
                ->with('error', 'Falha ao conectar sua conta Google. Tente novamente ou fale com o suporte.');
        }
    }

    public function disconnect()
    {
        $cliente = $this->requireCliente();
        if (!$cliente instanceof \App\Models\Cliente) return $cliente;

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
