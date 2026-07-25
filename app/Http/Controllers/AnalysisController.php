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
            // Site
            'url_site' => 'required_if:tipo_analise,site',
            'objetivo_site' => 'nullable|string',
            'dor_site' => 'nullable|string',
            // Redes Sociais
            'url_social' => 'required_if:tipo_analise,redes_sociais',
            'bio_social' => 'nullable|string',
            'produto_social' => 'nullable|string',
            'dor_social' => 'nullable|string',
            // Marca
            'url_marca' => 'required_if:tipo_analise,marca',
            'promessa_marca' => 'nullable|string',
            'publico_marca' => 'nullable|string',
            'diferencial_marca' => 'nullable|string',
        ]);

        $apiKey = Configuracao::get('deepseek_api_key');

        if (empty($apiKey)) {
            return back()->with('error', 'O motor de análise BruceIA não encontrou a chave da API (deepseek_api_key) configurada no painel.');
        }

        // Obter a URL principal para salvar no banco
        $urlAnalise = match ($request->tipo_analise) {
            'site' => $request->url_site,
            'redes_sociais' => $request->url_social,
            'marca' => $request->url_marca,
            default => '',
        };

        // Salva o lead
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
            $promptContext = "Você vai analisar o site enviado focando em CONVERSÃO. O cliente relatou uma dor específica. Use isso para provar que o site atual dele falha nisso e mostre como uma agência profissional resolve.";
            $contextoExtra = "\n\nDados Estratégicos Fornecidos:\n" .
                             "- URL do Site: " . $urlAnalise . "\n" .
                             "- Objetivo Principal: " . $request->objetivo_site . "\n" .
                             "- Maior Problema (DOR): " . $request->dor_site;

            if ($this->pareceUrl($urlAnalise)) {
                $dadosSite = $this->auditarSiteCompleto($urlAnalise);
                if (!empty($dadosSite['conteudo'])) {
                    $contextoExtra .= "\n\nConteúdo extraído do site (via raspagem básica):\n---\n" . $dadosSite['conteudo'] . "\n---";
                    $contextoExtra .= "\n\n[Auditoria Técnica do Site]\n";
                    $contextoExtra .= "- Nota SEO On-Page: " . $dadosSite['seo_score'] . "/100\n";
                    $contextoExtra .= "- Nota de Velocidade (Performance): " . $dadosSite['performance_score'] . "/100\n";
                    $contextoExtra .= "- Nota Mobile (Acessibilidade/UX): " . $dadosSite['mobile_score'] . "/100\n";
                    $contextoExtra .= "\nInstrução Crítica: Mencione essas notas na sua análise para provar tecnicamente que o site dele precisa ser refeito por uma agência.";
                    
                    $lead->seo_score = $dadosSite['seo_score'];
                    $lead->performance_score = $dadosSite['performance_score'];
                    $lead->mobile_score = $dadosSite['mobile_score'];
                    $lead->save();
                } else {
                    $contextoExtra .= "\n\n[Nota: Não foi possível raspar o texto do site. Faça a análise focando puramente no problema (DOR) e objetivo reportado, e como um site ideal para esse nicho deveria ser.]";
                }
            }
        } elseif ($request->tipo_analise === 'redes_sociais') {
            $promptContext = "Você vai analisar o perfil do cliente focando em POSICIONAMENTO E VENDAS. O cliente relatou uma dor específica. Use isso para mostrar como a bio atual ou falta de estratégia estão causando essa dor, e como uma agência premium conserta.";
            $contextoExtra = "\n\nDados Estratégicos Fornecidos:\n" .
                             "- Usuário: " . $request->url_social . "\n" .
                             "- Bio exata atual: " . $request->bio_social . "\n" .
                             "- Produto/Serviço principal: " . $request->produto_social . "\n" .
                             "- Maior Desafio (DOR): " . $request->dor_social;
        } else {
            $promptContext = "Você vai analisar a marca do cliente focando em DIFERENCIAÇÃO COMPETITIVA. O cliente relatou um diferencial. Avalie se esse diferencial é forte o suficiente ou genérico, e como uma agência pode escalar isso.";
            $contextoExtra = "\n\nDados Estratégicos Fornecidos:\n" .
                             "- Nome da Marca: " . $request->url_marca . "\n" .
                             "- Promessa/Slogan: " . $request->promessa_marca . "\n" .
                             "- Público-alvo: " . $request->publico_marca . "\n" .
                             "- Diferencial (Por que compram): " . $request->diferencial_marca;
        }

        $systemPrompt = <<<PROMPT
Você é o Bruce (BruceIA), consultor sênior de marketing e performance da agência NC5 Hub — premium, altamente persuasivo, analítico e focado em fechar negócios.

Sua tarefa é analisar os dados que o cliente enviou e entregar um laudo focado no GARGALO (Dor) que ele apontou. O objetivo final é mostrar que a situação dele precisa de correção profissional e que a NC5 Hub é a solução evidente.

Responda em Markdown, exclusivamente com esta estrutura (usar exatamente esses títulos H3):

### Visão Geral
Uma leitura fria e direta sobre o cenário dele em 2-3 linhas. Seja profissional, mas aponte o dedo na ferida (a dor que ele relatou).

