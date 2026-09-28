<?php

namespace App\Http\Controllers;

use App\Services\GoogleBusinessProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
}
