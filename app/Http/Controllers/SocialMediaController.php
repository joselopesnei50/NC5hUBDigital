<?php

namespace App\Http\Controllers;

use App\Agent\DTOs\PromptPayload;
use App\Agent\LlmManager;
use App\Models\AgentKnowledge;
use App\Models\Cliente;
use App\Models\FacebookDataDeletionRequest;
use App\Models\ProdutoServico;
use App\Models\ScheduledPost;
use App\Models\SocialAccount;
use App\Models\WhatsappBotTraining;
use App\Services\MetaSocialAuthService;
use App\Services\MetaSocialPublisherService;
use App\Services\SystemCliente;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SocialMediaController extends Controller
{
    public function __construct(
        protected MetaSocialAuthService $auth,
        protected MetaSocialPublisherService $publisher,
    ) {}

    protected function isAdminContext(): bool
    {
        if (request()->routeIs('admin.hub.social.*')) {
            return true;
        }

        return Auth::check() && Auth::user()->role === 'admin';
    }

    protected function routePrefix(): string
    {
        return $this->isAdminContext() ? 'admin.hub.social.' : 'customer.social.';
    }

    protected function indexRoute(): string
    {
        return $this->routePrefix() . 'index';
    }

    protected function requireCliente()
    {
        if ($this->isAdminContext()) {
            return SystemCliente::get();
        }

        $cliente = Auth::user()->cliente ?? null;
        if (!$cliente) {
            return redirect()->route('customer.index')
                ->with('error', 'Sua conta ainda não está vinculada a um cadastro empresarial.');
        }

        return $cliente;
    }

    public function index(Request $request)
    {
        $cliente = $this->requireCliente();
        if (!$cliente instanceof Cliente) {
            return $cliente;
        }

        $filter = in_array($request->query('status'), ['scheduled', 'published', 'failed', 'draft'], true)
            ? $request->query('status')
            : null;

        $accounts = SocialAccount::where('cliente_id', $cliente->id)
            ->orderByDesc('is_active')
            ->orderByDesc('created_at')
            ->get();

        $activeAccounts = $accounts->where('is_active', true)->values();

        $posts = ScheduledPost::where('cliente_id', $cliente->id)
            ->with('account')
            ->when($filter, fn ($q) => $q->where('status', $filter))
            ->orderByDesc('scheduled_at')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $counts = ScheduledPost::where('cliente_id', $cliente->id)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
        $counts['all'] = array_sum($counts);

        $metaConfigured = $this->auth->isConfigured();
        $routePrefix    = $this->routePrefix();
        $viewName       = $this->isAdminContext() ? 'admin.hub.social.index' : 'customer.social.index';

        return view($viewName, compact(
            'cliente',
            'accounts',
            'activeAccounts',
            'posts',
            'counts',
            'filter',
            'metaConfigured',
            'routePrefix'
        ));
    }

    public function connect(Request $request)
    {
        if ($request->has('code') || $request->has('error') || $request->has('error_code')) {
            return $this->callback($request);
        }

        if (!$this->auth->isConfigured()) {
            return back()->with('error', 'As credenciais do Meta App ainda não foram configuradas em Configurações Globais.');
        }

        $context = $this->isAdminContext() ? 'admin' : 'customer';

        return redirect($this->auth->getAuthUrl($context));
    }

    /**
     * Callback OAuth unificado para Facebook & Instagram Business.
     */
    public function callback(Request $request)
    {
        $state   = (string) $request->query('state', '');
        $isAdmin = str_ends_with($state, '|admin') || $this->isAdminContext();
        $targetRoute = $isAdmin ? 'admin.hub.social.index' : 'customer.social.index';

        if ($request->has('error') || $request->has('error_code')) {
            $errDesc = $request->query('error_description') ?: $request->query('error_message') ?: 'Autorização cancelada ou negada no Facebook.';
            return redirect()->route($targetRoute)
                ->with('error', $errDesc);
        }

        $code = (string) $request->query('code', '');
        if ($code === '') {
            return redirect()->route($targetRoute)
                ->with('error', 'Código de autorização ausente no retorno da Meta.');
        }

        $cliente = $isAdmin ? SystemCliente::get() : (Auth::user()->cliente ?? null);
        if (!$cliente) {
            return redirect()->route('dashboard')->with('error', 'Cliente não identificado para vincular conta social.');
        }

        $shortToken = $this->auth->exchangeCodeForToken($code, $request->url());
        if (!$shortToken) {
            return redirect()->route($targetRoute)
                ->with('error', 'Falha ao obter token de acesso do Facebook. Verifique a Redirect URI no painel Meta.');
        }

        $longToken = $this->auth->getLongLivedToken($shortToken) ?? $shortToken;
        $pages     = $this->auth->getUserPages($longToken);

        if (empty($pages)) {
            return redirect()->route($targetRoute)
                ->with('error', 'Nenhuma Página do Facebook encontrada. Certifique-se de selecionar as páginas durante a autorização.');
        }

        $saved = $this->auth->saveAccounts($pages, (int) $cliente->id);

        return redirect()->route($targetRoute)
            ->with('success', "{$saved} página(s) do Facebook/Instagram conectada(s) com sucesso!");
    }

    public function disconnectAccount(SocialAccount $account)
    {
        $cliente = $this->requireCliente();
        if (!$cliente instanceof Cliente) {
            return $cliente;
        }

        abort_unless((int) $account->cliente_id === (int) $cliente->id, 403);

        $account->update(['is_active' => false, 'access_token' => '']);

        return back()->with('success', "Conta \"{$account->page_name}\" desconectada.");
    }

    public function destroyAccount(SocialAccount $account)
    {
        $cliente = $this->requireCliente();
        if (!$cliente instanceof Cliente) {
            return $cliente;
        }

        abort_unless((int) $account->cliente_id === (int) $cliente->id, 403);

        $account->delete();

        return back()->with('success', 'Conta removida com sucesso.');
    }

    public function storePost(Request $request)
    {
        $cliente = $this->requireCliente();
        if (!$cliente instanceof Cliente) {
            return $cliente;
        }

        $publishNow = $request->boolean('publish_now');

        if (!$publishNow && $request->filled('scheduled_at')) {
            try {
                $request->merge([
                    'scheduled_at' => Carbon::parse($request->input('scheduled_at'), 'America/Sao_Paulo')
                        ->format('Y-m-d H:i:s'),
                ]);
            } catch (\Throwable) {
            }
        }

        $data = $request->validate([
            'social_account_id'  => 'nullable|integer',
            'platform'           => 'required|in:facebook,instagram,both',
            'format'             => 'nullable|in:feed,story',
            'caption'            => 'required|string|max:2200',
            'scheduled_at'       => $publishNow ? 'nullable|date' : 'required|date|after:now',
            'media'              => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4|max:51200',
            'media_files'        => 'nullable|array|max:10',
            'media_files.*'      => 'file|mimes:jpg,jpeg,png,gif,webp,mp4|max:51200',
            'media_url_external' => 'nullable|url|max:2048',
            'publish_now'        => 'nullable|boolean',
        ]);

        $format = $data['format'] ?? 'feed';
        if ($format === 'story' && $data['platform'] === 'facebook') {
            $data['platform'] = 'instagram';
        }

        $account = null;
        if (!empty($data['social_account_id'])) {
            $account = SocialAccount::where('cliente_id', $cliente->id)
                ->where('id', $data['social_account_id'])
                ->firstOrFail();
        }

        $mediaUrl   = null;
        $mediaType  = 'none';
        $mediaItems = null;

        if ($request->hasFile('media_files')) {
            $items = [];
            foreach ($request->file('media_files') as $file) {
                if (!$file || !$file->isValid()) {
                    continue;
                }
                $path = $file->store("clientes/{$cliente->id}/social-media", 'public');
                $type = str_starts_with((string) $file->getMimeType(), 'video') ? 'video' : 'image';
                $items[] = [
                    'url'  => url(Storage::url($path)),
                    'type' => $type,
                ];
            }
            if (!empty($items)) {
                $mediaItems = $items;
                $mediaUrl   = $items[0]['url'];
                $mediaType  = $items[0]['type'];
            }
        } elseif ($request->hasFile('media')) {
            $file      = $request->file('media');
            $path      = $file->store("clientes/{$cliente->id}/social-media", 'public');
            $mediaUrl  = url(Storage::url($path));
            $mediaType = str_starts_with((string) $file->getMimeType(), 'video') ? 'video' : 'image';
        } elseif (!empty($data['media_url_external'])) {
            $mediaUrl  = $data['media_url_external'];
            $mediaType = 'image';
        }

        if (in_array($data['platform'], ['instagram', 'both'], true) && !$mediaUrl && empty($mediaItems)) {
            return back()->withInput()->with('error', 'Publicações no Instagram exigem pelo menos uma imagem ou vídeo anexado.');
        }

        $scheduledAt = $publishNow ? now() : $data['scheduled_at'];

        $post = ScheduledPost::create([
            'cliente_id'        => $cliente->id,
            'social_account_id' => $account?->id,
            'user_id'           => Auth::id(),
            'platform'          => $data['platform'],
            'format'            => $format,
            'caption'           => $data['caption'],
            'media_url'         => $mediaUrl,
            'media_items'       => $mediaItems,
            'media_type'        => $mediaType,
            'scheduled_at'      => $scheduledAt,
            'status'            => $account ? 'scheduled' : 'draft',
        ]);

        if ($publishNow && $account) {
            try {
                $ok = $this->publisher->publish($post);
                if ($ok) {
                    return redirect()->route($this->indexRoute())
                        ->with('success', 'Post publicado agora com sucesso no Facebook/Instagram!');
                }
                $post->refresh();
                return redirect()->route($this->indexRoute())
                    ->with('error', 'Falha ao publicar agora: ' . ($post->error_message ?: 'verifique as permissões da conta.'));
            } catch (\Throwable $e) {
                Log::error('SocialMedia publish_now failed', [
                    'post_id' => $post->id,
                    'error'   => $e->getMessage(),
                ]);
                return redirect()->route($this->indexRoute())
                    ->with('error', 'Erro ao publicar: ' . $e->getMessage());
            }
        }

        return redirect()->route($this->indexRoute())
            ->with('success', $account ? 'Post agendado com sucesso no calendário!' : 'Rascunho salvo com sucesso!');
    }

    public function retryPost(ScheduledPost $post)
    {
        $cliente = $this->requireCliente();
        if (!$cliente instanceof Cliente) {
            return $cliente;
        }

        abort_unless((int) $post->cliente_id === (int) $cliente->id, 403);

        if (!$post->account || !$post->account->is_active) {
            return back()->with('error', 'Vincule uma conta social ativa antes de publicar.');
        }

        $ok = $this->publisher->publish($post);
        if ($ok) {
            return back()->with('success', 'Post publicado com sucesso!');
        }

        $post->refresh();
        return back()->with('error', 'Falha ao publicar: ' . ($post->error_message ?: 'erro na Graph API.'));
    }

    public function destroyPost(ScheduledPost $post)
    {
        $cliente = $this->requireCliente();
        if (!$cliente instanceof Cliente) {
            return $cliente;
        }

        abort_unless((int) $post->cliente_id === (int) $cliente->id, 403);

        $post->delete();

        return back()->with('success', 'Post removido do histórico.');
    }

    /**
     * Gera legenda estratégica com a BruceIA usando o DNA do Negócio (Memória Unificada BruceOS).
     */
    public function generateCaption(Request $request, LlmManager $llm)
    {
        $cliente = $this->requireCliente();
        if (!$cliente instanceof Cliente) {
            return response()->json(['error' => 'Cliente inválido.'], 403);
        }

        $data = $request->validate([
            'topic'    => 'required|string|max:500',
            'platform' => 'required|in:facebook,instagram,both',
            'format'   => 'nullable|in:feed,story',
        ]);

        $training = WhatsappBotTraining::where('cliente_id', $cliente->id)->first();
        $produtos = ProdutoServico::where('cliente_id', $cliente->id)
            ->limit(15)
            ->get(['nome', 'tipo', 'preco_padrao', 'descricao']);
        $docs = AgentKnowledge::where('cliente_id', $cliente->id)
            ->where('ativo', true)
            ->limit(8)
            ->get(['titulo', 'conteudo']);

        $nomeEmpresa = $training?->business_name ?: ($cliente->nome_fantasia ?: $cliente->razao_social);
        $tomVoz      = $training?->tom_de_voz ?: ($cliente->tom_voz ?: 'Profissional, próximo e focado em conversão');
        $publico     = $training?->publico_alvo ?: ($cliente->publico_alvo ?: '');
        $diferenciais= $training?->diferenciais ?: ($cliente->diferenciais_competitivos ?: '');

        $contextLines = [
            "Empresa: {$nomeEmpresa}",
            "Tom de voz: {$tomVoz}",
        ];
        if ($publico) {
            $contextLines[] = "Público-alvo: {$publico}";
        }
        if ($diferenciais) {
            $contextLines[] = "Diferenciais: {$diferenciais}";
        }
        if ($produtos->isNotEmpty()) {
            $contextLines[] = "Principais produtos/serviços: " . $produtos->pluck('nome')->implode(', ');
        }
        if ($docs->isNotEmpty()) {
            foreach ($docs as $d) {
                $contextLines[] = "Base de conhecimento ({$d->titulo}): " . Str::limit($d->conteudo, 200);
            }
        }

        $systemPrompt = "Você é a BruceIA, estrategista de conteúdo e copywriter de redes sociais da {$nomeEmpresa}.\n"
            . "Use os dados reais da empresa abaixo para escrever uma legenda autêntica, persuasiva e pronta para publicar.\n\n"
            . implode("\n", $contextLines);

        $canal = match ($data['platform']) {
            'instagram' => 'Instagram',
            'facebook'  => 'Facebook',
            default     => 'Instagram e Facebook',
        };

        $userPrompt = "Crie uma legenda altamente engajadora para {$canal} sobre o seguinte tema/oferta:\n"
            . "\"{$data['topic']}\"\n\n"
            . "Regras:\n"
            . "- Comece com um gancho forte na primeira linha.\n"
            . "- Desenvolva o benefício de forma escaneável (parágrafos curtos).\n"
            . "- Inclua uma Chamada para Ação (CTA) clara no final.\n"
            . "- Adicione de 4 a 6 hashtags estratégicas ao final.\n"
            . "- Retorne APENAS o texto final da legenda, sem comentários extras.";

        try {
            $response = $llm->driver('deepseek')->complete(new PromptPayload(
                systemPrompt: $systemPrompt,
                userPrompt:   $userPrompt,
            ));

            $caption = trim($response->content);
            if ($caption === '') {
                return response()->json(['error' => 'A BruceIA não retornou texto. Tente novamente.'], 422);
            }

            return response()->json([
                'ok'      => true,
                'caption' => $caption,
            ]);
        } catch (\Throwable $e) {
            Log::error('BruceIA Social Caption Error', ['msg' => $e->getMessage()]);
            return response()->json([
                'error' => 'Não foi possível gerar a legenda com a BruceIA agora: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Webhook público Meta: Deauthorize Callback
     */
    public function handleDeauthorize(Request $request): Response
    {
        $data = $this->auth->parseSignedRequest($request->input('signed_request'));
        if ($data !== null) {
            Log::info('Facebook deauthorize webhook recebido', [
                'facebook_user_id' => $data['user_id'] ?? null,
            ]);
        }
        return response('', 200);
    }

    /**
     * Webhook público Meta: Data Deletion Callback (LGPD / GDPR)
     */
    public function handleDataDeletion(Request $request): JsonResponse
    {
        $data = $this->auth->parseSignedRequest($request->input('signed_request'));
        $code = Str::random(40);

        FacebookDataDeletionRequest::create([
            'facebook_user_id'  => $data['user_id'] ?? null,
            'confirmation_code' => $code,
            'status'            => $data === null ? 'rejected' : 'received',
            'notes'             => $data === null
                ? 'signed_request inválido; IP ' . $request->ip()
                : 'Solicitação via Facebook em ' . now()->toIso8601String(),
        ]);

        return response()->json([
            'url'               => route('social.facebook.deletion-status', ['code' => $code]),
            'confirmation_code' => $code,
        ]);
    }

    public function dataDeletionStatus(string $code): JsonResponse
    {
        $req = FacebookDataDeletionRequest::where('confirmation_code', $code)->firstOrFail();

        return response()->json([
            'confirmation_code' => $req->confirmation_code,
            'status'            => $req->status,
            'requested_at'      => $req->created_at?->toIso8601String(),
        ]);
    }
}
