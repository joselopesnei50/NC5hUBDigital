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
            return redirect()->route('configuracoes.index')->with('error', 'Por favor, configure a chave da API do Brevo primeiro.');
        }

        $totalLeads = Lead::whereNotNull('email')->count();

        return view('admin.campanhas.create', compact('totalLeads'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string',
            'assunto' => 'required|string',
            'audience' => 'required|in:leads_ia,csv',
            'csv_file' => 'required_if:audience,csv|file|mimes:csv,txt',
            'html_content' => 'required|string',
        ]);

        $apiKey = $this->getBrevoKey();
        $senderEmail = Configuracao::get('mail_from_address', 'contato@nc5.com.br');
        $senderName = Configuracao::get('mail_from_name', 'NC5 Hub');

        // Passo 1: Obter/Criar Lista no Brevo
        $listId = null;
        if ($request->audience === 'leads_ia') {
            $listId = $this->syncLeadsToBrevo($apiKey);
        } else {
            $listId = $this->syncCsvToBrevo($apiKey, $request->file('csv_file'), $request->nome);
        }

        if (!$listId) {
            return back()->with('error', 'Falha ao sincronizar contatos com o Brevo.');
        }

        // Passo 2: Criar Campanha no Brevo (agora usando htmlContent em vez de templateId)
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
                'htmlContent' => $request->html_content,
            ]);

            if ($response->successful()) {
                $campaignId = $response->json()['id'];

                // Passo 3: Enviar Campanha Agora
                Http::withHeaders([
                    'api-key' => $apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])->post("https://api.brevo.com/v3/emailCampaigns/{$campaignId}/sendNow");

                Campanha::create([
                    'nome' => $request->nome,
                    'assunto' => $request->assunto,
                    'brevo_campaign_id' => $campaignId,
                    'audience' => $request->audience,
                    'status' => 'enviando',
                    'sent_at' => now(),
                ]);

                return redirect()->route('campanhas.index')->with('success', 'Campanha criada e disparada com sucesso!');
            }

            $erroMsg = $response->json()['message'] ?? 'Erro desconhecido na API do Brevo';
            return back()->with('error', 'Erro ao criar campanha no Brevo: ' . $erroMsg)->withInput();

        } catch (\Exception $e) {
            Log::error("Exceção na criação de campanha Brevo: " . $e->getMessage());
            return back()->with('error', 'Erro de conexão com o Brevo.')->withInput();
        }
    }

    private function getOrCreateList($apiKey, $listName)
    {
        $listId = null;
        try {
            $listsRes = Http::withHeaders(['api-key' => $apiKey])->get('https://api.brevo.com/v3/contacts/lists');
            if ($listsRes->successful()) {
                $lists = $listsRes->json()['lists'] ?? [];
                foreach ($lists as $l) {
                    if ($l['name'] === $listName) {
                        $listId = $l['id'];
                        break;
                    }
                }
            }
        } catch (\Exception $e) {}

        if (!$listId) {
            try {
                $foldersRes = Http::withHeaders(['api-key' => $apiKey])->get('https://api.brevo.com/v3/contacts/folders');
                $folderId = 1;
                if ($foldersRes->successful() && !empty($foldersRes->json()['folders'])) {
                    $folderId = $foldersRes->json()['folders'][0]['id'];
                }

                $createListRes = Http::withHeaders(['api-key' => $apiKey])->post('https://api.brevo.com/v3/contacts/lists', [
                    'name' => $listName,
                    'folderId' => $folderId
                ]);
                if ($createListRes->successful()) {
                    $listId = $createListRes->json()['id'];
                }
            } catch (\Exception $e) {}
        }

        return $listId;
    }

    private function syncLeadsToBrevo($apiKey)
    {
        $leads = Lead::whereNotNull('email')->get();
        if ($leads->isEmpty()) return false;

        $listId = $this->getOrCreateList($apiKey, 'NC5 Hub - Leads IA');
        if (!$listId) return false;

        $csvData = "EMAIL,NOME\n";
        foreach ($leads as $lead) {
            $csvData .= "{$lead->email},{$lead->nome}\n";
        }

        $this->importCsvToBrevoList($apiKey, $listId, $csvData);
        return $listId;
    }

    private function syncCsvToBrevo($apiKey, $file, $campaignName)
    {
        $csvData = file_get_contents($file->getRealPath());
        
        $listName = 'Import - ' . $campaignName . ' - ' . date('Y-m-d');
        $listId = $this->getOrCreateList($apiKey, $listName);
        
        if (!$listId) return false;

        $this->importCsvToBrevoList($apiKey, $listId, $csvData);
        return $listId;
    }

    private function importCsvToBrevoList($apiKey, $listId, $csvData)
    {
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
        } catch (\Exception $e) {
            Log::error("Erro importando contatos para o brevo: " . $e->getMessage());
        }
    }
}
