@php
    $nomeResponsavel = $pedido->clienteFinal?->nome_responsavel ?: ($pedido->clienteFinal?->nome_empresa ?? 'Cliente');
    $nomeEmissor = $pedido->cliente?->nome_fantasia ?: ($pedido->cliente?->razao_social ?? config('app.name'));
    $emailContato = $pedido->cliente?->email
        ?: ($pedido->cliente?->user?->email
        ?: ($pedido->cliente?->users()->first()?->email ?? null));
@endphp
<x-mail::message>
# Olá, {{ $nomeResponsavel }}

A empresa **{{ $nomeEmissor }}** preparou uma proposta comercial para você.

**Proposta Nº:** #{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}  
**Escopo / Projeto:** {{ $pedido->titulo }}  
**Investimento Total:** R$ {{ number_format($pedido->valor, 2, ',', '.') }}

Para visualizar o detalhamento completo e realizar o seu **Aceite Eletrônico**, clique no botão abaixo:

<x-mail::button :url="route('public.pedido.show', $pedido->token_publico)">
Visualizar e Aprovar Proposta
</x-mail::button>

@if($emailContato)
Em caso de dúvidas, entre em contato diretamente com **{{ $nomeEmissor }}** pelo e-mail: {{ $emailContato }}.
@endif

Atenciosamente,<br>
**{{ $nomeEmissor }}**
</x-mail::message>
