<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campanha;
use App\Models\Configuracao;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CampanhaController extends Controller
{
    private function getBrevoKey()
    {
        return Configuracao::get('brevo_api_key');
    }

    public function index()
    {
        $apiKey = $this->getBrevoKey();
        $campanhas = Campanha::latest()->get();
        $brevoConectado = !empty($apiKey);

        // Atualizar métricas se tiver ID
        if ($brevoConectado) {
            foreach ($campanhas as $campanha) {
                if ($campanha->brevo_campaign_id) {
                    try {
                        $response = Http::withHeaders([
                            'api-key' => $apiKey,
                            'Accept' => 'application/json'
                        ])->timeout(5)->get('https://api.brevo.com/v3/emailCampaigns/' . $campanha->brevo_campaign_id);

                        if ($response->successful()) {
                            $data = $response->json();
                            $stats = $data['statistics']['globalStats'] ?? [];
                            $campanha->metrics = [
                                'sent' => $stats['sent'] ?? 0,
                                'delivered' => $stats['delivered'] ?? 0,
                                'uniqueViews' => $stats['uniqueViews'] ?? 0,
                                'uniqueClicks' => $stats['uniqueClicks'] ?? 0,
                                'bounces' => ($stats['softBounces'] ?? 0) + ($stats['hardBounces'] ?? 0),
                            ];
                            $campanha->status = $data['status'] ?? $campanha->status;
                            $campanha->save();
                        }
                    } catch (\Exception $e) {
                        Log::warning("Erro ao buscar métricas da campanha " . $campanha->id . ": " . $e->getMessage());
                    }
                }
            }
        }

        return view('admin.campanhas.index', compact('campanhas', 'brevoConectado'));
    }

    public function create()
    {
        $apiKey = $this->getBrevoKey();
        if (empty($apiKey)) {
            return redirect()->route('admin.configuracoes.index')->with('error', 'Por favor, configure a chave da API do Brevo primeiro.');
        }

        // Buscar templates do Brevo para o usuário escolher
        $templates = [];
        try {
            $response = Http::withHeaders([
                'api-key' => $apiKey,
                'Accept' => 'application/json'
            ])->get('https://api.brevo.com/v3/smtp/templates?limit=50&status=true');
            
            if ($response->successful()) {
                $templates = $response->json()['templates'] ?? [];
            }
        } catch (\Exception $e) {
            Log::warning("Erro ao buscar templates do Brevo: " . $e->getMessage());
        }

        $totalLeads = Lead::count();

        return view('admin.campanhas.create', compact('templates', 'totalLeads'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string',
            'assunto' => 'required|string',
            'template_id' => 'required|numeric',
        ]);

        $apiKey = $this->getBrevoKey();
        $senderEmail = Configuracao::get('mail_from_address', 'contato@nc5.com.br');
        $senderName = Configuracao::get('mail_from_name', 'NC5 Hub');

        // Passo 1: Obter/Criar Lista no Brevo para os Leads
        $listId = $this->syncLeadsToBrevo($apiKey);
        if (!$listId) {
            return back()->with('error', 'Falha ao sincronizar leads com o Brevo.');
        }

        // Passo 2: Criar Campanha no Brevo
        try {
            $response = Http::withHeaders([
                'api-key' => $apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->post('https://api.brevo.com/v3/emailCampaigns', [
                'name' => $request->nome,
                'subject' => $request->assunto,
                'sender' => ['name' => $senderName, 'email' => $senderEmail],
                'type' => 'classic',
                'recipients' => ['listIds' => [$listId]],
                'templateId' => (int) $request->template_id,
            ]);

            if ($response->successful()) {
                $campaignId = $response->json()['id'];

                // Passo 3: Enviar Campanha Agora
                Http::withHeaders([
                    'api-key' => $apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])->post("https://api.brevo.com/v3/emailCampaigns/{$campaignId}/sendNow");

                // Salvar no nosso banco
                Campanha::create([
                    'nome' => $request->nome,
                    'assunto' => $request->assunto,
                    'brevo_campaign_id' => $campaignId,
                    'audience' => 'leads_ia',
                    'status' => 'enviando',
                    'sent_at' => now(),
                ]);

                return redirect()->route('admin.campanhas.index')->with('success', 'Campanha criada e disparada com sucesso!');
            }

            $erroMsg = $response->json()['message'] ?? 'Erro desconhecido na API do Brevo';
            return back()->with('error', 'Erro ao criar campanha no Brevo: ' . $erroMsg);

        } catch (\Exception $e) {
            Log::error("Exceção na criação de campanha Brevo: " . $e->getMessage());
            return back()->with('error', 'Erro de conexão com o Brevo.');
        }
    }

    private function syncLeadsToBrevo($apiKey)
    {
        // Pega todos os leads
        $leads = Lead::whereNotNull('email')->get();
        if ($leads->isEmpty()) {
            return false;
        }

        // 1. Tentar achar a lista "NC5 Hub - Leads IA"
        $listId = null;
        try {
            $listsRes = Http::withHeaders(['api-key' => $apiKey])->get('https://api.brevo.com/v3/contacts/lists');
            if ($listsRes->successful()) {
                $lists = $listsRes->json()['lists'] ?? [];
                foreach ($lists as $l) {
                    if ($l['name'] === 'NC5 Hub - Leads IA') {
                        $listId = $l['id'];
                        break;
                    }
                }
            }
        } catch (\Exception $e) {}

        // 2. Se não achou, criar a lista (precisa de um folderId, que por padrão é 1 no brevo, mas vamos pegar o primeiro)
        if (!$listId) {
            try {
                $foldersRes = Http::withHeaders(['api-key' => $apiKey])->get('https://api.brevo.com/v3/contacts/folders');
                $folderId = 1;
                if ($foldersRes->successful() && !empty($foldersRes->json()['folders'])) {
                    $folderId = $foldersRes->json()['folders'][0]['id'];
                }

                $createListRes = Http::withHeaders(['api-key' => $apiKey])->post('https://api.brevo.com/v3/contacts/lists', [
                    'name' => 'NC5 Hub - Leads IA',
                    'folderId' => $folderId
                ]);
                if ($createListRes->successful()) {
                    $listId = $createListRes->json()['id'];
                }
            } catch (\Exception $e) {}
        }

        if (!$listId) return false;

        // 3. Montar CSV
        $csvData = "EMAIL,NOME\n";
        foreach ($leads as $lead) {
            $csvData .= "{$lead->email},{$lead->nome}\n";
        }

        // 4. Importar para a lista
        try {
            Http::withHeaders([
                'api-key' => $apiKey,
                'Content-Type' => 'application/json'
            ])->post('https://api.brevo.com/v3/contacts/import', [
                'fileBody' => $csvData,
                'listIds' => [$listId],
                'emailBlacklist' => false,
                'smsBlacklist' => false,
                'updateExistingContacts' => true,
                'emptyContactsAttributes' => false
            ]);
        } catch (\Exception $e) {}

        return $listId;
    }
}
