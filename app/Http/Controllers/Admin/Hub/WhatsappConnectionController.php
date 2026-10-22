<?php

namespace App\Http\Controllers\Admin\Hub;

use App\Http\Controllers\Controller;
use App\Models\WhatsappInstance;
use App\Services\CloudApiOnboardingService;
use App\Services\EvolutionApiService;
use App\Services\MetaCloudApiService;
use App\Services\SystemCliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WhatsappConnectionController extends Controller
{
    public function __construct(
        protected EvolutionApiService $evo,
        protected CloudApiOnboardingService $cloudOnboarding,
    ) {}

    public function index()
    {
        $cliente  = SystemCliente::get();
        $instance = WhatsappInstance::where('cliente_id', $cliente->id)->first();

        $templates = [];
        if ($instance && $instance->isCloudApi()) {
            $res = MetaCloudApiService::forInstance($instance)->getTemplates();
            $templates = $res['data'] ?? [];
        }

        return view('admin.hub.whatsapp.conexao', [
            'instance'           => $instance,
            'evoConfigured'      => $this->evo->isConfigured(),
            'metaConfigured'     => $this->cloudOnboarding->isConfigured(),
            'embeddedConfigured' => $this->cloudOnboarding->isEmbeddedSignupConfigured(),
            'metaAppId'          => $this->cloudOnboarding->getAppId(),
            'metaConfigId'       => $this->cloudOnboarding->getConfigId(),
            'templates'          => $templates,
        ]);
    }

    public function connect(Request $request)
    {
        $cliente = SystemCliente::get();

        if (!$this->evo->isConfigured()) {
            return back()->with('error', 'Evolution API não configurada. Configure em Configurações Globais ou use a API Oficial da Meta.');
        }

        $instance = WhatsappInstance::firstOrCreate(
            ['cliente_id' => $cliente->id],
            [
                'provider'       => WhatsappInstance::PROVIDER_EVOLUTION,
                'instance_name'  => 'nc5-admin-' . $cliente->id . '-' . Str::random(6),
                'instance_token' => Str::random(64),
                'status'         => 'connecting',
            ]
        );

        $result = $this->evo->createInstance($instance->instance_name, $instance->instance_token);

        if (isset($result['error'])) {
            $msg = $result['error'];
            if (!empty($result['details'])) {
                $msg .= ' — Evolution respondeu: ' . mb_substr((string) $result['details'], 0, 300);
            }
            if ($instance->wasRecentlyCreated) {
                $instance->delete();
            }
            return back()->with('error', $msg);
        }

        $instance->update([
            'provider' => WhatsappInstance::PROVIDER_EVOLUTION,
            'status'   => 'connecting',
        ]);

        return redirect()->route('admin.hub.whatsapp.conexao')->with('success', 'Instância QR Code criada. Escaneie o QR code.');
    }

    /**
     * Conexão via Meta Embedded Signup (Popup FB.login)
     */
    public function connectCloudEmbedded(Request $request)
    {
        $cliente = SystemCliente::get();

        $data = $request->validate([
            'code'            => ['required', 'string'],
            'waba_id'         => ['required', 'string'],
            'phone_number_id' => ['required', 'string'],
            'pin'             => ['required', 'string', 'regex:/^\d{6}$/'],
        ]);

        try {
            $instance = $this->cloudOnboarding->completeSignup(
                clienteId:       (int) $cliente->id,
                code:            $data['code'],
                wabaId:          $data['waba_id'],
                phoneNumberId:   $data['phone_number_id'],
                registrationPin: $data['pin'],
            );
        } catch (\Throwable $e) {
            Log::error('Admin WhatsApp Cloud Embedded Signup falhou', [
                'cliente_id' => $cliente->id,
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'ok'    => false,
                'error' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'ok'          => true,
            'instance_id' => $instance->id,
            'redirect'    => route('admin.hub.whatsapp.conexao'),
        ]);
    }

    /**
     * Conexão Manual Assistida via API Oficial da Meta (WABA ID + Phone Number ID + System User Token)
     */
    public function connectCloudManual(Request $request)
    {
        $cliente = SystemCliente::get();

        $data = $request->validate([
            'waba_id'         => ['required', 'string', 'regex:/^\d{6,30}$/'],
            'phone_number_id' => ['required', 'string', 'regex:/^\d{6,30}$/'],
            'access_token'    => ['required', 'string', 'min:80', 'max:600'],
            'pin'             => ['nullable', 'string', 'regex:/^\d{6}$/'],
        ], [
            'waba_id.regex'         => 'O WABA ID deve conter apenas números.',
            'phone_number_id.regex' => 'O Phone Number ID deve conter apenas números.',
            'access_token.min'      => 'O System User Access Token parece incompleto.',
            'pin.regex'             => 'O PIN de verificação deve ter exatamente 6 dígitos numéricos.',
        ]);

        try {
            $this->cloudOnboarding->completeManualSignup(
                clienteId:       (int) $cliente->id,
                wabaId:          $data['waba_id'],
                phoneNumberId:   $data['phone_number_id'],
                accessToken:     $data['access_token'],
                registrationPin: $data['pin'] ?? null,
            );
        } catch (\Throwable $e) {
            Log::error('Admin WhatsApp Cloud Manual Signup falhou', [
                'cliente_id' => $cliente->id,
                'error'      => $e->getMessage(),
            ]);

            return back()
                ->withInput($request->except('access_token', 'pin'))
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.hub.whatsapp.conexao')
            ->with('success', 'WhatsApp API Oficial da Meta conectado com sucesso no Hub NC5!');
    }

    public function qr(Request $request)
    {
        $cliente  = SystemCliente::get();
        $instance = WhatsappInstance::where('cliente_id', $cliente->id)->firstOrFail();

        if ($instance->isCloudApi()) {
            return response()->json(['status' => 'open']);
        }

        return response()->json($this->evo->fetchQrCode($instance->instance_name));
    }

    public function status(Request $request)
    {
        $cliente  = SystemCliente::get();
        $instance = WhatsappInstance::where('cliente_id', $cliente->id)->firstOrFail();

        if ($instance->isCloudApi()) {
            return response()->json([
                'status'   => $instance->status,
                'provider' => 'cloud_api',
            ]);
        }

        $state     = $this->evo->getConnectionState($instance->instance_name);
        $newStatus = $state['instance']['state'] ?? ($state['state'] ?? $instance->status);

        if (in_array($newStatus, ['open', 'connecting', 'close'], true)) {
            $instance->update([
                'status'               => $newStatus,
                'last_status_check_at' => now(),
                'connected_at'         => $newStatus === 'open' && !$instance->connected_at ? now() : $instance->connected_at,
            ]);
        }

        return response()->json([
            'status' => $instance->fresh()->status,
            'raw'    => $state,
        ]);
    }

    public function disconnect(Request $request)
    {
        $cliente  = SystemCliente::get();
        $instance = WhatsappInstance::where('cliente_id', $cliente->id)->firstOrFail();

        if ($instance->isEvolution() && $this->evo->isConfigured()) {
            $this->evo->logout($instance->instance_name);
            $this->evo->deleteInstance($instance->instance_name);
        }

        $instance->delete();

        return redirect()->route('admin.hub.whatsapp.conexao')->with('success', 'WhatsApp desconectado.');
    }
}
