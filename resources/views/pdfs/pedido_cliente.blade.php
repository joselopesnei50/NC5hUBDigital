<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Proposta #{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 14px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #2d3748;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #1a202c;
        }
        .header p {
            margin: 5px 0 0;
            color: #718096;
            font-size: 14px;
        }
        .info-grid {
            width: 100%;
            margin-bottom: 30px;
        }
        .info-grid td {
            vertical-align: top;
            width: 50%;
        }
        .info-box {
            background-color: #f7fafc;
            padding: 15px;
            border-radius: 5px;
        }
        .info-box h3 {
            margin-top: 0;
            font-size: 12px;
            text-transform: uppercase;
            color: #a0aec0;
            margin-bottom: 10px;
        }
        .info-box p {
            margin: 0 0 5px;
            font-weight: bold;
            color: #2d3748;
        }
        .info-box span {
            font-weight: normal;
            color: #4a5568;
        }
        .section-title {
            font-size: 18px;
            color: #2d3748;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #edf2f7;
            color: #4a5568;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            font-size: 13px;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
            color: #2d3748;
        }
        .text-right {
            text-align: right !important;
        }
        .text-center {
            text-align: center !important;
        }
        .totals-box {
            width: 40%;
            float: right;
            background-color: #f7fafc;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .totals-box .row {
            margin-bottom: 10px;
        }
        .totals-box .row:last-child {
            margin-bottom: 0;
            padding-top: 10px;
            border-top: 1px solid #cbd5e0;
            font-weight: bold;
            font-size: 18px;
            color: #38a169;
        }
        .totals-box .label {
            float: left;
            color: #718096;
        }
        .totals-box .value {
            float: right;
            color: #2d3748;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .approval-box {
            margin-top: 50px;
            background-color: #f0fff4;
            border: 1px solid #c6f6d5;
            padding: 15px;
            border-radius: 5px;
        }
        .approval-box h3 {
            margin-top: 0;
            color: #2f855a;
            font-size: 16px;
        }
        .approval-box p {
            margin: 5px 0;
            font-size: 13px;
            color: #276749;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #a0aec0;
            font-size: 12px;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            background-color: #e2e8f0;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-concluido { background-color: #c6f6d5; color: #22543d; }
        .status-andamento { background-color: #bee3f8; color: #2a4365; }
        .status-pagamento { background-color: #fefcbf; color: #744210; }
        .status-orcamento { background-color: #edf2f7; color: #1a202c; }
        .status-cancelado { background-color: #fed7d7; color: #742a2a; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <table style="width: 100%;">
                <tr>
                    <td>
                        <h1>{{ $pedido->cliente->razao_social }}</h1>
                        <p>Documento: {{ $pedido->cliente->cpf_cnpj ?? 'Não informado' }}</p>
                    </td>
                    <td class="text-right">
                        <h2>Proposta / Pedido</h2>
                        <p>#{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}</p>
                        <p>Data: {{ $pedido->data_pedido ? $pedido->data_pedido->format('d/m/Y') : $pedido->created_at->format('d/m/Y') }}</p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Info Grid -->
        <table class="info-grid">
            <tr>
                <td style="padding-right: 10px;">
                    <div class="info-box">
                        <h3>Preparado Para</h3>
                        <p>{{ $pedido->clienteFinal->nome_empresa }}</p>
                        <p><span>Responsável:</span> {{ $pedido->clienteFinal->nome_responsavel }}</p>
                        <p><span>E-mail:</span> {{ $pedido->clienteFinal->email }}</p>
                    </div>
                </td>
                <td style="padding-left: 10px;">
                    <div class="info-box">
                        <h3>Detalhes do Documento</h3>
                        <p><span>Título:</span> {{ $pedido->titulo }}</p>
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
        <div class="section-title">Itens do Pedido</div>
        
        @if($pedido->descricao)
            <div style="margin-bottom: 20px; font-size: 13px; color: #4a5568; white-space: pre-wrap;">
                {{ $pedido->descricao }}
            </div>
        @endif

        <table class="items-table">
            <thead>
                <tr>
                    <th>Descrição do Item</th>
                    <th class="text-center">Qtd</th>
                    <th class="text-right">V. Unitário</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pedido->itens as $item)
                    <tr>
                        <td>{{ $item->nome_item }}</td>
                        <td class="text-center">{{ number_format($item->quantidade, 2, ',', '') }}</td>
                        <td class="text-right">R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                        <td class="text-right font-bold">R$ {{ number_format($item->valor_total, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="clearfix">
            <div class="totals-box">
                <div class="row clearfix">
                    <span class="label">Subtotal</span>
                    <span class="value">R$ {{ number_format($pedido->valor, 2, ',', '.') }}</span>
                </div>
                <div class="row clearfix" style="border-top: 1px solid #cbd5e0; padding-top: 10px; margin-top: 10px;">
                    <span class="label">Total Final</span>
                    <span class="value">R$ {{ number_format($pedido->valor, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>

        <!-- Aprovação -->
        @if($pedido->nome_aprovacao)
            <div class="approval-box">
                <h3>✓ Aprovado Eletronicamente</h3>
                <p><strong>Por:</strong> {{ $pedido->nome_aprovacao }}</p>
                <p><strong>Data:</strong> {{ $pedido->data_aprovacao ? $pedido->data_aprovacao->format('d/m/Y \à\s H:i') : '' }}</p>
                <p><strong>IP Registrado:</strong> {{ $pedido->ip_aprovacao }}</p>
            </div>
        @endif

        <div class="footer">
            Documento gerado eletronicamente por {{ $pedido->cliente->razao_social }}
        </div>
    </div>
</body>
</html>