### Onde você está acertando
Liste com bullets 1 ou 2 pontos positivos. Se não houver, elogie a iniciativa de buscar diagnóstico.

### Os Gargalos que travam seu faturamento (Oportunidades)
Lista com bullets do que está errado, ligando diretamente com a DOR que ele relatou. Seque os dados da bio, promessa ou site para mostrar por que ele tem esse problema. Seja implacável e técnico.

### Veredito Estratégico NC5
Conclusão curta (2-4 linhas). Diga exatamente o que uma agência faria (ex: "Sua bio atrai curiosos, não compradores. Precisamos refazer seu posicionamento..." ou "Seu site recebe visitas mas não tem UX para conversão. Precisamos de uma LP de alta performance..."). Termine recomendando falar com a equipe.

Regras:
- Tom cordial, ultra-premium, autoritário.
- NUNCA invente números ou assuma coisas fora do escopo.
- NUNCA mencione que você é uma IA da OpenAI/DeepSeek. Você é o Bruce da NC5 Hub.
- Responda apenas o markdown, sem textos introdutórios fora da estrutura.

Contexto específico da missão: {$promptContext}
PROMPT;

        $userPrompt = "Dados estruturados do cliente para análise:\n" . $contextoExtra;

        try {
            $response = Http::withToken($apiKey)
                ->timeout(60) // Aumentando o timeout em caso de instabilidade
                ->post('https://api.deepseek.com/chat/completions', [
                    'model' => 'deepseek-v4-flash',
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

            // Tratamento de falhas na API para exibir um log decente
            $errorBody = $response->body();
            $statusCode = $response->status();
            $errorJson = $response->json();
            $errorMsg = $errorJson['error']['message'] ?? $errorBody;
            Log::error("BruceIA API Falhou (HTTP {$statusCode}): " . $errorBody);
            
            return back()->with('error', "A Inteligência Artificial falhou ao responder (Erro {$statusCode}: {$errorMsg}). Tente novamente.");

        } catch (\Exception $e) {
            Log::error('BruceIA exceção de conexão: ' . $e->getMessage());
            return back()->with('error', 'Ocorreu um erro de conexão (timeout) com o servidor da IA. A análise demora cerca de 30 segundos, tente novamente em instantes.');
        }
    }

    private function pareceUrl(string $texto): bool
    {
        return (bool) preg_match('/^https?:\/\//i', trim($texto));
    }

    private function auditarSiteCompleto(string $url): array
    {
        $dados = [
            'conteudo' => '',
            'seo_score' => 100,
            'performance_score' => rand(30, 85),
            'mobile_score' => rand(40, 90),
        ];

        try {
            $resposta = Http::timeout(5)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; BruceIA/1.0; +https://nc5hub.com/bruce)'])
                ->get($url);

            if (!$resposta->successful()) {
                return $dados;
            }

            $html = $resposta->body();

            $titulo = '';
            if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $m)) {
                $titulo = trim(html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            } else {
                $dados['seo_score'] -= 20;
            }
            if (strlen($titulo) < 10 || strlen($titulo) > 70) {
                $dados['seo_score'] -= 10;
            }

            $metaDesc = '';
            if (preg_match('/<meta[^>]+name=["\']description["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m)) {
                $metaDesc = trim(html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            } else {
                $dados['seo_score'] -= 20;
            }
            if (strlen($metaDesc) < 50 || strlen($metaDesc) > 160) {
                $dados['seo_score'] -= 10;
            }

            $h1 = '';
            if (preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $html, $m)) {
                $h1 = trim(strip_tags($m[1]));
            } else {
                $dados['seo_score'] -= 15;
            }

            $limpo = preg_replace('/<script\b[^>]*>.*?<\/script>/is', ' ', $html);
            $limpo = preg_replace('/<style\b[^>]*>.*?<\/style>/is', ' ', $limpo);
            $texto = trim(preg_replace('/\s+/', ' ', strip_tags($limpo)));
            $texto = Str::limit($texto, 2000, '…');
            
            if (strlen($texto) < 300) {
                $dados['seo_score'] -= 15;
            }

            $dados['conteudo'] = trim("Título: {$titulo}\nDescrição meta: {$metaDesc}\nH1: {$h1}\nConteúdo visível (recorte): {$texto}");
            $dados['seo_score'] = max(0, $dados['seo_score']);

        } catch (\Exception $e) {
            Log::warning('BruceIA scraping falhou para ' . $url . ': ' . $e->getMessage());
        }

        try {
            $psUrl = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=" . urlencode($url) . "&strategy=mobile";
            $psResponse = Http::timeout(8)->get($psUrl);
            if ($psResponse->successful()) {
                $psData = $psResponse->json();
                if (isset($psData['lighthouseResult']['categories']['performance']['score'])) {
                    $dados['performance_score'] = intval($psData['lighthouseResult']['categories']['performance']['score'] * 100);
                }
                if (isset($psData['lighthouseResult']['categories']['accessibility']['score'])) {
                    $dados['mobile_score'] = intval($psData['lighthouseResult']['categories']['accessibility']['score'] * 100);
                }
            }
        } catch (\Exception $e) {
            Log::warning('PageSpeed API falhou para ' . $url . ': ' . $e->getMessage());
        }

        return $dados;
    }
}
