<?php

declare(strict_types=1);

namespace App\Services;

use App\Agent\Contracts\LlmDriver;
use App\Agent\DTOs\PromptPayload;
use App\Models\Cliente;
use Illuminate\Support\Facades\Log;

class BusinessGrowthEngineService
{
    public function __construct(
        protected LlmDriver $llm,
        protected GoogleBusinessProfileService $googleService
    ) {}

    /**
     * Presets de Nichos Suportados
     */
    public static function getNiches(): array
    {
        return [
            'gastronomia' => [
                'label' => 'Alimentação & Gastronomia',
                'icon' => '🍔',
                'description' => 'Hamburguerias, pizzarias, restaurantes, confeitarias e bares.',
                'default_tone' => 'Descontraído, apetitoso e convidativo',
                'cta_type' => 'CALL',
                'suggested_topics' => ['Combo ou Prato Destaque', 'Ingredientes & Bastidores', 'Fim de Semana / Sextou', 'Promoção & Happy Hour'],
            ],
            'imobiliario' => [
                'label' => 'Imobiliário & Corretores',
                'icon' => '🏢',
                'description' => 'Corretores de imóveis, imobiliárias, loteamentos e consultores.',
                'default_tone' => 'Profissional, consultivo e confiável',
                'cta_type' => 'BOOK',
                'suggested_topics' => ['Oportunidade da Semana', 'Dica de Financiamento/Bairro', 'Agende sua Visita', 'Tendências de Mercado'],
            ],
            'saude_estetica' => [
                'label' => 'Saúde, Estética & Bem-Estar',
                'icon' => '🩺',
                'description' => 'Clínicas médicas, odontológicas, salões, barbearias e estética.',
                'default_tone' => 'Acolhedor, técnico e cuidadoso',
                'cta_type' => 'BOOK',
                'suggested_topics' => ['Benefícios do Procedimento', 'Mitos & Verdades', 'Horários Livres da Semana', 'Depoimento / Resultado'],
            ],
            'servicos_profissionais' => [
                'label' => 'Serviços Profissionais',
                'icon' => '⚖️',
                'description' => 'Advocacia, contabilidade, consultoria, marketing e engenharia.',
                'default_tone' => 'Sério, autoridade e didático',
                'cta_type' => 'LEARN_MORE',
                'suggested_topics' => ['Solução para um Problema Comum', 'Atualização de Lei/Mercado', 'Consultoria Inicial', 'Cases de Sucesso'],
            ],
            'comercio_varejo' => [
                'label' => 'Comércio & Varejo',
                'icon' => '🛍️',
                'description' => 'Lojas físicas, confecção, autopeças, óticas e materiais.',
                'default_tone' => 'Animado, focado em benefícios e ofertas',
                'cta_type' => 'CALL',
                'suggested_topics' => ['Novidades da Coleção', 'Oferta Imperdível', 'Dica de Uso/Combinação', 'Venha Conhecer a Loja'],
            ],
            'reformas_servicos' => [
                'label' => 'Reformas & Manutenção',
                'icon' => '🔧',
                'description' => 'Oficinas mecânicas, marcenarias, eletricistas e obras.',
                'default_tone' => 'Prático, transparente e seguro',
                'cta_type' => 'CALL',
                'suggested_topics' => ['Check-up Preventivo', 'Antes e Depois', 'Garantia e Agilidade', 'Faça seu Orçamento'],
            ],
            'outro' => [
                'label' => 'Outro Negócio Local',
                'icon' => '🚀',
                'description' => 'Empresas em geral que buscam mais clientes locais.',
                'default_tone' => 'Profissional e acolhedor',
                'cta_type' => 'CALL',
                'suggested_topics' => ['Nossos Diferenciais', 'Atendimento Rápido', 'Depoimentos de Clientes', 'Fale Conosco'],
            ],
        ];
    }

