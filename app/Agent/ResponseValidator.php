<?php

declare(strict_types=1);

namespace App\Agent;

use Exception;

class ResponseValidator
{
    /**
     * Tenta decodificar o JSON do LLM e valida se todas as regras de negócio
     * e enums obrigatórios foram respeitados pelo modelo.
     *
     * @throws Exception Se o JSON for inválido ou o Schema quebrado.
     */
    public function validate(string $jsonString): array
    {
        // Alguns modelos (como GPT-4 ou DeepSeek) costumam mandar o JSON em blocos markdown (```json ... ```).
        // Aqui limpamos a string para evitar falha no json_decode
        $jsonString = preg_replace('/```json|```/', '', trim($jsonString));

        $data = json_decode($jsonString, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('O LLM retornou um formato JSON inválido: ' . json_last_error_msg());
        }

        if (!isset($data['diagnostico_geral']) || !is_string($data['diagnostico_geral'])) {
            throw new Exception('A propriedade raiz "diagnostico_geral" está ausente ou não é texto.');
        }

        if (!isset($data['insights']) || !is_array($data['insights'])) {
            throw new Exception('A propriedade raiz "insights" está ausente ou não é um array.');
        }

        $categorias = ['pedidos', 'caixa', 'clientes', 'produtos'];
        $severidades = ['info', 'atencao', 'critico'];

        foreach ($data['insights'] as $i => $insight) {
            if (!isset($insight['titulo'], $insight['categoria'], $insight['severidade'], $insight['acao_sugerida'], $insight['evidencia'])) {
                throw new Exception("O insight na posição [{$i}] está com campos obrigatórios ausentes.");
            }

            if (!in_array($insight['categoria'], $categorias)) {
                throw new Exception("Categoria inválida no insight [{$i}]: {$insight['categoria']}");
            }

            if (!in_array($insight['severidade'], $severidades)) {
                throw new Exception("Severidade inválida no insight [{$i}]: {$insight['severidade']}");
            }

            if (!is_array($insight['evidencia'])) {
                throw new Exception("A evidência no insight [{$i}] deve ser um objeto JSON (Array PHP).");
            }
        }

        return $data;
    }
}
