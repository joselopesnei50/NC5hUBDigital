<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    use HasFactory;

    protected $table = 'landing_pages';

    public const TEMPLATES = [
        'corporativo' => [
            'nome' => 'Corporativo & Consultoria Premium',
            'subtitulo' => 'Escritórios, contabilidade, advocacia, engenharia e consultorias B2B',
            'descricao' => 'Layout sóbrio e elegante focado em autoridade, números de impacto, áreas de atuação e formulário executivo.',
            'badge' => 'Autoridade B2B',
        ],
        'servicos_locais' => [
            'nome' => 'Negócios Locais & Atendimento Rápido',
            'subtitulo' => 'Clínicas, estética, odontologia, oficinas, assistência técnica e salões',
            'descricao' => 'Destaque imediato para chamada no WhatsApp, horários de funcionamento, endereço, serviços e avaliações 5 estrelas.',
            'badge' => 'Negócios Locais',
        ],
        'catalogo_vitrine' => [
            'nome' => 'Vitrine de Produtos & Pedidos no WhatsApp',
            'subtitulo' => 'Lojas, catálogos, distribuidoras e serviços tabelados',
            'descricao' => 'Grade visual de produtos e serviços com preços e botão "Pedir no WhatsApp" que já envia o item escolhido.',
            'badge' => 'Catálogo & Vendas',
        ],
        'captura_alta_conversao' => [
            'nome' => 'Captura de Leads (Alta Conversão)',
            'subtitulo' => 'Tráfego pago (Google/Meta Ads), energia solar, imobiliárias e seguros',
            'descricao' => 'Topo dividido com formulário de orçamento acima da dobra, benefícios diretos, prova social e quebra de objeções (FAQ).',
            'badge' => 'Foco em Leads',
        ],
    ];

    protected $fillable = [
        'cliente_id',
        'slug',
        'template',
        'is_published',
        'titulo_pagina',
        'meta_descricao',
        'cor_primaria',
        'cor_secundaria',
        'logo_path',
        'hero_image_path',
        'hero_badge',
        'hero_titulo',
        'hero_subtitulo',
        'hero_cta_texto',
        'sobre_titulo',
        'sobre_texto',
        'servicos_json',
        'diferenciais_json',
        'depoimentos_json',
        'faq_json',
        'whatsapp_numero',
        'telefone',
        'email_contato',
        'endereco_texto',
        'horario_atendimento',
        'instagram_url',
        'facebook_url',
        'google_maps_embed',
        'views_count',
        'whatsapp_clicks_count',
        'leads_count',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'servicos_json' => 'array',
        'diferenciais_json' => 'array',
        'depoimentos_json' => 'array',
        'faq_json' => 'array',
        'views_count' => 'integer',
        'whatsapp_clicks_count' => 'integer',
        'leads_count' => 'integer',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Retorna o número de WhatsApp apenas com dígitos (com DDI 55 se omitido) para links wa.me.
     */
    public function whatsappLimpo(): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $this->whatsapp_numero);
        if (!$digits) {
            return null;
        }
        if (strlen($digits) >= 10 && strlen($digits) <= 11 && !str_starts_with($digits, '55')) {
            return '55' . $digits;
        }
        return $digits;
    }

    /**
     * Monta URL wa.me com texto customizado.
     */
    public function whatsappUrl(?string $mensagem = null): ?string
    {
        $num = $this->whatsappLimpo();
        if (!$num) {
            return null;
        }
        $msg = $mensagem ?: "Olá! Vim pelo site {$this->titulo_pagina} e gostaria de mais informações.";
        return 'https://wa.me/' . $num . '?text=' . rawurlencode($msg);
    }

    public function publicUrl(): string
    {
        return route('public.landing-page.show', ['slug' => $this->slug]);
    }
}
