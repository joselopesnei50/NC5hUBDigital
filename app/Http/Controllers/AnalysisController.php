<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Configuracao;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AnalysisController extends Controller
{
    public function index()
    {
        return view('public.analise');
    }

    public function process(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'whatsapp' => 'required|string|max:20',
            'tipo_analise' => 'required|in:marca,site,redes_sociais',
            'url_site' => 'required_if:tipo_analise,site',
            'url_social' => 'required_if:tipo_analise,redes_sociais',
            'bio_social' => 'nullable|string',
            'produto_social' => 'nullable|string',
            'url_marca' => 'required_if:tipo_analise,marca',
            'promessa_marca' => 'nullable|string',
            'publico_marca' => 'nullable|string',
        ]);

        $apiKey = Configuracao::get('deepseek_api_key');

        if (empty($apiKey)) {
            return back()->with('error', 'O motor de análise BruceIA está temporariamente indisponível. Tente novamente em instantes.');
        }

        // Obter a URL principal para salvar no banco
        $urlAnalise = match ($request->tipo_analise) {
            'site' => $request->url_site,
            'redes_sociais' => $request->url_social,
            'marca' => $request->url_marca,
            default => '',
        };

        $lead = Lead::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'whatsapp' => $request->whatsapp,
            'tipo_analise' => $request->tipo_analise,
            'url_analise' => $urlAnalise,
        ]);

        $contextoExtra = '';
        $promptContext = '';

        if ($request->tipo_analise === 'site') {
            $promptContext = "Você vai analisar o site enviado. Foque em UX/UI, clareza da proposta de valor, hierarquia de informação, prova social, CTA principal e SEO básico.";
            if ($this->pareceUrl($urlAnalise)) {
                $conteudoSite = $this->capturarConteudoSite($urlAnalise);
                if (!empty($conteudoSite)) {
                    $contextoExtra = "\n\nConteúdo extraído via scraper do site (analise o copy e estrutura baseada nisso):\n---\n" . $conteudoSite . "\n---";
                }
            }
        } elseif ($request->tipo_analise === 'redes_sociais') {
            $promptContext = "Você vai analisar a rede social do cliente com base nos textos que ele forneceu. Foque no posicionamento visual que a bio transmite, o quão atrativo é o produto/serviço e se a promessa condiz com uma agência premium.";
            $contextoExtra = "\n\nDados fornecidos pelo cliente sobre o Instagram:\n" .
                             "- Usuário: " . $request->url_social . "\n" .
                             "- Bio exata do perfil: " . $request->bio_social . "\n" .
                             "- Principal produto/serviço vendido: " . $request->produto_social;
        } else {
            $promptContext = "Você vai analisar a marca do cliente baseada na sua promessa e público. Foque em posicionamento de mercado, tom de voz adequado para esse público e diferenciação competitiva.";
            $contextoExtra = "\n\nDados fornecidos pelo cliente sobre a Marca:\n" .
                             "- Nome da Marca: " . $request->url_marca . "\n" .
                             "- Promessa/Slogan: " . $request->promessa_marca . "\n" .
                             "- Público-alvo: " . $request->publico_marca;
        }

        $systemPrompt = <<<PROMPT
Você é o Bruce (BruceIA), consultor sênior de marketing e performance da agência NC5 Hub — premium, direto e focado em conversão.

Sua tarefa é analisar o que o cliente enviou de forma profissional, honesta e valiosa.

Responda em Markdown, exclusivamente com esta estrutura (usar exatamente esses títulos H3):

### Visão Geral
Sua primeira impressão profissional em 2-4 linhas.

### Pontos Fortes
Lista com bullets do que já está bom. Seja específico.

### Oportunidades Críticas
Lista com bullets do que precisa melhorar urgentemente para faturar mais. Priorize por impacto.

### Veredito da Agência
Conclusão curta (2-4 linhas) recomendando uma reestruturação, impulsionamento ou próximo passo estratégico.

Regras:
- Tom cordial, premium e autoritário — de especialista, sem jargão inflado.
- Nunca invente números, prêmios ou fatos que não vieram nos dados enviados.
- Se faltar informação para uma seção, diga honestamente e recomende o próximo passo para obtê-la.
- Nunca cite o motor por trás (não fale de DeepSeek/OpenAI/etc.) — você é o Bruce, ponto.

Contexto específico do pedido: {$promptContext}
PROMPT;

        $userPrompt = "Dados estruturados do cliente para análise: {$urlAnalise}" . $contextoExtra;

        try {
            $response = Http::withToken($apiKey)
                ->timeout(60)
                ->post('https://api.deepseek.com/chat/completions', [
                    'model' => 'deepseek-chat',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user',   'content' => $userPrompt],
                    ],
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                $resultado = $response->json()['choices'][0]['message']['content'];

                $lead->update(['resultado_ia' => $resultado]);

                $html = Str::markdown($resultado);
                $htmlSanitizado = strip_tags(
                    $html,
                    '<h1><h2><h3><h4><h5><h6><p><strong><b><em><i><ul><ol><li><br><hr><blockquote><a><code>'
                );

                return view('public.analise_resultado', [
                    'lead' => $lead,
                    'resultado' => $htmlSanitizado,
                ]);
            }

            Log::error('BruceIA falha (motor de análise): ' . $response->body());
            return back()->with('error', 'O Bruce demorou demais para responder. Tente novamente em instantes.');

        } catch (\Exception $e) {
            Log::error('BruceIA exceção: ' . $e->getMessage());
            return back()->with('error', 'Ocorreu um erro ao conectar com o Bruce. Tente novamente mais tarde.');
        }
    }

    private function pareceUrl(string $texto): bool
    {
        return (bool) preg_match('/^https?:\/\//i', trim($texto));
    }

    private function capturarConteudoSite(string $url): string
    {
        try {
            $resposta = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; BruceIA/1.0; +https://nc5hub.com/bruce)'])
                ->get($url);

            if (!$resposta->successful()) {
                return '';
            }

            $html = $resposta->body();

            // Extrai título, meta description e texto visível
            $titulo = '';
            if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $m)) {
                $titulo = trim(html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            }

            $metaDesc = '';
            if (preg_match('/<meta[^>]+name=["\']description["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m)) {
                $metaDesc = trim(html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            }

            // Remove script/style, depois strip_tags no restante
            $limpo = preg_replace('/<script\b[^>]*>.*?<\/script>/is', ' ', $html);
            $limpo = preg_replace('/<style\b[^>]*>.*?<\/style>/is', ' ', $limpo);
            $texto = trim(preg_replace('/\s+/', ' ', strip_tags($limpo)));
            $texto = Str::limit($texto, 3000, '…');

            return trim("Título: {$titulo}\nDescrição meta: {$metaDesc}\nConteúdo visível (recorte): {$texto}");
        } catch (\Exception $e) {
            Log::warning('BruceIA scraping falhou para ' . $url . ': ' . $e->getMessage());
            return '';
        }
    }
}
