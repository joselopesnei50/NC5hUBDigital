<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AgentKnowledge;
use App\Models\Cliente;
use App\Services\BusinessGrowthEngineService;
use App\Services\GoogleBusinessProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class KnowledgeController extends Controller
{
    public function __construct(
        protected BusinessGrowthEngineService $growthEngine,
        protected GoogleBusinessProfileService $googleService
    ) {}

    protected function requireCliente(): Cliente
    {
        $cliente = Auth::user()->cliente ?? null;
        if (!$cliente) {
            abort(403, 'Sua conta ainda não está vinculada a um cadastro empresarial.');
        }
        return $cliente;
    }

    protected function authorizeOwnership(AgentKnowledge $item, Cliente $cliente): void
    {
        if ((int) $item->cliente_id !== (int) $cliente->id) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $cliente = $this->requireCliente();

        $niches = BusinessGrowthEngineService::getNiches();
        $currentNiche = $cliente->nicho_atuacao ?: 'outro';
        $currentNicheInfo = $niches[$currentNiche] ?? $niches['outro'];

        // Gera ou carrega ideias de posts do Google Meu Negócio
        $tema = $request->query('tema');
        $suggestedPosts = $this->growthEngine->generateGooglePosts($cliente, $tema);

        // Gera recomendações e radar de oportunidades
        $growthTips = $this->growthEngine->generateGrowthTips($cliente);

        // Itens de DNA / Informações estratégicas da empresa
        $dnaItens = AgentKnowledge::where('cliente_id', $cliente->id)->latest()->get();

        $googleConnected = !empty($cliente->google_location_id);

        return view('customer.conhecimento.index', compact(
            'cliente',
            'niches',
            'currentNiche',
            'currentNicheInfo',
            'suggestedPosts',
            'growthTips',
            'dnaItens',
            'googleConnected'
        ));
    }

    /**
     * Atualiza o perfil estratégico / nicho da empresa
     */
    public function updateProfile(Request $request)
    {
        $cliente = $this->requireCliente();

        $validated = $request->validate([
            'nicho_atuacao' => 'required|string|max:60',
            'tom_voz' => 'nullable|string|max:100',
            'diferenciais_competitivos' => 'nullable|string|max:2000',
            'publico_alvo' => 'nullable|string|max:2000',
        ]);

        $cliente->update($validated);

        return redirect()->route('customer.conhecimento.index')
            ->with('success', 'Perfil do negócio atualizado! O Motor de Crescimento recalibrou as sugestões.');
    }

    /**
     * Publica um post diretamente no Google Meu Negócio
     */
    public function publishGooglePost(Request $request)
    {
        $cliente = $this->requireCliente();

        if (empty($cliente->google_location_id)) {
            return redirect()->route('customer.conhecimento.index')
                ->with('error', 'Sua ficha do Google Meu Negócio ainda não está conectada. Conecte-a em Marketing & Vendas > Google Meu Negócio.');
        }

        $validated = $request->validate([
            'summary' => 'required|string|max:1500',
            'cta_type' => 'nullable|in:CALL,BOOK,ORDER,LEARN_MORE,SIGN_UP',
            'cta_url' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        // Upload de imagem se fornecida
        $imageUrl = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext  = strtolower($file->getClientOriginalExtension() ?: 'jpg');
            $name = 'c' . $cliente->id . '-' . Str::random(20) . '.' . $ext;
            $file->storeAs('gbp-posts', $name, 'public');
            $imageUrl = asset('storage/gbp-posts/' . $name);
        }

        // Se o CTA precisa de URL e nenhuma foi dada, usa o site do cliente ou fallback
        $ctaType = $validated['cta_type'] ?? 'CALL';
        $ctaUrl = $validated['cta_url'] ?? null;

        if ($ctaType !== 'CALL' && empty($ctaUrl)) {
            $landingPage = $cliente->landingPage;
            if ($landingPage && !empty($landingPage->slug)) {
                $ctaUrl = url('/lp/' . $landingPage->slug);
            } else {
                $ctaType = 'CALL'; // Fallback seguro para botão de chamada
            }
        }

        try {
            $this->googleService->createPost(
                $cliente,
                $cliente->google_location_id,
                $validated['summary'],
                [
                    'cta_type' => $ctaType,
                    'cta_url' => $ctaUrl,
                    'image_url' => $imageUrl,
                ]
            );

            return redirect()->route('customer.conhecimento.index')
                ->with('success', '🎉 Post publicado com sucesso na sua ficha do Google Meu Negócio!');
        } catch (\Throwable $e) {
            return redirect()->route('customer.conhecimento.index')
                ->with('error', 'Falha ao publicar no Google: ' . $e->getMessage());
        }
    }

    /**
     * Cadastro de DNA / Informação estratégica complementar
     */
    public function store(Request $request)
    {
        $cliente = $this->requireCliente();

        $validated = $request->validate([
            'titulo' => 'required|string|max:200',
            'conteudo' => 'required|string|max:20000',
            'ativo' => 'sometimes|boolean',
        ]);

        $validated['ativo'] = (bool) ($validated['ativo'] ?? true);
        $validated['cliente_id'] = $cliente->id;

        AgentKnowledge::create($validated);

        return redirect()->route('customer.conhecimento.index')
            ->with('success', 'Informação estratégica adicionada ao DNA do Bruce IA!');
    }

    public function update(Request $request, AgentKnowledge $conhecimento)
    {
        $cliente = $this->requireCliente();
        $this->authorizeOwnership($conhecimento, $cliente);

        $validated = $request->validate([
            'titulo' => 'required|string|max:200',
            'conteudo' => 'required|string|max:20000',
            'ativo' => 'sometimes|boolean',
        ]);

        $validated['ativo'] = (bool) ($validated['ativo'] ?? false);

        $conhecimento->update($validated);

        return redirect()->route('customer.conhecimento.index')
            ->with('success', 'Informação atualizada.');
    }

    public function destroy(AgentKnowledge $conhecimento)
    {
        $cliente = $this->requireCliente();
        $this->authorizeOwnership($conhecimento, $cliente);

        $conhecimento->delete();

        return redirect()->route('customer.conhecimento.index')
            ->with('success', 'Informação removida do DNA da empresa.');
    }
}