    /**
     * Gera ideias de postagem para o Google Meu Negócio calibradas pelo nicho e DNA do cliente
     */
    public function generateGooglePosts(Cliente $cliente, ?string $tema = null): array
    {
        $nichoKey = $cliente->nicho_atuacao ?: 'outro';
        $niches = self::getNiches();
        $nichoInfo = $niches[$nichoKey] ?? $niches['outro'];

        $empresa = $cliente->razao_social;
        $tom = $cliente->tom_voz ?: $nichoInfo['default_tone'];
        $diferenciais = $cliente->diferenciais_competitivos ?: 'Atendimento com excelência e qualidade';
        $publico = $cliente->publico_alvo ?: 'Clientes locais e regionais';

        $temaContexto = $tema ? "Tema específico solicitado: {$tema}." : "Gere temas variados para a semana.";

        $system = <<<TXT
Você é o estrategista de Marketing Local e Google Meu Negócio do Bruce IA.
Sua missão é criar 3 sugestões de posts prontas para publicação no Google Meu Negócio para a empresa indicada.
Cada post deve ter alta taxa de atração e engajamento local para o nicho específico.

Regras Estritas:
1. Responda ESTRITAMENTE em formato JSON puro, sem blocos de código markdown ou texto extra, no seguinte formato:
[
  {
    "titulo_tema": "Título curto e chamativo da ideia (ex: Sextou com Smash Duplo)",
    "texto_post": "Texto persuasivo de 200 a 450 caracteres otimizado com emojis e gatilhos de ação local.",
    "sugestao_foto": "Instrução visual curta do tipo de foto real que o cliente deve usar (ex: Foto de perto do hambúrguer cortado com queijo caindo).",
    "cta_recomendado": "CALL|BOOK|LEARN_MORE|ORDER",
    "cta_label": "Texto do botão (ex: Ligar Agora, Agendar Horário, Fazer Pedido)"
  }
]
2. Não ultrapasse 500 caracteres no texto do post (ideal para leitura rápida na busca do Google).
3. Adapte a linguagem totalmente para o nicho e tom da empresa.
TXT;

        $userPrompt = <<<TXT
Empresa: {$empresa}
Segmento / Nicho: {$nichoInfo['label']} ({$nichoInfo['description']})
Tom de voz: {$tom}
Diferenciais da Empresa: {$diferenciais}
Público-alvo: {$publico}
{$temaContexto}
Gere as 3 ideias de posts agora em JSON puro.
TXT;

        try {
            $response = $this->llm->complete(new PromptPayload($system, $userPrompt));
            $content = trim($response->content);

            // Limpa formatação Markdown se vier envolvido em ```json ... ```
            if (str_starts_with($content, '```')) {
                $content = preg_replace('/^```(?:json)?\s*/i', '', $content);
                $content = preg_replace('/\s*```$/', '', $content);
                $content = trim($content);
            }

            $decoded = json_decode($content, true);
            if (is_array($decoded) && count($decoded) > 0) {
                return $decoded;
            }
        } catch (\Throwable $e) {
            Log::warning("[GrowthEngine] Falha ao gerar posts com LLM para cliente {$cliente->id}: " . $e->getMessage());
        }

        // Fallback inteligente caso a LLM esteja sem saldo ou offline
        return $this->getFallbackPosts($cliente, $nichoInfo);
    }

