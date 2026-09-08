<x-mail::message>
# Olá, {{ $pedido->clienteFinal->nome_responsavel }}

A empresa **{{ $pedido->cliente->razao_social }}** enviou uma proposta / pedido para você.

**Referência:** {{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}  
**Serviço / Produto:** {{ $pedido->titulo }}  
**Valor Total:** R$ {{ number_format($pedido->valor, 2, ',', '.') }}

Para visualizar os detalhes completos e dar o seu Aceite Eletrônico, clique no botão abaixo:

<x-mail::button :url="route('public.pedido.show', $pedido->token_publico)">
Visualizar Proposta
</x-mail::button>

Se você tiver alguma dúvida, entre em contato diretamente com a empresa através do e-mail: {{ $pedido->cliente->user->email }}.

Atenciosamente,<br>
{{ config('app.name') }}
</x-mail::message>
