<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Contrato #{{ $contrato->id }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #1a1a2e; line-height: 1.6; }
    .header { background: #0A1128; color: #fff; padding: 28px 40px; }
    .header-label { font-size: 9px; font-weight: bold; letter-spacing: 3px; text-transform: uppercase; color: #E63888; margin-bottom: 6px; }
    .header-title { font-size: 22px; font-weight: bold; color: #fff; }
    .body { padding: 32px 40px; }
    .section-title { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; color: #8A8F9C; margin-bottom: 10px; border-bottom: 1px solid #e5e7eb; padding-bottom: 6px; }
    .info-grid { width: 100%; border-collapse: collapse; margin-bottom: 28px; }
    .info-grid td { padding: 6px 0; vertical-align: top; width: 50%; }
    .info-label { font-size: 9px; font-weight: bold; text-transform: uppercase; color: #8A8F9C; letter-spacing: 1px; }
    .info-value { font-size: 12px; font-weight: bold; color: #0A1128; }
    .content-box { border: 1px solid #e5e7eb; border-radius: 6px; padding: 20px; margin-bottom: 28px; background: #fafafa; }
    .content-box p { margin-bottom: 8px; }
    .content-box h1, .content-box h2, .content-box h3 { margin-bottom: 8px; margin-top: 12px; }
    .content-box ul, .content-box ol { padding-left: 20px; margin-bottom: 8px; }
    .sig-section { border-top: 2px solid #0A1128; padding-top: 20px; margin-top: 28px; }
    .sig-badge { display: inline-block; background: #dcfce7; color: #166534; font-size: 9px; font-weight: bold; padding: 3px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
    .sig-info { font-size: 10px; color: #555; margin-bottom: 4px; }
    .sig-img { max-width: 220px; border: 1px solid #e5e7eb; border-radius: 4px; padding: 6px; background: #fff; margin-top: 10px; }
    .pending-badge { display: inline-block; background: #fff7ed; color: #9a3412; font-size: 9px; font-weight: bold; padding: 3px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: 1px; }
    .footer { margin-top: 40px; padding-top: 16px; border-top: 1px solid #e5e7eb; font-size: 9px; color: #8A8F9C; text-align: center; }
</style>
</head>
<body>

<div class="header">
    <div class="header-label">NC5 Hub Digital — Contrato de Prestação de Serviços</div>
    <div class="header-title">Contrato #{{ $contrato->id }}</div>
</div>

<div class="body">

    <div class="section-title" style="margin-top:0;">Dados do Contrato</div>
    <table class="info-grid">
        <tr>
            <td>
                <div class="info-label">Cliente</div>
                <div class="info-value">{{ $contrato->cliente->razao_social ?? '—' }}</div>
            </td>
            <td>
                <div class="info-label">Serviço</div>
                <div class="info-value">{{ $contrato->servico->nome ?? 'Contrato Avulso' }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="info-label">Data de Início</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($contrato->data_inicio)->format('d/m/Y') }}</div>
            </td>
            <td>
                <div class="info-label">Data de Término</div>
                <div class="info-value">{{ $contrato->data_fim ? \Carbon\Carbon::parse($contrato->data_fim)->format('d/m/Y') : 'Vigente' }}</div>
            </td>
        </tr>
        @if($contrato->servico)
        <tr>
            <td>
                <div class="info-label">Valor</div>
                <div class="info-value">R$ {{ number_format($contrato->servico->preco, 2, ',', '.') }}</div>
            </td>
            <td></td>
        </tr>
        @endif
    </table>

    @if($contrato->conteudo)
        <div class="section-title">Termos e Condições</div>
        <div class="content-box">
            {!! $contrato->conteudo !!}
        </div>
    @endif

    <div class="sig-section">
        <div class="section-title">Assinatura</div>

        @if($contrato->status_assinatura === 'assinado' && $contrato->assinatura_url)
            @php $sig = json_decode($contrato->assinatura_url, true); @endphp
            <div class="sig-badge">✓ Assinado Eletronicamente</div>
            <div style="margin-top:8px;">
                <div class="sig-info"><strong>Data/Hora:</strong> {{ $sig['timestamp'] ?? '—' }}</div>
                <div class="sig-info"><strong>IP:</strong> {{ $sig['ip'] ?? '—' }}</div>
                <div class="sig-info"><strong>Dispositivo:</strong> {{ Str::limit($sig['user_agent'] ?? '—', 80) }}</div>
            </div>
            @if(!empty($sig['signature_image']))
                <br>
                <div class="sig-info"><strong>Assinatura manuscrita:</strong></div>
                <img src="{{ $sig['signature_image'] }}" class="sig-img" alt="Assinatura">
            @endif
            <p style="margin-top:14px;font-size:10px;color:#555;">
                Este documento foi assinado eletronicamente. O registro acima constitui prova legal de aceite dos termos.
            </p>
        @else
            <div class="pending-badge">Aguardando assinatura</div>
            <div style="margin-top:20px;border-top:1px solid #0A1128;padding-top:8px;width:260px;">
                <div style="font-size:9px;color:#8A8F9C;">Assinatura do Contratante</div>
            </div>
        @endif
    </div>

</div>

<div class="footer">
    NC5 Hub Digital · Gerado em {{ now()->format('d/m/Y H:i') }} · nc5hubdigital.com.br
</div>

</body>
</html>