    /**
     * Gera recomendações práticas e radar de crescimento para a empresa
     */
    public function generateGrowthTips(Cliente $cliente): array
    {
        $nichoKey = $cliente->nicho_atuacao ?: 'outro';
        $niches = self::getNiches();
        $nichoInfo = $niches[$nichoKey] ?? $niches['outro'];

        $tips = [];

        // 1. Verificação do Google Meu Negócio
        if (empty($cliente->google_location_id)) {
            $tips[] = [
                'categoria' => 'Google Local',
                'badge' => 'Oportunidade de Alto Impacto',
                'titulo' => 'Conecte sua Ficha no Google Meu Negócio',
                'descricao' => 'Negócios com ficha conectada recebem até 5x mais chamadas e rotas no Google Maps do que negócios sem presença ativa.',
                'acao_label' => 'Conectar Ficha Agora',
                'acao_url' => route('customer.google-business.index'),
                'tipo' => 'alerta',
            ];
        } else {
            $tips[] = [
                'categoria' => 'Google Local',
                'badge' => 'Impulsionamento Semanal',
                'titulo' => 'Mantenha sua ficha no topo das buscas',
                'descricao' => 'O Google prioriza perfis que postam novidades ou fotos a cada 7 dias. Publique hoje uma nova atualização.',
                'acao_label' => 'Publicar Post no Google',
                'acao_url' => '#aba-posts',
                'tipo' => 'sucesso',
            ];
        }

        // 2. Dicas específicas por nicho
        if ($nichoKey === 'gastronomia') {
            $tips[] = [
                'categoria' => 'Vendas & Fidelização',
                'badge' => 'Estratégia de Fim de Semana',
                'titulo' => 'Campanha de Reativação no WhatsApp na Sexta-feira',
                'descricao' => 'Envie uma mensagem convidativa no final da tarde lembrando os clientes fiéis do combo da semana antes que eles peçam no concorrente.',
                'acao_label' => 'Abrir WhatsApp Web',
                'acao_url' => route('customer.whatsapp.inbox'),
                'tipo' => 'dica',
            ];
            $tips[] = [
                'categoria' => 'Cardápio & Fotos',
                'badge' => 'Apetite Visual',
                'titulo' => 'Fotos reais vendem 3x mais que ilustrações',
                'descricao' => 'Tire fotos de produtos recém-saídos com boa iluminação natural e publique tanto no seu Site/Landing Page quanto no Google.',
                'acao_label' => 'Editar Meu Site',
                'acao_url' => route('customer.landing-page.edit'),
                'tipo' => 'dica',
            ];
        } elseif ($nichoKey === 'imobiliario') {
            $tips[] = [
                'categoria' => 'Relacionamento & CRM',
                'badge' => 'Follow-up de Alto Valor',
                'titulo' => 'Acompanhamento de Leads com mais de 7 dias',
                'descricao' => 'No mercado imobiliário, clientes que não recebem novas opções em até 10 dias esfriam. Consulte seus contatos no CRM e envie um comparativo.',
                'acao_label' => 'Ver Clientes no CRM',
                'acao_url' => route('customer.clientes-finais.index'),
                'tipo' => 'dica',
            ];
            $tips[] = [
                'categoria' => 'Autoridade Local',
                'badge' => 'Google & SEO',
                'titulo' => 'Posts educativos sobre valorização do bairro',
                'descricao' => 'Compradores buscam segurança e valorização. Publique posts explicando o crescimento da infraestrutura da sua cidade.',
                'acao_label' => 'Gerar Post do Bairro',
                'acao_url' => '#aba-posts',
                'tipo' => 'dica',
            ];
        } elseif ($nichoKey === 'saude_estetica') {
            $tips[] = [
                'categoria' => 'Agenda & Horários',
                'badge' => 'Ocupação de Horários',
                'titulo' => 'Confirmação automática de consultas',
                'descricao' => 'O Robô Bruce no WhatsApp confirma consultas para evitar faltas e reagendar pacientes com antecedência.',
                'acao_label' => 'Configurar Bot da Agenda',
                'acao_url' => route('customer.whatsapp.bot.index'),
                'tipo' => 'dica',
            ];
        } else {
            $tips[] = [
                'categoria' => 'Atração Local',
                'badge' => 'Visibilidade',
                'titulo' => 'Peça avaliações para clientes satisfeitos',
                'descricao' => 'A cada 5 avaliações 5 estrelas recebidas no Google, a taxa de clique de novos clientes aumenta cerca de 25%.',
                'acao_label' => 'Ver Avaliações no Google',
                'acao_url' => route('customer.google-business.index'),
                'tipo' => 'dica',
            ];
        }

        return $tips;
    }

