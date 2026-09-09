<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AgentNotificationDraft;
use App\Agent\NotificationDraftService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentDraftController extends Controller
{
    public function index()
    {
        $tenantId = Auth::user()->cliente_id;
        
        $drafts = AgentNotificationDraft::where('cliente_id', $tenantId)
            ->with('clienteFinal') // Para mostrar o nome do cliente recebedor
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('customer.agent-drafts.index', compact('drafts'));
    }

    public function approve(AgentNotificationDraft $draft, NotificationDraftService $service)
    {
        $this->authorize('approve', $draft);
        
        if ($service->approveAndSend($draft->id, Auth::id())) {
            return back()->with('success', 'Notificação aprovada e colocada na fila de envio!');
        }
        
        return back()->with('error', 'Falha ao aprovar a notificação. Tente novamente.');
    }

    public function discard(AgentNotificationDraft $draft, NotificationDraftService $service)
    {
        $this->authorize('discard', $draft);
        
        $service->discard($draft->id);
        
        return back()->with('success', 'Rascunho descartado com sucesso.');
    }
}
