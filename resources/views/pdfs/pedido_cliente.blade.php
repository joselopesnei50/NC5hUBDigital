<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Proposta #{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #0A1128;
            font-size: 13px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 24px;
        }
        .header {
            background-color: #0A1128;
            color: #ffffff;
            padding: 24px;
            border-radius: 8px;
            margin-bottom: 24px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            color: #ffffff;
        }
        .header p {
            margin: 4px 0 0;
            color: #cbd5e1;
            font-size: 12px;
        }
        .header .badge-top {
            color: #FF7A1A;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .info-grid {
            width: 100%;
            margin-bottom: 24px;
            border-collapse: collapse;
        }
        .info-grid td {
            vertical-align: top;
            width: 50%;
        }
        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 14px;
            border-radius: 6px;
        }
        .info-box h3 {
            margin-top: 0;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #FF7A1A;
            margin-bottom: 8px;
        }
        .info-box p {
            margin: 0 0 4px;
            font-weight: bold;
            color: #0A1128;
            font-size: 13px;
        }
        .info-box span {
            font-weight: normal;
            color: #475569;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #0A1128;
            border-bottom: 2px solid #0A1128;
            padding-bottom: 6px;
            margin-bottom: 14px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #0A1128;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 10px 12px;
            font-size: 11px;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
            color: #0A1128;
        }
        .text-right {
            text-align: right !important;
        }
        .text-center {
            text-align: center !important;
        }
        .totals-box {
            width: 42%;
            float: right;
            background-color: #0A1128;
            color: #ffffff;
            padding: 16px;
            border-radius: 6px;
            margin-bottom: 30px;
        }
        .totals-box .row {
            margin-bottom: 8px;
        }
        .totals-box .row:last-child {
            margin-bottom: 0;
            padding-top: 10px;
            border-top: 1px solid #334155;
            font-weight: bold;
            font-size: 16px;
            color: #FF7A1A;
        }
        .totals-box .label {
            float: left;
            color: #cbd5e1;
        }
        .totals-box .value {
            float: right;
            color: #ffffff;
            font-weight: bold;
        }
        .totals-box .row:last-child .value {
            color: #FF7A1A;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .approval-box {
            margin-top: 30px;
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            padding: 14px;
            border-radius: 6px;
        }
        .approval-box h3 {
            margin-top: 0;
            color: #065f46;
            font-size: 14px;
        }
        .approval-box p {
            margin: 4px 0;
            font-size: 12px;
            color: #047857;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #94a3b8;
            font-size: 11px;
            border-top: 1px solid #e2e8f0;
            padding-top: 16px;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            background-color: #e2e8f0;
            border-radius: 10px;
            font-size: 11px;
            font-weight: bold;
        }
        .status-concluido { background-color: #d1fae5; color: #065f46; }
        .status-andamento { background-color: #dbeafe; color: #1e40af; }
        .status-pagamento { background-color: #fef3c7; color: #92400e; }
        .status-orcamento { background-color: #f1f5f9; color: #0A1128; }
        .status-cancelado { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    @php
        $nomeEmissor = $pedido->cliente?->nome_fantasia ?: ($pedido->cliente?->razao_social ?? 'Empresa');
        $nomeClienteFinal = $pedido->clienteFinal?->nome_empresa ?: ($pedido->clienteFinal?->nome_responsavel ?? 'Cliente');
    @endphp
    <div class="container">
        <!-- Header -->
        <div class="header">
            <table style="width: 100%;">
                <tr>
                    <td>
                        <div class="badge-top">Proposta Comercial</div>
                        <h1>{{ $nomeEmissor }}</h1>
                        @if($pedido->cliente?->cpf_cnpj)
                            <p>CNPJ / Documento: {{ $pedido->cliente->cpf_cnpj }}</p>
                        @endif
                    </td>
                    <td class="text-right">
                        <div class="badge-top">Referência</div>
                        <h1 style="font-size: 20px;">#{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}</h1>
                        <p>Data: {{ $pedido->data_pedido ? $pedido->data_pedido->format('d/m/Y') : $pedido->created_at?->format('d/m/Y') }}</p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Info Grid -->
        <table class="info-grid">
            <tr>
                <td style="padding-right: 8px;">
                    <div class="info-box">
                        <h3>Preparado Para</h3>
                        <p>{{ $nomeClienteFinal }}</p>
                        @if($pedido->clienteFinal?->nome_responsavel)
                            <p><span>Responsável:</span> {{ $pedido->clienteFinal->nome_responsavel }}</p>
                        @endif
                        @if($pedido->clienteFinal?->email)
                            <p><span>E-mail:</span> {{ $pedido->clienteFinal->email }}</p>
                        @endif
                        @if($pedido->clienteFinal?->telefone)
                            <p><span>Telefone:</span> {{ $pedido->clienteFinal->telefone }}</p>
                        @endif
                    </div>
                </td>
                <td style="padding-left: 8px;">
                    <div class="info-box">
                        <h3>Resumo do Escopo</h3>
                        <p><span>Projeto:</span> {{ $pedido->titulo }}</p>
                        @if($pedido->data_entrega)
                            <p><span>Previsão de Entrega:</span> {{ $pedido->data_entrega->format('d/m/Y') }}</p>
                        @endif
                        <p>
                            <span>Status:</span>
                            @php
                                $statusClass = 'status-orcamento';
                                if($pedido->status == 'Concluído') $statusClass = 'status-concluido';
                                if($pedido->status == 'Em Andamento') $statusClass = 'status-andamento';
                                if($pedido->status == 'Aguardando Pagamento') $statusClass = 'status-pagamento';
                                if($pedido->status == 'Cancelado') $statusClass = 'status-cancelado';
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ $pedido->status }}</span>
                        </p>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Escopo -->
        <div class="section-title">Detalhamento dos Itens</div>

        @if($pedido->descricao)
            <div style="margin-bottom: 18px; font-size: 12px; color: #334155; white-space: pre-wrap; background: #f8fafc; padding: 12px; border-radius: 6px; border: 1px solid #e2e8f0;">{{ $pedido->descricao }}</div>
        @endif

        <table class="items-table">
            <thead>
                <tr>
                    <th>Descrição do Item</th>
                    <th class="text-center">Qtd</th>
                    <th class="text-right">V. Unitário</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pedido->itens as $item)
                    <tr>
                        <td><strong>{{ $item->nome_item }}</strong></td>
                        <td class="text-center">{{ rtrim(rtrim(number_format($item->quantidade, 2, ',', '.'), '0'), ',') }}</td>
                        <td class="text-right">R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                        <td class="text-right"><strong>R$ {{ number_format($item->valor_total, 2, ',', '.') }}</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td><strong>{{ $pedido->titulo }}</strong></td>
                        <td class="text-center">1</td>
                        <td class="text-right">R$ {{ number_format($pedido->valor, 2, ',', '.') }}</td>
                        <td class="text-right"><strong>R$ {{ number_format($pedido->valor, 2, ',', '.') }}</strong></td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="clearfix">
            <div class="totals-box">
                <div class="row clearfix">
                    <span class="label">Subtotal</span>
                    <span class="value">R$ {{ number_format($pedido->valor, 2, ',', '.') }}</span>
                </div>
                <div class="row clearfix">
                    <span class="label">Total do Investimento</span>
                    <span class="value">R$ {{ number_format($pedido->valor, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>

        <!-- Aprovação -->
        @if($pedido->nome_aprovacao)
            <div class="approval-box">
                <h3>✓ Proposta Aprovada Eletronicamente</h3>
                <p><strong>Assinado por:</strong> {{ $pedido->nome_aprovacao }}</p>
                <p><strong>Data e Hora:</strong> {{ $pedido->data_aprovacao ? $pedido->data_aprovacao->format('d/m/Y \à\s H:i') : 'Registrado' }}</p>
                <p><strong>IP de Validação:</strong> {{ $pedido->ip_aprovacao ?? '-' }}</p>
            </div>
        @endif

        <div class="footer">
            Documento emitido eletronicamente por <strong>{{ $nomeEmissor }}</strong> · Tecnologia BruceOS
        </div>
    </div>
</body>
</html>
