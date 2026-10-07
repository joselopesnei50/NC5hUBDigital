<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\WhatsappInstance;
use App\Services\EvolutionApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WhatsappConnectionController extends Controller
{
    public function __construct(protected EvolutionApiService $evo) {}

    public function index()
    {
        $cliente = Auth::user()->cliente;
        $instance = WhatsappInstance::where('cliente_id', $cliente->id)->first();

        return view('customer.whatsapp.conexao', [
            'instance' => $instance,
            'evoConfigured' => $this->evo->isConfigured(),
        ]);
    }

    public function connect(Request $request)
    {
        $cliente = Auth::user()->cliente;

        if (!$this->evo->isConfigured()) {
            return back()->with('error', 'Evolution API não configurada. Contate o administrador.');
        }

        $instance = WhatsappInstance::firstOrCreate(
            ['cliente_id' => $cliente->id],
            [
                'instance_name' => 'nc5-cliente-' . $cliente->id . '-' . Str::random(6),
                'instance_token' => Str::random(64),
                'status' => 'connecting',
            ]
        );

        $result = $this->evo->createInstance($instance->instance_name, $instance->instance_token);

        if (isset($result['error'])) {
            return back()->with('error', $result['error']);
        }

        $instance->update(['status' => 'connecting']);

        return redirect()->route('customer.whatsapp.conexao')->with('success', 'Instância criada. Escaneie o QR code abaixo.');
    }

    public function qr(Request $request)
    {
        $cliente = Auth::user()->cliente;
        $instance = WhatsappInstance::where('cliente_id', $cliente->id)->firstOrFail();

        return response()->json($this->evo->fetchQrCode($instance->instance_name));
    }

    public function status(Request $request)
    {
        $cliente = Auth::user()->cliente;
        $instance = WhatsappInstance::where('cliente_id', $cliente->id)->firstOrFail();

        $state = $this->evo->getConnectionState($instance->instance_name);
        $newStatus = $state['instance']['state'] ?? ($state['state'] ?? $instance->status);

        if (in_array($newStatus, ['open', 'connecting', 'close'])) {
            $instance->update([
                'status' => $newStatus,
                'last_status_check_at' => now(),
                'connected_at' => $newStatus === 'open' && !$instance->connected_at ? now() : $instance->connected_at,
            ]);
        }

        return response()->json([
            'status' => $instance->fresh()->status,
            'raw' => $state,
        ]);
    }

    public function disconnect(Request $request)
    {
        $cliente = Auth::user()->cliente;
        $instance = WhatsappInstance::where('cliente_id', $cliente->id)->firstOrFail();

        $this->evo->logout($instance->instance_name);
        $this->evo->deleteInstance($instance->instance_name);
        $instance->delete();

        return redirect()->route('customer.whatsapp.conexao')->with('success', 'WhatsApp desconectado.');
    }
}
