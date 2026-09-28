<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Google Meu Negócio — {{ $cliente->razao_social ?? 'Relatório' }}</title>
    <style>
        @page { margin: 20mm 15mm; }
        body { font-family: DejaVu Sans, sans-serif; color: #0A1128; font-size: 11px; line-height: 1.4; }
        h1 { font-size: 20px; margin: 0 0 4px 0; }
        h2 { font-size: 14px; margin: 20px 0 8px 0; padding-bottom: 4px; border-bottom: 2px solid #FF7A1A; color: #0A1128; }
        h3 { font-size: 12px; margin: 12px 0 6px 0; color: #0A1128; }
        .header { border-bottom: 1px solid #E5E7EB; padding-bottom: 12px; margin-bottom: 16px; }
        .muted { color: #64748B; font-size: 10px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-bruce { background: #FFF0E5; color: #FF7A1A; }
        .badge-emerald { background: #ECFDF5; color: #059669; }
        .badge-rose { background: #FFF1F2; color: #E11D48; }
        .badge-slate { background: #F1F5F9; color: #475569; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th, td { text-align: left; padding: 6px 8px; border-bottom: 1px solid #E5E7EB; vertical-align: top; }
        th { font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748B; background: #F8FAFC; }
        .num { text-align: right; font-weight: bold; }
        .cards { display: block; margin-bottom: 12px; }
        .card { display: inline-block; width: 22%; margin-right: 2%; padding: 8px; background: #F8FAFC; border-radius: 6px; vertical-align: top; }
        .card:last-child { margin-right: 0; }
        .card-label { font-size: 9px; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; }
        .card-value { font-size: 18px; font-weight: bold; margin-top: 4px; }
        .stars { color: #F59E0B; }
        .stars-empty { color: #E5E7EB; }
        .review-block { padding: 8px; border: 1px solid #E5E7EB; border-radius: 6px; margin-bottom: 8px; page-break-inside: avoid; }
        .review-reply { margin-top: 6px; padding: 6px 8px; background: #F8FAFC; border-radius: 4px; font-size: 10px; }
        .footer { margin-top: 20px; padding-top: 8px; border-top: 1px solid #E5E7EB; font-size: 9px; color: #64748B; text-align: center; }
    </style>
</head>
<body>

@php
    $atual    = $metricas['atual']    ?? [];
    $anterior = $metricas['anterior'] ?? [];
    $delta    = $metricas['delta']    ?? [];

    $formatDelta = function ($d) {
        if ($d === null) return ['badge-emerald', 'novo'];
        if ($d > 0)      return ['badge-emerald', '+' . number_format($d, 1, ',', '.') . '%'];
        if ($d < 0)      return ['badge-rose',    number_format($d, 1, ',', '.') . '%'];
        return ['badge-slate', '0%'];
    };
@endphp

<div class="header">
    <h1>Google Meu Negócio</h1>
    <div class="muted">{{ $cliente->razao_social ?? '—' }} · Gerado em {{ $geradoEm->format('d/m/Y H:i') }}</div>
    @if(!empty($atual['periodo']))
        <div class="muted">Período: {{ \Carbon\Carbon::parse($atual['periodo']['inicio'])->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($atual['periodo']['fim'])->format('d/m/Y') }}</div>
    @endif
</div>

<h2>Desempenho nos últimos 30 dias</h2>
<div class="cards">
    @php
        $cardsData = [
            ['Visualizações',   $atual['impressoes']   ?? 0, $delta['impressoes']   ?? 0],
            ['Cliques no site', $atual['cliques_site'] ?? 0, $delta['cliques_site'] ?? 0],
            ['Ligações',        $atual['ligacoes']     ?? 0, $delta['ligacoes']     ?? 0],
            ['Pedidos de rota', $atual['rotas']        ?? 0, $delta['rotas']        ?? 0],
        ];
    @endphp
    @foreach($cardsData as $c)
        @php [$badge, $txt] = $formatDelta($c[2]); @endphp
        <div class="card">
            <div class="card-label">{{ $c[0] }}</div>
            <div class="card-value">{{ number_format($c[1], 0, ',', '.') }}</div>
            <div style="margin-top: 4px;"><span class="badge {{ $badge }}">{{ $txt }}</span></div>
        </div>
    @endforeach
</div>

<h3>Distribuição</h3>
<table>
    <tr><th>Origem</th><th style="text-align: right;">Impressões</th></tr>
    <tr><td>Busca</td>   <td class="num">{{ number_format($atual['impressoes_busca']   ?? 0, 0, ',', '.') }}</td></tr>
    <tr><td>Maps</td>    <td class="num">{{ number_format($atual['impressoes_maps']    ?? 0, 0, ',', '.') }}</td></tr>
    <tr><td>Mobile</td>  <td class="num">{{ number_format($atual['impressoes_mobile']  ?? 0, 0, ',', '.') }}</td></tr>
    <tr><td>Desktop</td> <td class="num">{{ number_format($atual['impressoes_desktop'] ?? 0, 0, ',', '.') }}</td></tr>
</table>

<h2>Publicações recentes</h2>
@forelse($posts as $p)
    @php
        $state = $p['state'] ?? 'LIVE';
        $badgeMap = ['LIVE' => ['badge-emerald', 'Publicado'], 'REJECTED' => ['badge-rose', 'Recusado'], 'PROCESSING' => ['badge-slate', 'Processando']];
        [$b, $txt] = $badgeMap[$state] ?? ['badge-slate', $state];
    @endphp
    <div class="review-block">
        <div>
            <span class="badge {{ $b }}">{{ $txt }}</span>
            @if(!empty($p['createTime']))
                <span class="muted">{{ \Carbon\Carbon::parse($p['createTime'])->format('d/m/Y H:i') }}</span>
            @endif
        </div>
        <div style="margin-top: 4px;">{{ \Illuminate\Support\Str::limit($p['summary'] ?? '', 400) }}</div>
    </div>
@empty
    <div class="muted">Nenhuma publicação nos últimos 30 dias.</div>
@endforelse

<h2>Avaliações</h2>
<div>
    <span class="badge badge-bruce">Média {{ number_format((float) ($reviews['media'] ?? 0), 1, ',', '.') }}</span>
    <span class="muted">em {{ number_format($reviews['total'] ?? 0, 0, ',', '.') }} avaliação(ões)</span>
</div>

@forelse(($reviews['reviews'] ?? []) as $r)
    @php
        $estrelas = \App\Services\GoogleBusinessProfileService::estrelas($r['starRating'] ?? null);
        $reply = $r['reviewReply']['comment'] ?? null;
    @endphp
    <div class="review-block">
        <div>
            <span class="stars">{{ str_repeat('★', $estrelas) }}</span><span class="stars-empty">{{ str_repeat('★', max(0, 5 - $estrelas)) }}</span>
            <strong>{{ $r['reviewer']['displayName'] ?? 'Cliente' }}</strong>
            @if(!empty($r['updateTime']))
                <span class="muted">· {{ \Carbon\Carbon::parse($r['updateTime'])->format('d/m/Y') }}</span>
            @endif
        </div>
        @if(!empty($r['comment']))
            <div style="margin-top: 4px;">{{ $r['comment'] }}</div>
        @else
            <div class="muted" style="margin-top: 4px;">(sem comentário)</div>
        @endif
        @if($reply)
            <div class="review-reply">
                <strong>Sua resposta:</strong> {{ $reply }}
            </div>
        @endif
    </div>
@empty
    <div class="muted">Nenhuma avaliação registrada.</div>
@endforelse

<div class="footer">
    Relatório gerado pelo NC5 HUB · Dados via Google Business Profile API · Atraso natural de 2-3 dias nos dados de desempenho.
</div>

</body>
</html>