    /**
     * Fallback de posts caso a LLM não responda
     */
    protected function getFallbackPosts(Cliente $cliente, array $nichoInfo): array
    {
        $nome = $cliente->razao_social;

        if (($cliente->nicho_atuacao ?: '') === 'gastronomia') {
            return [
                [
                    'titulo_tema' => 'Combos e Destaques da Semana',
                    'texto_post' => "Bateu aquela fome? Aqui na {$nome} preparamos tudo com ingredientes frescos e sabor inconfundível! Peça pelo WhatsApp ou venha nos visitar.",
                    'sugestao_foto' => 'Foto em close do produto principal bem iluminado.',
                    'cta_recomendado' => 'CALL',
                    'cta_label' => 'Fazer Pedido',
                ],
                [
                    'titulo_tema' => 'Final de Semana Especial',
                    'texto_post' => "Final de semana combina com o melhor cardápio da cidade! Reúna os amigos ou a família e aproveite nossos pratos especiais na {$nome}.",
                    'sugestao_foto' => 'Mesa servida com opções variadas e bebidas.',
                    'cta_recomendado' => 'CALL',
                    'cta_label' => 'Ligar Agora',
                ],
                [
                    'titulo_tema' => 'Qualidade e Bastidores',
                    'texto_post' => "Cuidado em cada detalhe, do preparo à entrega. Venha conferir por que nossos clientes avaliam a {$nome} com 5 estrelas!",
                    'sugestao_foto' => 'Bastidores da cozinha organizada ou equipe atendendo.',
                    'cta_recomendado' => 'LEARN_MORE',
                    'cta_label' => 'Saiba Mais',
                ],
            ];
        }

        if (($cliente->nicho_atuacao ?: '') === 'imobiliario') {
            return [
                [
                    'titulo_tema' => 'Oportunidade da Semana para Morar ou Investir',
                    'texto_post' => "Procurando o imóvel ideal na região? Na {$nome} selecionamos as melhores opções com condições facilitadas e localização estratégica.",
                    'sugestao_foto' => 'Foto nítida da fachada ou sala ampla do imóvel.',
                    'cta_recomendado' => 'BOOK',
                    'cta_label' => 'Agendar Visita',
                ],
                [
                    'titulo_tema' => 'Dica de Especialista: Financiamento Inteligente',
                    'texto_post' => "Antes de comprar, entenda como simular as melhores taxas de financiamento. Fale conosco para uma consultoria personalizada sem custos.",
                    'sugestao_foto' => 'Foto profissional do corretor ou da equipe atendendo.',
                    'cta_recomendado' => 'CALL',
                    'cta_label' => 'Ligue Agora',
                ],
                [
                    'titulo_tema' => 'Segurança e Agilidade na Negociação',
                    'texto_post' => "Comprar ou alugar com segurança jurídica é fundamental. Conte com a expertise da {$nome} para fechar o melhor negócio com tranquilidade.",
                    'sugestao_foto' => 'Assinatura de chaves ou maquete/tour virtual.',
                    'cta_recomendado' => 'LEARN_MORE',
                    'cta_label' => 'Saiba Mais',
                ],
            ];
        }

        return [
            [
                'titulo_tema' => 'Atendimento com Qualidade Comprovada',
                'texto_post' => "Precisa de soluções confiáveis e ágeis? A {$nome} oferece atendimento personalizado para superar suas expectativas. Fale com nossa equipe!",
                'sugestao_foto' => 'Foto da fachada da empresa ou da equipe trabalhando.',
                'cta_recomendado' => 'CALL',
                'cta_label' => 'Ligar Agora',
            ],
            [
                'titulo_tema' => 'Novidades e Condições Especiais',
                'texto_post' => "Estamos com condições especiais para você este mês! Visite nosso espaço ou solicite um orçamento direto pelo WhatsApp da {$nome}.",
                'sugestao_foto' => 'Foto de um serviço sendo executado com excelência.',
                'cta_recomendado' => 'LEARN_MORE',
                'cta_label' => 'Saiba Mais',
            ],
            [
                'titulo_tema' => 'Por que nossos clientes confiam na gente?',
                'texto_post' => "Compromisso com prazo, transparência e satisfação total. Venha conhecer a {$nome} e descubra a diferença de um serviço profissional.",
                'sugestao_foto' => 'Depoimento de cliente ou certificado/diferencial da empresa.',
                'cta_recomendado' => 'CALL',
                'cta_label' => 'Fale Conosco',
            ],
        ];
    }
}
