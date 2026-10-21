<?php

declare(strict_types=1);

namespace App\Agent\Chat\Tools;

use App\Agent\Chat\Contracts\ToolInterface;
use App\Models\LancamentoFinanceiro;
use Illuminate\Support\Carbon;

class CreateFinancialEntryTool implements ToolInterface
{
    public function getName(): string
    {
        return 'create_financial_entry';
    }

    public function getDescription(): string
    {
        return 'Registra um lançamento financeiro (conta a pagar ou a receber) diretamente no Fluxo de Caixa do BruceOS. '
             . 'USE ESTA TOOL quando o gestor pedir no chat para lançar uma despesa, conta a pagar, entrada ou recebimento (ex: "lança uma conta de luz de R$ 280 vencendo dia 15", "registra uma entrada de R$ 1.200 hoje").';
    }

    public function getParametersSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'descricao' => [
                    'type' => 'string',
                    'description' => 'Descrição clara do lançamento (ex: "Conta de Internet Fibra", "Serviço de Manutenção").',
                ],
                'tipo' => [
                    'type' => 'string',
                    'enum' => ['receber', 'pagar'],
                    'description' => 'Tipo do lançamento: "receber" (entrada/receita) ou "pagar" (saída/despesa).',
                ],
                'valor' => [
                    'type' => 'number',
                    'description' => 'Valor em reais (ex: 350.00).',
                ],
                'data_vencimento' => [
                    'type' => 'string',
                    'description' => 'Data de vencimento no formato YYYY-MM-DD (se não informada, assume hoje).',
                ],
                'status' => [
                    'type' => 'string',
                    'enum' => ['pendente', 'pago'],
                    'description' => 'Status do lançamento: "pendente" (a vencer) ou "pago" (já quitado/recebido).',
                ],
                'categoria' => [
                    'type' => 'string',
                    'description' => 'Categoria financeira opcional (ex: "Despesas Fixas", "Fornecedores", "Vendas", "Impostos").',
                ],
            ],
            'required' => ['descricao', 'tipo', 'valor'],
        ];
    }

    public function execute(int $tenantId, array $arguments): array
    {
        $descricao = trim((string) ($arguments['descricao'] ?? ''));
        $tipo = in_array($arguments['tipo'] ?? '', ['receber', 'pagar'], true) ? $arguments['tipo'] : 'pagar';
        $valor = (float) ($arguments['valor'] ?? 0);

        if ($descricao === '' || $valor <= 0) {
            return ['error' => 'Descrição e valor maior que zero são obrigatórios.'];
        }

        try {
            $vencimento = !empty($arguments['data_vencimento'])
                ? Carbon::parse($arguments['data_vencimento'])->toDateString()
                : now()->toDateString();
        } catch (\Throwable) {
            $vencimento = now()->toDateString();
        }

        $status = in_array($arguments['status'] ?? '', ['pendente', 'pago'], true)
            ? $arguments['status']
            : 'pendente';

        $lancamento = LancamentoFinanceiro::create([
            'cliente_id' => $tenantId,
            'descricao' => $descricao,
            'tipo' => $tipo,
            'categoria' => trim((string) ($arguments['categoria'] ?? ($tipo === 'receber' ? 'Vendas / Serviços' : 'Despesas Operacionais'))),
            'valor' => $valor,
            'data_vencimento' => $vencimento,
            'data_pagamento' => $status === 'pago' ? now()->toDateString() : null,
            'status' => $status,
        ]);

        return [
            'status' => 'sucesso',
            'lancamento_id' => $lancamento->id,
            'descricao' => $lancamento->descricao,
            'tipo' => $lancamento->tipo,
            'valor_formatado' => 'R$ ' . number_format((float) $lancamento->valor, 2, ',', '.'),
            'data_vencimento' => Carbon::parse($vencimento)->format('d/m/Y'),
            'situacao' => $lancamento->status,
            'mensagem' => 'Lançamento registrado com sucesso no Fluxo de Caixa do BruceOS.',
        ];
    }
}
