<?php

declare(strict_types=1);

namespace Tests\Unit\Agent;

use Tests\TestCase;
use App\Agent\Proactive\ProactiveAlertsService;
use App\Agent\SnapshotBuilder;
use App\Models\AgentAlert;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProactiveAlertsGoogleBusinessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Instancia ProactiveAlertsService com SnapshotBuilder mockado a devolver
     * exatamente o bloco `google_business` passado — os outros blocos ficam
     * vazios, então nenhuma outra regra dispara.
     */
    private function serviceWithGbp(array $gbp): ProactiveAlertsService
    {
        $snapshot = [
            'tenant_id'         => 1,
            'analise_gerada_em' => now()->toIso8601String(),
            'periodo_dias'      => 30,
            'metricas'          => ['google_business' => $gbp],
        ];

        $mock = $this->createMock(SnapshotBuilder::class);
        $mock->method('build')->willReturn($snapshot);

        return new ProactiveAlertsService($mock);
    }

    private function seedCliente(int $id = 1): void
    {
        $user = User::factory()->create();
        Cliente::create(['id' => $id, 'razao_social' => 'Teste', 'user_id' => $user->id]);
    }

    public function test_media_estrelas_abaixo_de_4_com_5_reviews_gera_alerta_critico()
    {
        $this->seedCliente();

        $criados = $this->serviceWithGbp([
            'conectado'         => true,
            'ficha_selecionada' => true,
            'media_estrelas'    => 3.6,
            'total_reviews'     => 12,
            'posts_30d'         => 3, // > 0 pra nao disparar R8
        ])->analyzeTenant(1);

        $this->assertCount(1, $criados);
        $alerta = AgentAlert::where('tipo', 'gbp_estrelas_baixas')->first();
        $this->assertNotNull($alerta);
        $this->assertEquals('critico', $alerta->severidade);
        $this->assertEquals(3.6, $alerta->detalhes['media']);
    }

    public function test_menos_de_5_reviews_nao_gera_alerta_de_estrelas()
    {
        $this->seedCliente();

        $criados = $this->serviceWithGbp([
            'conectado'         => true,
            'ficha_selecionada' => true,
            'media_estrelas'    => 3.0,
            'total_reviews'     => 4, // amostral pequeno demais
            'posts_30d'         => 3,
        ])->analyzeTenant(1);

        $this->assertFalse($criados->contains(fn ($a) => $a->tipo === 'gbp_estrelas_baixas'));
    }

    public function test_posts_30d_zero_gera_alerta_atencao()
    {
        $this->seedCliente();

        $criados = $this->serviceWithGbp([
            'conectado'         => true,
            'ficha_selecionada' => true,
            'media_estrelas'    => 4.5,
            'total_reviews'     => 10,
            'posts_30d'         => 0,
        ])->analyzeTenant(1);

        $alerta = AgentAlert::where('tipo', 'gbp_sem_posts')->first();
        $this->assertNotNull($alerta);
        $this->assertEquals('atencao', $alerta->severidade);
    }

    public function test_queda_20pct_com_base_anterior_gera_alerta()
    {
        $this->seedCliente();

        $criados = $this->serviceWithGbp([
            'conectado'            => true,
            'ficha_selecionada'    => true,
            'media_estrelas'       => 4.5,
            'total_reviews'        => 10,
            'posts_30d'            => 3,
            'delta_impressoes_pct' => -25.5,
            'anterior_impressoes'  => 1000,
        ])->analyzeTenant(1);

        $alerta = AgentAlert::where('tipo', 'gbp_visualizacoes_caindo')->first();
        $this->assertNotNull($alerta);
        $this->assertEquals(-25.5, $alerta->detalhes['delta_pct']);
    }

    public function test_queda_com_anterior_zero_nao_gera_alerta()
    {
        $this->seedCliente();

        $criados = $this->serviceWithGbp([
            'conectado'            => true,
            'ficha_selecionada'    => true,
            'media_estrelas'       => 4.5,
            'total_reviews'        => 10,
            'posts_30d'            => 3,
            'delta_impressoes_pct' => -50.0,
            'anterior_impressoes'  => 0, // sem base = sem alerta
        ])->analyzeTenant(1);

        $this->assertFalse($criados->contains(fn ($a) => $a->tipo === 'gbp_visualizacoes_caindo'));
    }

    public function test_review_sem_resposta_3_dias_gera_alerta()
    {
        $this->seedCliente();

        $criados = $this->serviceWithGbp([
            'conectado'                     => true,
            'ficha_selecionada'             => true,
            'media_estrelas'                => 4.5,
            'total_reviews'                 => 10,
            'posts_30d'                     => 3,
            'reviews_sem_resposta_dias_max' => 5,
            'reviews_sem_resposta_qtd'      => 2,
        ])->analyzeTenant(1);

        $alerta = AgentAlert::where('tipo', 'gbp_avaliacao_sem_resposta')->first();
        $this->assertNotNull($alerta);
        $this->assertEquals(5, $alerta->detalhes['dias_max']);
    }

    public function test_sem_conectar_nao_gera_nenhum_alerta_gbp()
    {
        $this->seedCliente();

        $criados = $this->serviceWithGbp([
            'conectado'         => false,
            'ficha_selecionada' => false,
        ])->analyzeTenant(1);

        $this->assertCount(0, $criados);
    }

    public function test_dedup_nao_cria_alerta_duplicado_se_ja_ativo()
    {
        $this->seedCliente();

        $svc = $this->serviceWithGbp([
            'conectado'         => true,
            'ficha_selecionada' => true,
            'media_estrelas'    => 4.5,
            'total_reviews'     => 10,
            'posts_30d'         => 0, // dispara gbp_sem_posts
        ]);

        $primeiroRun = $svc->analyzeTenant(1);
        $segundoRun  = $svc->analyzeTenant(1);

        $this->assertCount(1, $primeiroRun);
        $this->assertCount(0, $segundoRun); // dedup por jaExiste()
        $this->assertEquals(1, AgentAlert::where('tipo', 'gbp_sem_posts')->count());
    }
}
