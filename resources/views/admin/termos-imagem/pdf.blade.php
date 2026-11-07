<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Termo de Uso de Imagem #{{ $termo->id }}</title>
<style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10.5px; color: #1a1a2e; line-height: 1.6; margin: 0; padding: 0; }
    .header { background-color: #0A1128; color: #fff; padding: 24px 36px; }
    .header-label { font-size: 9px; font-weight: bold; letter-spacing: 3px; text-transform: uppercase; color: #FF7A1A; margin-bottom: 6px; }
    .header-title { font-size: 18px; font-weight: bold; color: #fff; }
    .header-sub { font-size: 10px; color: #cbd5e1; margin-top: 4px; }
    .body { padding: 26px 36px; }
    .section-title { font-size: 9.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; color: #0A1128; margin-top: 18px; margin-bottom: 10px; border-bottom: 1.5px solid #FF7A1A; padding-bottom: 4px; }
    .parties { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
    .parties td { padding: 10px; vertical-align: top; width: 50%; background: #fafafa; border: 1px solid #e5e7eb; border-radius: 4px; }
    .party-label { font-size: 8px; font-weight: bold; text-transform: uppercase; color: #8A8F9C; letter-spacing: 1px; margin-bottom: 4px; }
    .party-name { font-size: 11px; font-weight: bold; color: #0A1128; margin-bottom: 3px; }
    .party-info { font-size: 9.5px; color: #475569; line-height: 1.5; }
    .info-grid { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
    .info-grid td { padding: 4px 0; vertical-align: top; width: 33%; }
    .info-label { font-size: 8px; font-weight: bold; text-transform: uppercase; color: #8A8F9C; letter-spacing: 1px; }
    .info-value { font-size: 10.5px; font-weight: bold; color: #0A1128; }
    .content-box { border: 1px solid #e5e7eb; border-radius: 6px; padding: 16px 18px; margin-bottom: 20px; background: #fafafa; white-space: pre-line; text-align: justify; font-size: 10px; line-height: 1.65; }
    .sig-section { border-top: 2px solid #0A1128; padding-top: 16px; margin-top: 22px; }
    .sig-badge { display: inline-block; background: #dcfce7; color: #166534; font-size: 9px; font-weight: bold; padding: 3px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
    .pending-badge { display: inline-block; background: #fff7ed; color: #9a3412; font-size: 9px; font-weight: bold; padding: 3px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: 1px; }
    .cancelled-badge { display: inline-block; background: #ffe4e6; color: #9f1239; font-size: 9px; font-weight: bold; padding: 3px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: 1px; }
    .sig-info { font-size: 9.5px; color: #334155; margin-bottom: 3px; }
    .sig-img { max-width: 220px; max-height: 95px; border: 1px solid #e5e7eb; border-radius: 4px; padding: 6px; background: #fff; margin-top: 8px; }
    .footer { margin-top: 28px; padding-top: 12px; border-top: 1px solid #e5e7eb; font-size: 8px; color: #8A8F9C; text-align: center; line-height: 1.5; }
</style>
</head>
<body>

<div class="header">
    <div class="header-label">NC5 Hub Digital · Autorização de Uso de Imagem e Voz</div>
    <div class="header-title">{{ $termo->titulo }}</div>
    <div class="header-sub">Termo nº {{ str_pad($termo->id, 5, '0', STR_PAD_LEFT) }} · Emitido em {{ $termo->created_at?->format('d/m/Y') }}</div>
</div>

<div class="body">

    <div class="section-title" style="margin-top:0;">Identificação das Partes</div>
    <table class="parties">
        <tr>
            <td>
                <div class="party-label">Autorizada / Produtora</div>
                <div class="party-name">NC5 Hub Digital</div>
                <div class="party-info">
                    CNPJ: {{ config('services.nc5.cnpj', '—') }}<br>
                    Endereço: {{ config('services.nc5.endereco', '—') }}<br>
                    nc5hubdigital.com.br
                    @if($termo->cliente)
                        <br><br>
                        <strong>Cliente Vinculado:</strong> {{ $termo->cliente->razao_social }}
                        @if($termo->cliente->cpf_cnpj) ({{ $termo->cliente->cpf_cnpj }}) @endif
                    @endif
                </div>
            </td>
            <td>
                <div class="party-label">Cedente {{ $termo->cedente_menor ? '(Menor de Idade)' : '' }}</div>
                <div class="party-name">{{ $termo->cedente_nome }}</div>
                <div class="party-info">
                    CPF: {{ $termo->cedente_cpf }}<br>
                    @if($termo->cedente_rg) RG: {{ $termo->cedente_rg }}<br> @endif
                    @if($termo->cedente_endereco) Endereço: {{ $termo->cedente_endereco }}<br> @endif
                    @if($termo->cedente_email) E-mail: {{ $termo->cedente_email }}<br> @endif
                    @if($termo->cedente_telefone) Telefone: {{ $termo->cedente_telefone }}<br> @endif

                    @if($termo->cedente_menor)
                        <br>
                        <strong>Responsável Legal ({{ $termo->responsavel_parentesco }}):</strong><br>
                        {{ $termo->responsavel_nome }} — CPF: {{ $termo->responsavel_cpf }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <table class="info-grid">
        <tr>
            <td>
                <div class="info-label">Projeto / Gravação</div>
                <div class="info-value">{{ $termo->projeto_referencia ?: '—' }}</div>
            </td>
            <td>
                <div class="info-label">Data da Captação</div>
                <div class="info-value">{{ $termo->data_captacao?->format('d/m/Y') ?: '—' }}</div>
            </td>
            <td>
                <div class="info-label">Local da Captação</div>
                <div class="info-value">{{ $termo->local_captacao ?: '—' }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Termo de Autorização</div>
    <div class="content-box">{{ $termo->conteudo }}</div>

    <div class="sig-section">
        <div class="section-title" style="margin-top:0;">Registro de Assinatura Eletrônica</div>

        @if($termo->status === 'assinado' && $termo->assinatura_dados)
            @php $sig = $termo->assinatura_array; @endphp
            <div class="sig-badge">✓ Assinado Eletronicamente em {{ $termo->assinado_em?->format('d/m/Y H:i:s') }}</div>

            <div style="margin-top:6px;">
                <div class="sig-info"><strong>Assinado por:</strong> {{ $sig['nome'] ?? '—' }} @if($termo->cedente_menor) (Responsável Legal — {{ $termo->responsavel_parentesco }}) @endif</div>
                <div class="sig-info"><strong>CPF / Documento:</strong> {{ $sig['documento'] ?? '—' }}</div>
                <div class="sig-info"><strong>Data e Hora (Servidor):</strong> {{ $termo->assinado_em?->format('d/m/Y H:i:s') ?? ($sig['timestamp'] ?? '—') }}</div>
                <div class="sig-info"><strong>Endereço IP:</strong> {{ $sig['ip'] ?? '—' }}</div>
                <div class="sig-info"><strong>User Agent:</strong> {{ \Illuminate\Support\Str::limit($sig['user_agent'] ?? '—', 95) }}</div>
                @if($termo->conteudo_hash)
                    <div class="sig-info"><strong>Hash SHA-256 (Integridade):</strong> <span style="font-family:monospace;font-size:8.5px;">{{ $termo->conteudo_hash }}</span></div>
                @endif
            </div>

            @if(!empty($sig['signature_image']))
                <div style="margin-top:10px;">
                    <div class="sig-info"><strong>Assinatura manuscrita:</strong></div>
                    @if(extension_loaded('gd') || extension_loaded('imagick'))
                        <img src="{{ $sig['signature_image'] }}" class="sig-img" alt="Assinatura">
                    @else
                        <div class="sig-info" style="font-style:italic;color:#8A8F9C;">
                            Assinatura manuscrita registrada no sistema (extensão GD ausente para renderização neste PDF).
                        </div>
                    @endif
                </div>
            @endif
        @elseif($termo->status === 'cancelado')
            <div class="cancelled-badge">Termo Cancelado</div>
        @else
            <div class="pending-badge">Aguardando assinatura</div>
            <div style="margin-top:28px;border-top:1px solid #0A1128;padding-top:6px;width:260px;">
                <div style="font-size:9px;color:#0A1128;font-weight:bold;">{{ $termo->nome_assinante_esperado }}</div>
                <div style="font-size:8.5px;color:#8A8F9C;">CPF: {{ $termo->cpf_assinante_esperado }}</div>
            </div>
        @endif
    </div>

</div>

<div class="footer">
    NC5 Hub Digital · Gerado em {{ now()->format('d/m/Y H:i') }} · nc5hubdigital.com.br<br>
    @if($termo->conteudo_hash)
        Hash SHA-256 de integridade do conteúdo: {{ $termo->conteudo_hash }}
    @else
        Documento pendente de assinatura eletrônica.
    @endif
</div>

</body>
</html>
