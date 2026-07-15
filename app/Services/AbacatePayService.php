<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Configuracao;

class AbacatePayService
{
    protected $baseUrl = 'https://api.abacatepay.com/v2';
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = Configuracao::where('chave', 'abacatepay_api_key')->value('valor');
    }

    protected function client()
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->baseUrl($this->baseUrl);
    }

    /**
     * Cria um produto avulso (necessário para o checkout)
     */
    public function criarProduto($nome, $valorCentavos, $faturaId)
    {
        if (empty($this->apiKey)) {
            return null; // Gateway não configurado
        }

        $response = $this->client()->post('/products/create', [
            'externalId' => 'FAT-' . $faturaId,
            'name'       => $nome,
            'price'      => (int) $valorCentavos, // Garante int em centavos
            'currency'   => 'BRL'
        ]);

        if ($response->successful()) {
            return $response->json('data.id'); // Retorna o ID do produto na AbacatePay
        }

        \Log::error('AbacatePay Erro Produto: ' . $response->body());
        return null;
    }

    /**
     * Gera um link de Checkout usando um produto existente
     */
    public function criarCheckout($produtoId, $faturaId)
    {
        if (empty($this->apiKey) || !$produtoId) {
            return null; // Gateway não configurado ou produto inválido
        }

        $response = $this->client()->post('/checkouts/create', [
            'frequency' => 'ONE_TIME', // Frequência do checkout
            'items' => [
                [
                    'productId' => $produtoId,
                    'quantity'  => 1
                ]
            ],
            'returnUrl'     => route('customer.faturas'), // URL se voltar
            'completionUrl' => route('customer.faturas'), // URL de sucesso
            'metadata' => [
                'fatura_id' => (string) $faturaId
            ]
        ]);

        if ($response->successful()) {
            return $response->json('data.url'); // Retorna a URL do Checkout
        }

        \Log::error('AbacatePay Erro Checkout: ' . $response->body());
        return null;
    }
}
