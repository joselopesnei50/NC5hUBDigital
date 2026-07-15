<?php

namespace Database\Seeders;

use App\Models\Servico;
use App\Models\Post;
use App\Models\Pagina;
use Illuminate\Database\Seeder;

class ExemploSeeder extends Seeder
{
    public function run()
    {
        // ─────────────────────────────────────────────
        // CATÁLOGO DE SERVIÇOS
        // ─────────────────────────────────────────────

        $servicos = [
            [
                'nome' => 'Gestão de Tráfego Pago',
                'descricao' => 'Campanhas estratégicas no Meta Ads, Google Ads e TikTok Ads com otimização contínua baseada em dados. Inclui planejamento de mídia, criação de públicos segmentados, testes A/B e relatórios semanais de performance com métricas reais de ROAS.',
                'preco' => 2500.00,
                'status' => 'ativo',
            ],
            [
                'nome' => 'Social Media Premium',
                'descricao' => 'Gestão completa das redes sociais da sua marca com planejamento editorial, 20 posts mensais com design profissional, stories estratégicos, copywriting persuasivo e monitoramento de métricas de engajamento e crescimento orgânico.',
                'preco' => 1800.00,
                'status' => 'ativo',
            ],
            [
                'nome' => 'Branding & Identidade Visual',
                'descricao' => 'Criação completa da identidade da sua marca: logotipo, paleta de cores, tipografia, manual de marca, papelaria digital e aplicações para redes sociais. Um ecossistema visual coeso que transmite autoridade e profissionalismo.',
                'preco' => 4500.00,
                'status' => 'ativo',
            ],
            [
                'nome' => 'Desenvolvimento Web Sob Medida',
                'descricao' => 'Sites institucionais, landing pages e e-commerces construídos com tecnologia de ponta, design responsivo, SEO on-page, integração com CRM e velocidade de carregamento otimizada para conversão máxima.',
                'preco' => 6000.00,
                'status' => 'ativo',
            ],
            [
                'nome' => 'Automação de Marketing',
                'descricao' => 'Funis de vendas automatizados com e-mail marketing, chatbots inteligentes, sequências de nutrição de leads e integração com WhatsApp Business API. Transforme visitantes em clientes no piloto automático.',
                'preco' => 3200.00,
                'status' => 'ativo',
            ],
            [
                'nome' => 'Consultoria de Growth',
                'descricao' => 'Sessões mensais de mentoria estratégica com análise profunda do seu funil de vendas, identificação de gargalos de conversão, definição de KPIs e plano de ação personalizado para escalar o faturamento.',
                'preco' => 1500.00,
                'status' => 'ativo',
            ],
        ];

        foreach ($servicos as $s) {
            Servico::updateOrCreate(['nome' => $s['nome']], $s);
        }

        // ─────────────────────────────────────────────
        // ARTIGOS DO BLOG
        // ─────────────────────────────────────────────

        $posts = [
            [
                'titulo' => 'Como o Tráfego Pago Pode Triplicar Suas Vendas em 90 Dias',
                'slug' => 'trafego-pago-triplicar-vendas-90-dias',
                'conteudo' => "Se você ainda depende apenas do orgânico para vender, está deixando dinheiro na mesa. O tráfego pago, quando bem gerido, é a alavanca mais poderosa para escalar um negócio digital.\n\nNão se trata apenas de \"boostar\" posts. Estamos falando de uma estratégia completa que envolve:\n\n1. Mapeamento da Jornada do Cliente — Entender em que estágio o seu lead está e criar anúncios específicos para cada fase do funil.\n\n2. Públicos Inteligentes — Utilizar dados de pixel, lookalikes e remarketing para atingir exatamente quem tem maior propensão de compra.\n\n3. Criativos que Convertem — A parte visual não é decoração. Cada imagem, vídeo ou carrossel precisa ser pensado para gerar cliques e conversões.\n\n4. Otimização Contínua — O jogo não acaba quando o anúncio sobe. Análise diária de CPC, CTR, CPA e ROAS é o que separa campanhas medianas de campanhas que geram milhões.\n\nNa NC5, operamos com uma metodologia proprietária de Growth Ads que já gerou mais de R$ 12 milhões em receita para nossos clientes nos últimos 12 meses. Se a sua empresa fatura acima de R$ 50 mil/mês e quer escalar, precisamos conversar.",
                'status' => 'publicado',
            ],
            [
                'titulo' => '5 Sinais de que Sua Marca Precisa de um Rebranding Urgente',
                'slug' => '5-sinais-rebranding-urgente',
                'conteudo' => "A identidade visual é a primeira impressão que o mercado tem da sua empresa. E primeiras impressões importam mais do que nunca na era digital.\n\nVeja se você se identifica com algum destes sinais:\n\n1. Seu Logo Parece Datado — Se o seu logotipo foi feito há mais de 5 anos e nunca foi atualizado, provavelmente ele está comunicando que a sua empresa parou no tempo.\n\n2. Inconsistência Visual — Cada material da sua empresa parece ter sido feito por uma pessoa diferente. Cores, fontes e estilos variam sem critério entre o site, redes sociais e materiais impressos.\n\n3. Você Tem Vergonha de Compartilhar — Se você hesita antes de enviar o link do seu site ou entregar um cartão de visita, isso é um sinal claro.\n\n4. Seu Público Mudou — Empresas evoluem. Se o seu público-alvo hoje é diferente de quando a marca foi criada, a identidade visual precisa acompanhar essa mudança.\n\n5. Concorrentes Parecem Mais Profissionais — Quando a concorrência investe em branding e você não, a percepção de valor do mercado muda. E não a seu favor.\n\nUm rebranding bem executado não é só estética. É estratégia. É reposicionar a percepção do mercado sobre o valor que você entrega.",
                'status' => 'publicado',
            ],
            [
                'titulo' => 'O Guia Definitivo de Automação de Marketing para PMEs',
                'slug' => 'guia-definitivo-automacao-marketing-pmes',
                'conteudo' => "Automação de marketing não é mais exclusividade de grandes corporações. Com as ferramentas certas, qualquer PME pode implementar fluxos automatizados que economizam tempo e aumentam as vendas.\n\nO que automatizar primeiro?\n\nCaptura de Leads — Landing pages otimizadas com formulários inteligentes que segmentam automaticamente os contatos por interesse, origem e perfil.\n\nNutrição por E-mail — Sequências automáticas que educam o lead sobre seu produto ou serviço, quebram objeções e preparam o terreno para a venda.\n\nChatbots no WhatsApp — Atendimento 24/7 com respostas pré-configuradas para as dúvidas mais frequentes, agendamento de reuniões e qualificação de leads.\n\nScoring de Leads — Um sistema de pontuação que identifica automaticamente quais leads estão prontos para comprar, permitindo que seu time comercial foque apenas nos contatos quentes.\n\nRetargeting Inteligente — Anúncios automáticos que perseguem visitantes que não converteram, mostrando exatamente o produto que eles viram no seu site.\n\nA automação não substitui o humano. Ela libera o humano para fazer o que realmente importa: fechar negócios e construir relacionamentos.",
                'status' => 'publicado',
            ],
        ];

        foreach ($posts as $p) {
            Post::updateOrCreate(['slug' => $p['slug']], $p);
        }

        // ─────────────────────────────────────────────
        // PÁGINAS INSTITUCIONAIS
        // ─────────────────────────────────────────────

        $paginas = [
            [
                'titulo' => 'Quem Somos',
                'slug' => 'quem-somos',
                'conteudo' => "A NC5 Hub nasceu da convicção de que marketing de verdade gera resultado mensurável. Somos uma agência de performance digital que combina estratégia, design premium e tecnologia avançada para acelerar negócios ambiciosos.\n\nNossa equipe é formada por especialistas em tráfego pago, branding, automação e desenvolvimento web que trabalham de forma integrada para entregar soluções completas. Não vendemos pacotes genéricos — criamos ecossistemas digitais sob medida para cada cliente.\n\nNossa Missão: Elevar marcas através de inteligência, design e performance orientada a dados.\n\nNossos Valores:\n• Resultados acima de promessas\n• Transparência radical com dados\n• Design como diferencial competitivo\n• Tecnologia a serviço da estratégia",
                'status' => 'publicado',
            ],
        ];

        foreach ($paginas as $pg) {
            Pagina::updateOrCreate(['slug' => $pg['slug']], $pg);
        }

        $this->command->info('✅ Exemplos criados: 6 serviços, 3 artigos, 1 página institucional.');
    }
}
