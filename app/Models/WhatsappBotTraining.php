<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappBotTraining extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_bot_training';

    protected $guarded = [];

    protected $casts = [
        'horario_comercial' => 'array',
    ];

    public const DIAS_SEMANA = ['seg', 'ter', 'qua', 'qui', 'sex', 'sab', 'dom'];
    public const DIAS_SEMANA_LABEL = [
        'seg' => 'Segunda', 'ter' => 'Terça', 'qua' => 'Quarta',
        'qui' => 'Quinta', 'sex' => 'Sexta', 'sab' => 'Sábado', 'dom' => 'Domingo',
    ];

    public function getHorarioDoDia(string $dia): ?array
    {
        $h = $this->horario_comercial[$dia] ?? null;
        if (!is_array($h) || empty($h['aberto'])) {
            return null;
        }
        return $h;
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Calcula o nível de prontidão/inteligência do treinamento do Agente (0 a 100%).
     */
    public function calculateReadiness(?WhatsappBotProfile $profile, int $catalogCount = 0, int $knowledgeCount = 0): array
    {
        $hasHours = false;
        if (is_array($this->horario_comercial)) {
            foreach ($this->horario_comercial as $d) {
                if (!empty($d['aberto'])) {
                    $hasHours = true;
                    break;
                }
            }
        }

        $items = [
            [
                'label' => 'Segmento e Persona',
                'done' => !empty($profile?->segment),
                'weight' => 15,
                'hint' => 'Define a base de comportamento do seu nicho.',
            ],
            [
                'label' => 'Identidade e Tom de Voz',
                'done' => !empty($this->business_name) && (!empty($this->tom_de_voz) || !empty($this->horario_atendimento)),
                'weight' => 15,
                'hint' => 'Nome da empresa, tom da marca e horário.',
            ],
            [
                'label' => 'Diferenciais e Como Funciona',
                'done' => !empty($this->diferenciais) || !empty($this->processo_producao),
                'weight' => 20,
                'hint' => 'Argumentos de valor e como seu produto/serviço é entregue.',
            ],
            [
                'label' => 'Catálogo / Preços Sincronizados',
                'done' => $catalogCount > 0 || !empty($this->catalogo_texto),
                'weight' => 20,
                'hint' => $catalogCount > 0 ? "{$catalogCount} item(ns) sincronizado(s) do Catálogo BruceOS." : 'Cadastre no Catálogo ou preencha em texto.',
            ],
            [
                'label' => 'Políticas e FAQ',
                'done' => !empty($this->politicas) || !empty($this->faq_custom) || $knowledgeCount > 0,
                'weight' => 15,
                'hint' => 'Prazos, formas de pagamento, garantias e dúvidas.',
            ],
            [
                'label' => 'Regras e Guardrails (Proibições)',
                'done' => !empty($this->regras_proibicoes),
                'weight' => 10,
                'hint' => 'O que o agente nunca deve prometer ou fazer.',
            ],
            [
                'label' => 'Horário de Agendamento',
                'done' => $hasHours,
                'weight' => 5,
                'hint' => 'Dias e horários abertos para agendamentos no WhatsApp.',
            ],
        ];

        $score = 0;
        foreach ($items as $item) {
            if ($item['done']) {
                $score += $item['weight'];
            }
        }

        $level = match (true) {
            $score >= 85 => 'Especialista (Pronto para Alta Conversão)',
            $score >= 60 => 'Avançado (Bom Nível de Contexto)',
            $score >= 35 => 'Intermediário (Complete Políticas e FAQ)',
            default => 'Iniciante (Preencha os Fundamentos)',
        };

        return [
            'score' => min(100, $score),
            'level' => $level,
            'items' => $items,
        ];
    }

    /**
     * Templates inteligentes por segmento para preenchimento rápido em 1 clique.
     */
    public static function getSegmentQuickTemplates(): array
    {
        return [
            'hamburgueria' => [
                'tom_de_voz' => 'Ágil, simpático e apetitoso. Respostas diretas e cordiais para fechar o pedido rápido.',
                'diferenciais' => "• Ingredientes selecionados e preparo fresco na hora do pedido.\n• Embalagem térmica especial para chegar quentinho.\n• Entrega rápida com motoboy próprio.",
                'processo_producao' => "Todos os pedidos são preparados na hora assim que confirmados. O tempo médio de preparo e entrega é de 35 a 50 minutos (ou 20 minutos para retirada no balcão).",
                'politicas' => "• Pagamento via PIX, Cartão de Crédito/Débito (levamos maquininha) ou Dinheiro.\n• Alterações ou cancelamentos em até 5 minutos após o pedido.\n• Se houver qualquer problema na entrega, resolvemos imediatamente.",
                'faq_custom' => "P: Qual a taxa de entrega?\nR: A taxa varia conforme o bairro. Me informe seu endereço que já calculo para você!\n\nP: Vocês têm opção vegetariana ou sem bacon?\nR: Sim! Podemos adaptar qualquer item ou tirar ingredientes conforme sua preferência.",
                'regras_proibicoes' => "• Nunca prometer entrega abaixo de 25 minutos.\n• Nunca conceder desconto ou frete grátis sem autorização expressa na tabela.\n• Sempre confirmar endereço completo e forma de pagamento antes de finalizar.",
            ],
            'barbearia_salao' => [
                'tom_de_voz' => 'Acolhedor, estiloso e organizado. Transmite confiança e valoriza a pontualidade.',
                'diferenciais' => "• Profissionais especialistas em visagismo e atendimento personalizado.\n• Ambiente climatizado, produtos de primeira linha e pontualidade.\n• Lembrete automático no WhatsApp antes do horário.",
                'processo_producao' => "Trabalhamos com hora marcada para garantir atendimento sem fila de espera. O cliente escolhe o serviço, o profissional de preferência e recebe confirmação imediata na agenda.",
                'politicas' => "• Tolerância de atraso de até 10 minutos para não impactar o próximo cliente.\n• Cancelamentos ou remarcações devem ser avisados com pelo menos 2 horas de antecedência.\n• Aceitamos PIX, cartões de débito/crédito e dinheiro.",
                'faq_custom' => "P: Atendem por ordem de chegada ou só agendado?\nR: Priorizamos horários agendados para você não esperar, mas se houver vaga no dia encaixamos você na hora!\n\nP: Tem estacionamento no local?\nR: Sim, temos facilidade para estacionar bem em frente.",
                'regras_proibicoes' => "• Nunca agendar fora do horário comercial configurado.\n• Sempre confirmar o serviço e o horário com o cliente antes de gravar na agenda.\n• Nunca inventar preços que não estejam no catálogo.",
            ],
            'clinica_consultorio' => [
                'tom_de_voz' => 'Empático, ético, respeitoso e sereno. Transmite segurança e cuidado com o paciente.',
                'diferenciais' => "• Atendimento humanizado com avaliação detalhada e individualizada.\n• Estrutura moderna, biossegurança rigorosa e equipamentos de ponta.\n• Acompanhamento próximo pré e pós-atendimento.",
                'processo_producao' => "O primeiro passo é agendar uma avaliação ou consulta inicial. Na consulta, o especialista analisa o caso detalhadamente e apresenta o plano de tratamento ideal.",
                'politicas' => "• Pedimos confirmação do agendamento com 24h de antecedência.\n• Em caso de imprevisto, avise com antecedência para reagendarmos sem custo.\n• Parcelamos tratamentos no cartão e aceitamos PIX.",
                'faq_custom' => "P: Vocês atendem convênio ou somente particular?\nR: Atendemos particular com emissão de recibo/nota para reembolso e condições especiais de parcelamento.\n\nP: Qual o valor do tratamento completo?\nR: O valor exato de tratamentos personalizados depende da avaliação clínica inicial, mas a consulta de avaliação tem valor fixo.",
                'regras_proibicoes' => "• NUNCA dar diagnóstico médico/clínico, receitar medicamentos ou prometer resultado garantido pelo WhatsApp.\n• Em caso de dor aguda ou urgência, escalar imediatamente para atendimento humano.\n• Nunca passar orçamento fechado de procedimento complexo sem avaliação.",
            ],
            'loja_varejo' => [
                'tom_de_voz' => 'Consultivo, prestativo e entusiasmado. Ajuda o cliente a escolher a melhor opção.',
                'diferenciais' => "• Curadoria de produtos de alta qualidade com pronta-entrega.\n• Atendimento consultivo para ajudar a escolher o tamanho/modelo ideal.\n• Envio rápido por motoboy na região ou Correios/Transportadora.",
                'processo_producao' => "Consultamos a disponibilidade no catálogo em tempo real, separamos o produto na hora e enviamos o link da proposta/pedido ou chave PIX para pagamento rápido.",
                'politicas' => "• Trocas em até 7 dias corridos com etiqueta fixada e sem sinais de uso.\n• Pagamento via PIX com desconto à vista ou parcelado no cartão de crédito.\n• Retirada na loja sem custo ou entrega expressa.",
                'faq_custom' => "P: Vocês entregam hoje mesmo?\nR: Para pedidos confirmados até as 16h na nossa região, enviamos no mesmo dia!\n\nP: Posso reservar uma peça?\nR: Reservamos mediante confirmação do pedido pelo nosso link de orçamento.",
                'regras_proibicoes' => "• Nunca confirmar reserva definitiva por mais de 24h sem pagamento.\n• Nunca alterar os preços oficiais cadastrados no catálogo.\n• Sempre sugerir produtos complementares de forma natural.",
            ],
            'servicos_gerais' => [
                'tom_de_voz' => 'Profissional, transparente, prestativo e focado em solução.',
                'diferenciais' => "• Equipe técnica qualificada, pontual e com garantia em todos os serviços.\n• Orçamento transparente e formalizado digitalmente pelo BruceOS.\n• Atendimento ágil e suporte pós-serviço.",
                'processo_producao' => "Entendemos a necessidade do cliente pelo WhatsApp, consultamos nossa tabela de serviços ou agendamos uma visita técnica/reunião, e enviamos a proposta formal com link de aprovação online.",
                'politicas' => "• Todos os serviços possuem garantia de 90 dias.\n• Pagamento via PIX, boleto ou cartão de crédito conforme aprovado na proposta.\n• Visitas agendadas podem ser remarcadas com até 4h de antecedência.",
                'faq_custom' => "P: Vocês emitem Nota Fiscal e orçamento formal?\nR: Sim! Enviamos a proposta digital detalhada por link/PDF e emitimos Nota Fiscal.\n\nP: Qual o prazo para atendimento?\nR: Temos horários disponíveis já para esta semana na nossa agenda.",
                'regras_proibicoes' => "• Nunca fechar preço de serviço sob medida sem antes levantar os requisitos ou agendar avaliação.\n• Nunca prometer prazos irreais fora da agenda.\n• Se o cliente pedir falar com especialista humano, transferir imediatamente.",
            ],
        ];
    }
}
