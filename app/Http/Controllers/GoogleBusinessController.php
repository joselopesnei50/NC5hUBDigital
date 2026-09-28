<?php

namespace App\Http\Controllers;

use App\Agent\Contracts\LlmDriver;
use App\Agent\DTOs\PromptPayload;
use App\Services\GoogleBusinessProfileService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GoogleBusinessController extends Controller
{
    protected GoogleBusinessProfileService $googleService;

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

    protected function cacheKey($cliente, string $suffix): string
    {
        return "gbp:{$cliente->id}:{$suffix}";
    }

    public function index(Request $request)
    {
        $cliente = $this->requireCliente();
        if (!$cliente instanceof \App\Models\Cliente) return $cliente;

        // Limpar caches quando o cliente pedir refresh explícito
        if ($request->boolean('refresh')) {
            foreach (['locations', 'metricas', 'posts', 'reviews'] as $s) {
                Cache::forget($this->cacheKey($cliente, $s));
            }
        }

        $isConnected = !empty($cliente->google_refresh_token);
        $locations   = [];
        $selected    = null;
        $metricas    = null;
        $posts       = [];
        $reviews     = ['media' => 0, 'total' => 0, 'reviews' => []];
        $erros       = [];

        if (!$isConnected) {
            return view('customer.google-business.index', compact(
                'isConnected', 'locations', 'selected', 'metricas', 'posts', 'reviews', 'erros', 'cliente'
            ));
        }

        // 1) Carregar fichas (cache 10 min)
        try {
            $locations = Cache::remember($this->cacheKey($cliente, 'locations'), 600, function () use ($cliente) {
                return $this->googleService->getLocations($cliente);
            });
        } catch (\Throwable $e) {
            $erros['fichas'] = $e->getMessage();
        }

        // 2) Auto-selecionar se só houver 1 ficha; validar a salva
        if (!empty($locations)) {
            $v4Names = array_column($locations, '_v4_name');

            if (empty($cliente->google_location_id) && count($locations) === 1) {
                $cliente->update(['google_location_id' => $locations[0]['_v4_name']]);
            }

            if (!empty($cliente->google_location_id) && !in_array($cliente->google_location_id, $v4Names, true)) {
                // Ficha salva não pertence mais à conta conectada — limpar
                Log::info('[GoogleBusiness] Cliente ' . $cliente->id . ' tinha ficha inexistente, limpando google_location_id.');
                $cliente->update(['google_location_id' => null]);
            }
        }

        // Encontrar a ficha selecionada nos locations
        if (!empty($cliente->google_location_id) && !empty($locations)) {
            foreach ($locations as $loc) {
                if (($loc['_v4_name'] ?? null) === $cliente->google_location_id) {
                    $selected = $loc;
                    break;
                }
            }
        }

        // 3) Se tem ficha selecionada, carregar métricas + posts + reviews (cada um try/catch)
        if ($selected) {
            $v4Name = $selected['_v4_name'];

            try {
                $metricas = Cache::remember($this->cacheKey($cliente, 'metricas'), 3600, function () use ($cliente, $v4Name) {
                    return $this->googleService->getPerformanceComparison($cliente, $v4Name, 30);
                });
            } catch (\Throwable $e) {
                $erros['metricas'] = $e->getMessage();
            }

            try {
                $posts = Cache::remember($this->cacheKey($cliente, 'posts'), 300, function () use ($cliente, $v4Name) {
                    return $this->googleService->listPosts($cliente, $v4Name);
                });
            } catch (\Throwable $e) {
                $erros['posts'] = $e->getMessage();
            }

            try {
                $reviews = Cache::remember($this->cacheKey($cliente, 'reviews'), 300, function () use ($cliente, $v4Name) {
                    return $this->googleService->listReviews($cliente, $v4Name);
                });
            } catch (\Throwable $e) {
                $erros['reviews'] = $e->getMessage();
            }
        }

        return view('customer.google-business.index', compact(
            'isConnected', 'locations', 'selected', 'metricas', 'posts', 'reviews', 'erros', 'cliente'
        ));
    }

    public function redirectToGoogle(Request $request)
    {
        $cliente = $this->requireCliente();
        if (!$cliente instanceof \App\Models\Cliente) return $cliente;

        if (
            empty(\App\Models\Configuracao::get('google_client_id')) ||
            empty(\App\Models\Configuracao::get('google_client_secret')) ||
            empty(\App\Models\Configuracao::get('google_redirect_uri'))
        ) {
            return redirect()->route('customer.google-business.index')
                ->with('error', 'O administrador do sistema ainda não configurou as chaves do Google Meu Negócio.');
        }

        $state = Str::random(40);
        $request->session()->put('gbp_oauth_state', $state);

        $authUrl = $this->googleService->getAuthUrl($state);
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

        $expected = $request->session()->pull('gbp_oauth_state');
        $received = (string) $request->query('state', '');
        if (!$expected || !hash_equals($expected, $received)) {
            Log::warning('[GoogleBusiness] state OAuth inválido no callback do cliente ' . $cliente->id);
            return redirect()->route('customer.google-business.index')
                ->with('error', 'Sessão de autorização inválida ou expirada. Tente reconectar.');
        }

        try {
            $this->googleService->authenticateAndSaveTokens($request->code, $cliente);
            return redirect()->route('customer.google-business.index')
                ->with('success', 'Conta do Google Meu Negócio conectada com sucesso!');
        } catch (\Exception $e) {
            Log::warning('[GoogleBusiness] Falha no callback do cliente ' . $cliente->id . ': ' . $e->getMessage());
            return redirect()->route('customer.google-business.index')
                ->with('error', 'Falha ao conectar sua conta Google. Tente novamente ou fale com o suporte.');
        }
    }

    public function disconnect()
    {
        $cliente = $this->requireCliente();
        if (!$cliente instanceof \App\Models\Cliente) return $cliente;

        $cliente->update([
            'google_access_token'     => null,
            'google_refresh_token'    => null,
            'google_token_expires_at' => null,
            'google_location_id'      => null,
        ]);

        foreach (['locations', 'metricas', 'posts', 'reviews'] as $s) {
            Cache::forget($this->cacheKey($cliente, $s));
        }

        return redirect()->route('customer.google-business.index')
            ->with('success', 'Conta desconectada com sucesso.');
    }

    public function selectLocation(Request $request)
    {
        $cliente = $this->requireCliente();
        if (!$cliente instanceof \App\Models\Cliente) return $cliente;

        $validated = $request->validate([
            'v4_name' => 'required|string',
        ], [
            'v4_name.required' => 'Selecione uma ficha antes de continuar.',
        ]);

        try {
            $locations = Cache::remember($this->cacheKey($cliente, 'locations'), 600, function () use ($cliente) {
                return $this->googleService->getLocations($cliente);
            });
        } catch (\Throwable $e) {
            return redirect()->route('customer.google-business.index')
                ->with('error', 'Não conseguimos validar sua ficha agora: ' . $e->getMessage());
        }

        $v4Names = array_column($locations, '_v4_name');
        if (!in_array($validated['v4_name'], $v4Names, true)) {
            return redirect()->route('customer.google-business.index')
                ->with('error', 'A ficha escolhida não está mais disponível na sua conta Google.');
        }

        $cliente->update(['google_location_id' => $validated['v4_name']]);

        // Trocar de ficha invalida as métricas/posts/reviews da anterior
        foreach (['metricas', 'posts', 'reviews'] as $s) {
            Cache::forget($this->cacheKey($cliente, $s));
        }

        return redirect()->route('customer.google-business.index')
            ->with('success', 'Ficha selecionada.');
    }

    public function storePost(Request $request)
    {
        $cliente = $this->requireCliente();
        if (!$cliente instanceof \App\Models\Cliente) return $cliente;

        if (empty($cliente->google_location_id)) {
            return redirect()->route('customer.google-business.index')
                ->with('error', 'Selecione uma ficha antes de publicar.');
        }

        $validated = $request->validate([
            'summary'  => 'required|string|max:1500',
            'cta_type' => 'nullable|in:LEARN_MORE,BOOK,ORDER,SHOP,SIGN_UP,CALL',
            'cta_url'  => 'nullable|url|required_if:cta_type,LEARN_MORE|required_if:cta_type,BOOK|required_if:cta_type,ORDER|required_if:cta_type,SHOP|required_if:cta_type,SIGN_UP',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png|max:5120|dimensions:min_width=250,min_height=250',
        ], [
            'summary.required'    => 'Escreva o conteúdo da publicação.',
            'summary.max'         => 'A publicação não pode passar de 1500 caracteres.',
            'cta_type.in'         => 'Tipo de botão inválido.',
            'cta_url.url'         => 'Informe uma URL válida para o botão.',
            'cta_url.required_if' => 'Este tipo de botão exige uma URL de destino.',
            'image.image'         => 'O arquivo enviado precisa ser uma imagem.',
            'image.mimes'         => 'Use JPG ou PNG.',
            'image.max'           => 'A imagem não pode passar de 5 MB.',
            'image.dimensions'    => 'A imagem precisa ter no mínimo 250×250 pixels (exigência do Google).',
        ]);

        // Upload: o Google precisa baixar por HTTPS público, então salva em
        // storage/app/public/gbp-posts/ (exige `php artisan storage:link`).
        $imageUrl = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext  = strtolower($file->getClientOriginalExtension() ?: 'jpg');
            $name = 'c' . $cliente->id . '-' . Str::random(20) . '.' . $ext;
            $file->storeAs('gbp-posts', $name, 'public');
            $imageUrl = asset('storage/gbp-posts/' . $name);
        }

        try {
            $this->googleService->createPost(
                $cliente,
                $cliente->google_location_id,
                $validated['summary'],
                [
                    'cta_type'  => $validated['cta_type'] ?? null,
                    'cta_url'   => $validated['cta_url']  ?? null,
                    'image_url' => $imageUrl,
                ]
            );
        } catch (\Throwable $e) {
            // Se falhou publicando no Google, remove o arquivo local pra não deixar orfão
            if ($imageUrl && isset($name)) {
                Storage::disk('public')->delete('gbp-posts/' . $name);
            }
            return redirect()->route('customer.google-business.index')
                ->with('error', 'Não foi possível publicar: ' . $e->getMessage());
        }

        Cache::forget($this->cacheKey($cliente, 'posts'));

        return redirect()->route('customer.google-business.index')
            ->with('success', 'Publicação enviada. Ela pode levar alguns minutos para aparecer.');
    }

    public function destroyPost(Request $request)
    {
        $cliente = $this->requireCliente();
        if (!$cliente instanceof \App\Models\Cliente) return $cliente;

        $validated = $request->validate([
            'post_name' => 'required|string',
        ]);

        // Só aceitar postName que pertence à ficha salva
        $prefix = $cliente->google_location_id . '/localPosts/';
        if (empty($cliente->google_location_id) || !Str::startsWith($validated['post_name'], $prefix)) {
            return redirect()->route('customer.google-business.index')
                ->with('error', 'Publicação inválida.');
        }

        try {
            $this->googleService->deletePost($cliente, $validated['post_name']);
        } catch (\Throwable $e) {
            return redirect()->route('customer.google-business.index')
                ->with('error', 'Não foi possível remover: ' . $e->getMessage());
        }

        Cache::forget($this->cacheKey($cliente, 'posts'));

        return redirect()->route('customer.google-business.index')
            ->with('success', 'Publicação removida.');
    }

    /**
     * Pede pro Bruce (DeepSeek) sugerir uma resposta pra avaliação. Recebe
     * estrelas + comentário + tom e devolve JSON com o rascunho — o cliente
     * ainda precisa clicar "Enviar resposta" pra ir pro Google.
     */
    public function suggestReply(Request $request, LlmDriver $llm)
    {
        $cliente = $this->requireCliente();
        if (!$cliente instanceof \App\Models\Cliente) {
            return response()->json(['error' => 'Cliente não autenticado.'], 401);
        }

        $validated = $request->validate([
            'stars'    => 'required|integer|min:1|max:5',
            'comment'  => 'nullable|string|max:4000',
            'reviewer' => 'nullable|string|max:200',
            'tom'      => 'nullable|in:formal,amistoso',
        ]);

        $stars    = (int) $validated['stars'];
        $comment  = trim((string) ($validated['comment'] ?? ''));
        $reviewer = trim((string) ($validated['reviewer'] ?? 'Cliente'));
        $tom      = $validated['tom'] ?? 'amistoso';

        $tomInstr = $tom === 'formal'
            ? 'Escreva num tom cordial e profissional, sem gírias.'
            : 'Escreva num tom próximo e amistoso, mantendo o profissionalismo.';

        $system = <<<TXT
Você é assistente de atendimento de uma empresa que responde avaliações do Google Meu Negócio em pt-BR.
Regras rígidas:
- No máximo 3 frases curtas (até ~400 caracteres).
- {$tomInstr}
- Se a avaliação for negativa (1-3 estrelas): reconheça o problema com empatia, evite defesa, chame pra continuar a conversa por canal privado.
- Se a avaliação for positiva (4-5 estrelas): agradeça de forma genuína e específica, sem parecer script.
- Nunca prometa reembolso/desconto sem contexto.
- Nunca peça dados sensíveis (CPF, cartão, senha).
- Nunca cite valores, produtos ou promoções que você não sabe.
- Responda APENAS com o texto da resposta, sem prefixo ("Resposta:", aspas, markdown).
TXT;

        $razao = $cliente->razao_social ?? 'nossa empresa';
        $user = "Avaliação de {$reviewer} — {$stars} estrelas.\n"
              . ($comment !== '' ? "Comentário: {$comment}\n" : "(sem comentário)\n")
              . "Empresa: {$razao}\n"
              . "Escreva a resposta.";

        try {
            $response = $llm->complete(new PromptPayload($system, $user));
            $sugestao = trim($response->content);
            // Remove aspas envolventes caso o LLM insista
            $sugestao = trim($sugestao, "\"'");
            if ($sugestao === '') {
                return response()->json(['error' => 'O Bruce não conseguiu gerar uma sugestão agora. Tente de novo.'], 502);
            }
            return response()->json(['sugestao' => $sugestao]);
        } catch (\Throwable $e) {
            Log::warning('[GoogleBusiness] Bruce falhou na sugestao cliente=' . $cliente->id . ' erro=' . $e->getMessage());
            return response()->json(['error' => 'Não foi possível gerar a sugestão agora. Tente de novo em instantes.'], 502);
        }
    }

    public function replyReview(Request $request)
    {
        $cliente = $this->requireCliente();
        if (!$cliente instanceof \App\Models\Cliente) return $cliente;

        $validated = $request->validate([
            'review_name' => 'required|string',
            'comment'     => 'required|string|max:4000',
        ], [
            'comment.required' => 'Escreva uma resposta antes de enviar.',
            'comment.max'      => 'A resposta ficou longa demais (máx. 4000 caracteres).',
        ]);

        $prefix = $cliente->google_location_id . '/reviews/';
        if (empty($cliente->google_location_id) || !Str::startsWith($validated['review_name'], $prefix)) {
            return redirect()->route('customer.google-business.index')
                ->with('error', 'Avaliação inválida.');
        }

        try {
            $this->googleService->replyReview($cliente, $validated['review_name'], $validated['comment']);
        } catch (\Throwable $e) {
            return redirect()->route('customer.google-business.index')
                ->with('error', 'Não foi possível responder: ' . $e->getMessage());
        }

        Cache::forget($this->cacheKey($cliente, 'reviews'));

        return redirect()->route('customer.google-business.index')
            ->with('success', 'Resposta enviada.');
    }

    /**
     * Junta metricas + posts + reviews do cache (ou busca fresco) — usado
     * pelos exports pra evitar duplicar a orquestracao da index.
     */
    protected function loadDadosExport($cliente): array
    {
        if (empty($cliente->google_location_id)) {
            return ['ok' => false, 'msg' => 'Selecione uma ficha antes de exportar.'];
        }

        try {
            $metricas = Cache::remember($this->cacheKey($cliente, 'metricas'), 3600, function () use ($cliente) {
                return $this->googleService->getPerformanceComparison($cliente, $cliente->google_location_id, 30);
            });
            $posts = Cache::remember($this->cacheKey($cliente, 'posts'), 300, function () use ($cliente) {
                return $this->googleService->listPosts($cliente, $cliente->google_location_id);
            });
            $reviews = Cache::remember($this->cacheKey($cliente, 'reviews'), 300, function () use ($cliente) {
                return $this->googleService->listReviews($cliente, $cliente->google_location_id);
            });
        } catch (\Throwable $e) {
            return ['ok' => false, 'msg' => $e->getMessage()];
        }

        return [
            'ok'       => true,
            'metricas' => $metricas,
            'posts'    => $posts,
            'reviews'  => $reviews,
            'gerado_em'=> now(),
        ];
    }

    public function exportCsv()
    {
        $cliente = $this->requireCliente();
        if (!$cliente instanceof \App\Models\Cliente) return $cliente;

        $data = $this->loadDadosExport($cliente);
        if (!$data['ok']) {
            return redirect()->route('customer.google-business.index')
                ->with('error', 'Não foi possível exportar: ' . $data['msg']);
        }

        $filename = 'gmn-' . $cliente->id . '-' . now()->format('Y-m-d') . '.csv';

        return new StreamedResponse(function () use ($data, $cliente) {
            $out = fopen('php://output', 'w');
            // BOM UTF-8 pra Excel abrir com acentos corretos
            fwrite($out, "\xEF\xBB\xBF");

            $atual = $data['metricas']['atual'] ?? [];
            $anterior = $data['metricas']['anterior'] ?? [];
            $delta = $data['metricas']['delta'] ?? [];

            fputcsv($out, ['Google Meu Negócio — Relatório de desempenho']);
            fputcsv($out, ['Empresa', $cliente->razao_social ?? '—']);
            fputcsv($out, ['Gerado em', $data['gerado_em']->format('d/m/Y H:i')]);
            if (!empty($atual['periodo'])) {
                fputcsv($out, ['Período', $atual['periodo']['inicio'] . ' a ' . $atual['periodo']['fim']]);
            }
            fputcsv($out, []);

            fputcsv($out, ['Métrica', 'Atual (30d)', 'Anterior (30d)', 'Variação %']);
            $formatDelta = fn ($d) => $d === null ? 'novo' : (number_format($d, 1, ',', '.') . '%');
            fputcsv($out, ['Visualizações',    $atual['impressoes'] ?? 0,   $anterior['impressoes'] ?? 0,   $formatDelta($delta['impressoes'] ?? 0)]);
            fputcsv($out, ['Cliques no site',  $atual['cliques_site'] ?? 0, $anterior['cliques_site'] ?? 0, $formatDelta($delta['cliques_site'] ?? 0)]);
            fputcsv($out, ['Ligações',         $atual['ligacoes'] ?? 0,     $anterior['ligacoes'] ?? 0,     $formatDelta($delta['ligacoes'] ?? 0)]);
            fputcsv($out, ['Pedidos de rota',  $atual['rotas'] ?? 0,        $anterior['rotas'] ?? 0,        $formatDelta($delta['rotas'] ?? 0)]);
            fputcsv($out, []);
            fputcsv($out, ['Distribuição', 'Total']);
            fputcsv($out, ['Impressões via Busca', $atual['impressoes_busca'] ?? 0]);
            fputcsv($out, ['Impressões via Maps',  $atual['impressoes_maps'] ?? 0]);
            fputcsv($out, ['Impressões Mobile',    $atual['impressoes_mobile'] ?? 0]);
            fputcsv($out, ['Impressões Desktop',   $atual['impressoes_desktop'] ?? 0]);
            fputcsv($out, []);

            fputcsv($out, ['Publicações recentes']);
            fputcsv($out, ['Data', 'Status', 'Resumo', 'Link']);
            foreach (($data['posts'] ?? []) as $p) {
                fputcsv($out, [
                    !empty($p['createTime']) ? \Carbon\Carbon::parse($p['createTime'])->format('d/m/Y H:i') : '',
                    $p['state'] ?? '',
                    Str::limit((string) ($p['summary'] ?? ''), 200),
                    $p['searchUrl'] ?? '',
                ]);
            }
            fputcsv($out, []);

            fputcsv($out, ['Avaliações']);
            fputcsv($out, ['Média', 'Total']);
            fputcsv($out, [number_format((float) ($data['reviews']['media'] ?? 0), 1, ',', '.'), $data['reviews']['total'] ?? 0]);
            fputcsv($out, []);
            fputcsv($out, ['Data', 'Estrelas', 'Autor', 'Comentário', 'Sua resposta']);
            foreach (($data['reviews']['reviews'] ?? []) as $r) {
                fputcsv($out, [
                    !empty($r['updateTime']) ? \Carbon\Carbon::parse($r['updateTime'])->format('d/m/Y') : '',
                    \App\Services\GoogleBusinessProfileService::estrelas($r['starRating'] ?? null),
                    $r['reviewer']['displayName'] ?? '',
                    $r['comment'] ?? '',
                    $r['reviewReply']['comment'] ?? '',
                ]);
            }

            fclose($out);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-store, no-cache',
        ]);
    }

    public function exportPdf()
    {
        $cliente = $this->requireCliente();
        if (!$cliente instanceof \App\Models\Cliente) return $cliente;

        $data = $this->loadDadosExport($cliente);
        if (!$data['ok']) {
            return redirect()->route('customer.google-business.index')
                ->with('error', 'Não foi possível exportar: ' . $data['msg']);
        }

        $pdf = Pdf::loadView('customer.google-business.pdf', [
            'cliente'  => $cliente,
            'metricas' => $data['metricas'],
            'posts'    => $data['posts'],
            'reviews'  => $data['reviews'],
            'geradoEm' => $data['gerado_em'],
        ])->setPaper('a4');

        $filename = 'gmn-' . $cliente->id . '-' . now()->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }
}
