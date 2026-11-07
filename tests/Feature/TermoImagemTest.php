<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Mail\TermoImagemMail;
use App\Models\Cliente;
use App\Models\TermoImagem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TermoImagemTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function createClienteWithUser(string $razaoSocial = 'Cliente Audiovisual LTDA'): array
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $cliente = Cliente::create([
            'user_id'      => $user->id,
            'razao_social' => $razaoSocial,
            'cpf_cnpj'     => '12.345.678/0001-99',
        ]);
        $user->update(['cliente_id' => $cliente->id]);

        return [$user->fresh(), $cliente];
    }

    private function validSignatureDataUri(): string
    {
        // PNG 1x1 válido + padding para passar o tamanho mínimo razoável (> 60 bytes)
        $pngHeader = "\x89PNG\r\n\x1a\n";
        $body = str_repeat("\x00\x11\x22\x33\x44\x55\x66\x77", 16);

        return 'data:image/png;base64,' . base64_encode($pngHeader . $body);
    }

    public function test_admin_cria_edita_e_lista_termos_com_e_sem_cliente_e_menor(): void
    {
        $admin = $this->createAdmin();
        [, $cliente] = $this->createClienteWithUser('Produtora Alfa');

        $this->actingAs($admin);

        // 1. Criar termo sem cliente e adulto (usando modelo padrão automático)
        $res1 = $this->post(route('admin.termos-imagem.store'), [
            'titulo'             => 'Termo Campanha Institucional NC5',
            'projeto_referencia' => 'Reels Institucional',
            'data_captacao'      => '2026-11-10',
            'local_captacao'     => 'Estúdio NC5',
            'cedente_nome'       => 'João da Silva',
            'cedente_cpf'        => '111.222.333-44',
            'cedente_rg'         => '12.345.678-9',
            'cedente_email'      => 'joao@example.com',
            'cedente_telefone'   => '(11) 98888-7777',
            'cedente_endereco'   => 'Rua A, 100, São Paulo/SP',
            'cedente_menor'      => '0',
            'conteudo'           => '',
        ]);

        $termoAdulto = TermoImagem::where('cedente_nome', 'João da Silva')->first();
        $this->assertNotNull($termoAdulto);
        $res1->assertRedirect(route('admin.termos-imagem.show', $termoAdulto));
        $this->assertNull($termoAdulto->cliente_id);
        $this->assertFalse($termoAdulto->cedente_menor);
        $this->assertStringContainsString('João da Silva', $termoAdulto->conteudo);
        $this->assertStringContainsString('111.222.333-44', $termoAdulto->conteudo);
        $this->assertStringContainsString('Reels Institucional', $termoAdulto->conteudo);
        $this->assertStringContainsString('13.709/2018', $termoAdulto->conteudo);

        // 2. Criar termo com cliente e menor de idade
        $res2 = $this->post(route('admin.termos-imagem.store'), [
            'cliente_id'             => $cliente->id,
            'titulo'                 => 'Termo Comercial Infantil',
            'projeto_referencia'     => 'Campanha Dia das Crianças',
            'data_captacao'          => '2026-11-12',
            'local_captacao'         => 'Parque Ibirapuera',
            'cedente_nome'           => 'Lucas Pereira',
            'cedente_cpf'            => '555.666.777-88',
            'cedente_menor'          => '1',
            'responsavel_nome'       => 'Maria Pereira',
            'responsavel_cpf'        => '999.888.777-66',
            'responsavel_parentesco' => 'Mãe',
            'conteudo'               => '',
        ]);

        $termoMenor = TermoImagem::where('cedente_nome', 'Lucas Pereira')->first();
        $this->assertNotNull($termoMenor);
        $res2->assertRedirect(route('admin.termos-imagem.show', $termoMenor));
        $this->assertEquals($cliente->id, $termoMenor->cliente_id);
        $this->assertTrue($termoMenor->cedente_menor);
        $this->assertEquals('Maria Pereira', $termoMenor->responsavel_nome);
        $this->assertStringContainsString('Maria Pereira', $termoMenor->conteudo);
        $this->assertStringContainsString('8.069/1990', $termoMenor->conteudo);
        $this->assertStringContainsString('Produtora Alfa', $termoMenor->conteudo);

        // 3. Editar termo ainda não assinado
        $resUpdate = $this->put(route('admin.termos-imagem.update', $termoAdulto), [
            'cliente_id'       => $cliente->id,
            'titulo'           => 'Termo Campanha Institucional NC5 Atualizado',
            'cedente_nome'     => 'João da Silva Neto',
            'cedente_cpf'      => '111.222.333-44',
            'cedente_menor'    => '0',
            'conteudo'         => 'Texto customizado pelo admin para {{cedente_nome}}.',
            'status'           => 'enviado',
        ]);
        $resUpdate->assertRedirect(route('admin.termos-imagem.show', $termoAdulto));

        $termoAdulto->refresh();
        $this->assertEquals('Termo Campanha Institucional NC5 Atualizado', $termoAdulto->titulo);
        $this->assertEquals('enviado', $termoAdulto->status);
        $this->assertStringContainsString('João da Silva Neto', $termoAdulto->conteudo);

        // 4. Listagem e filtros no admin
        $resIndex = $this->get(route('admin.termos-imagem.index'));
        $resIndex->assertOk();
        $resIndex->assertSee('Termo Campanha Institucional NC5 Atualizado');
        $resIndex->assertSee('Termo Comercial Infantil');

        $resFiltroStatus = $this->get(route('admin.termos-imagem.index', ['status' => 'rascunho']));
        $resFiltroStatus->assertOk();
        $resFiltroStatus->assertSee('Termo Comercial Infantil');
        $resFiltroStatus->assertDontSee('Termo Campanha Institucional NC5 Atualizado');
    }

    public function test_validacao_menor_exige_dados_do_responsavel(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        $response = $this->post(route('admin.termos-imagem.store'), [
            'titulo'        => 'Termo Sem Responsável',
            'cedente_nome'  => 'Menor Teste',
            'cedente_cpf'   => '123.456.789-00',
            'cedente_menor' => '1',
        ]);

        $response->assertSessionHasErrors([
            'responsavel_nome',
            'responsavel_cpf',
            'responsavel_parentesco',
        ]);
        $this->assertDatabaseCount('termos_imagem', 0);
    }

    public function test_pagina_publica_abre_com_token_valido_e_retorna_404_com_token_invalido(): void
    {
        $termo = TermoImagem::create([
            'titulo'          => 'Termo Público Teste',
            'cedente_nome'    => 'Carlos Souza',
            'cedente_cpf'     => '123.123.123-12',
            'conteudo'        => 'Conteúdo integral do termo.',
            'status'          => 'enviado',
            'signature_token' => 'token-valido-1234567890',
        ]);

        $ok = $this->get(route('public.termo-imagem.show', $termo->signature_token));
        $ok->assertOk();
        $ok->assertSee('Termo Público Teste');
        $ok->assertSee('Carlos Souza');

        $notFound = $this->get(route('public.termo-imagem.show', 'token-inexistente-999'));
        $notFound->assertNotFound();
    }

    public function test_assinatura_sem_imagem_ou_sem_aceite_eh_recusada(): void
    {
        $termo = TermoImagem::create([
            'titulo'          => 'Termo Validação Assinatura',
            'cedente_nome'    => 'Ana Paula',
            'cedente_cpf'     => '321.321.321-32',
            'conteudo'        => 'Conteúdo para validar assinatura.',
            'status'          => 'enviado',
            'signature_token' => 'token-validacao-assinatura',
        ]);

        // Sem imagem de assinatura
        $resSemImagem = $this->post(route('public.termo-imagem.sign', $termo->signature_token), [
            'aceite'          => '1',
            'nome_assinante'  => 'Ana Paula',
            'documento'       => '321.321.321-32',
            'signature_image' => '',
        ]);
        $resSemImagem->assertSessionHasErrors('signature_image');
        $this->assertNotEquals('assinado', $termo->fresh()->status);

        // Sem aceite
        $resSemAceite = $this->post(route('public.termo-imagem.sign', $termo->signature_token), [
            'nome_assinante'  => 'Ana Paula',
            'documento'       => '321.321.321-32',
            'signature_image' => $this->validSignatureDataUri(),
        ]);
        $resSemAceite->assertSessionHasErrors('aceite');
        $this->assertNotEquals('assinado', $termo->fresh()->status);
    }

    public function test_assinatura_valida_grava_dados_status_assinado_em_e_conteudo_hash(): void
    {
        $conteudo = 'Texto oficial do termo de imagem e voz para verificação de hash.';
        $termo = TermoImagem::create([
            'titulo'          => 'Termo Assinatura Válida',
            'cedente_nome'    => 'Roberto Lima',
            'cedente_cpf'     => '444.555.666-77',
            'conteudo'        => $conteudo,
            'status'          => 'enviado',
            'signature_token' => 'token-assinatura-valida-01',
        ]);

        $sigImage = $this->validSignatureDataUri();

        $response = $this->post(route('public.termo-imagem.sign', $termo->signature_token), [
            'aceite'          => '1',
            'nome_assinante'  => 'Roberto Lima',
            'documento'       => '444.555.666-77',
            'signature_image' => $sigImage,
        ]);

        $response->assertRedirect(route('public.termo-imagem.show', $termo->signature_token));

        $termo->refresh();
        $this->assertEquals('assinado', $termo->status);
        $this->assertNotNull($termo->assinado_em);
        $this->assertEquals(hash('sha256', $conteudo), $termo->conteudo_hash);

        $dados = $termo->assinatura_array;
        $this->assertEquals('Roberto Lima', $dados['nome'] ?? null);
        $this->assertEquals('444.555.666-77', $dados['documento'] ?? null);
        $this->assertEquals($sigImage, $dados['signature_image'] ?? null);
        $this->assertNotEmpty($dados['ip'] ?? null);
        $this->assertNotEmpty($dados['timestamp'] ?? null);
    }

    public function test_nao_eh_possivel_assinar_duas_vezes_nem_editar_termo_assinado(): void
    {
        $admin = $this->createAdmin();
        $sigImage = $this->validSignatureDataUri();

        $termo = TermoImagem::create([
            'titulo'           => 'Termo Já Assinado',
            'cedente_nome'     => 'Fernanda Costa',
            'cedente_cpf'      => '777.888.999-00',
            'conteudo'         => 'Conteúdo imutável após assinatura.',
            'status'           => 'assinado',
            'assinado_em'      => now()->subHour(),
            'signature_token'  => 'token-ja-assinado-01',
            'assinatura_dados' => json_encode([
                'nome'            => 'Fernanda Costa',
                'documento'       => '777.888.999-00',
                'ip'              => '127.0.0.1',
                'user_agent'      => 'PHPUnit',
                'timestamp'       => now()->subHour()->toDateTimeString(),
                'signature_image' => $sigImage,
            ]),
            'conteudo_hash'    => hash('sha256', 'Conteúdo imutável após assinatura.'),
        ]);

        // Tentar assinar novamente
        $resSignAgain = $this->post(route('public.termo-imagem.sign', $termo->signature_token), [
            'aceite'          => '1',
            'nome_assinante'  => 'Outro Nome',
            'documento'       => '000.000.000-00',
            'signature_image' => $sigImage,
        ]);
        $resSignAgain->assertSessionHas('error');
        $this->assertEquals('Fernanda Costa', $termo->fresh()->assinatura_array['nome']);

        // Tentar abrir tela de edição e atualizar via Admin
        $this->actingAs($admin);

        $resEditForm = $this->get(route('admin.termos-imagem.edit', $termo));
        $resEditForm->assertRedirect(route('admin.termos-imagem.show', $termo));
        $resEditForm->assertSessionHas('error');

        $resUpdate = $this->put(route('admin.termos-imagem.update', $termo), [
            'titulo'        => 'Tentativa de Alteração',
            'cedente_nome'  => 'Hackeado',
            'cedente_cpf'   => '000.000.000-00',
            'cedente_menor' => '0',
            'conteudo'      => 'Conteúdo alterado',
            'status'        => 'rascunho',
        ]);
        $resUpdate->assertRedirect(route('admin.termos-imagem.show', $termo));
        $resUpdate->assertSessionHas('error');

        $termo->refresh();
        $this->assertEquals('Termo Já Assinado', $termo->titulo);
        $this->assertEquals('assinado', $termo->status);

        // Porém pode ser cancelado
        $resCancel = $this->post(route('admin.termos-imagem.cancelar', $termo));
        $resCancel->assertRedirect(route('admin.termos-imagem.show', $termo));
        $this->assertEquals('cancelado', $termo->fresh()->status);
    }

    public function test_pdf_eh_gerado_antes_e_depois_da_assinatura(): void
    {
        $admin = $this->createAdmin();
        $termo = TermoImagem::create([
            'titulo'          => 'Termo PDF Teste',
            'cedente_nome'    => 'Marcos Rocha',
            'cedente_cpf'     => '222.333.444-55',
            'conteudo'        => 'Conteúdo do PDF.',
            'status'          => 'enviado',
            'signature_token' => 'token-pdf-teste-01',
        ]);

        // PDF antes da assinatura (público e admin)
        $resPdfPublicBefore = $this->get(route('public.termo-imagem.pdf', $termo->signature_token));
        $resPdfPublicBefore->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $resPdfPublicBefore->headers->get('content-type'));

        // Assinar
        $this->post(route('public.termo-imagem.sign', $termo->signature_token), [
            'aceite'          => '1',
            'nome_assinante'  => 'Marcos Rocha',
            'documento'       => '222.333.444-55',
            'signature_image' => $this->validSignatureDataUri(),
        ]);

        // PDF depois da assinatura (público e admin)
        $resPdfPublicAfter = $this->get(route('public.termo-imagem.pdf', $termo->signature_token));
        $resPdfPublicAfter->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $resPdfPublicAfter->headers->get('content-type'));

        $this->actingAs($admin);
        $resPdfAdmin = $this->get(route('admin.termos-imagem.pdf', $termo));
        $resPdfAdmin->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $resPdfAdmin->headers->get('content-type'));
    }

    public function test_cliente_ve_so_os_proprios_termos_e_recebe_404_no_termo_de_outro_cliente(): void
    {
        [$user1, $cliente1] = $this->createClienteWithUser('Empresa Cliente Um');
        [, $cliente2] = $this->createClienteWithUser('Empresa Cliente Dois');

        $termoCliente1 = TermoImagem::create([
            'cliente_id'      => $cliente1->id,
            'titulo'          => 'Termo Exclusivo Cliente 1',
            'cedente_nome'    => 'Modelo Cliente 1',
            'cedente_cpf'     => '101.101.101-01',
            'conteudo'        => 'Texto cliente 1',
            'status'          => 'assinado',
            'assinado_em'     => now(),
            'signature_token' => 'token-cli-1',
        ]);

        $termoCliente2 = TermoImagem::create([
            'cliente_id'      => $cliente2->id,
            'titulo'          => 'Termo Secreto Cliente 2',
            'cedente_nome'    => 'Modelo Cliente 2',
            'cedente_cpf'     => '202.202.202-02',
            'conteudo'        => 'Texto cliente 2',
            'status'          => 'assinado',
            'assinado_em'     => now(),
            'signature_token' => 'token-cli-2',
        ]);

        $termoInternoNc5 = TermoImagem::create([
            'cliente_id'      => null,
            'titulo'          => 'Termo Interno NC5',
            'cedente_nome'    => 'Modelo NC5',
            'cedente_cpf'     => '303.303.303-03',
            'conteudo'        => 'Texto NC5',
            'status'          => 'assinado',
            'assinado_em'     => now(),
            'signature_token' => 'token-nc5-interno',
        ]);

        $this->actingAs($user1);

        $resList = $this->get(route('customer.termos-imagem'));
        $resList->assertOk();
        $resList->assertSee('Termo Exclusivo Cliente 1');
        $resList->assertDontSee('Termo Secreto Cliente 2');
        $resList->assertDontSee('Termo Interno NC5');

        // Download do próprio termo
        $resOwnPdf = $this->get(route('customer.termos-imagem.pdf', $termoCliente1->id));
        $resOwnPdf->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $resOwnPdf->headers->get('content-type'));

        // Download de termo de outro cliente -> 404
        $resOtherPdf = $this->get(route('customer.termos-imagem.pdf', $termoCliente2->id));
        $resOtherPdf->assertNotFound();

        // Download de termo interno da NC5 -> 404
        $resNc5Pdf = $this->get(route('customer.termos-imagem.pdf', $termoInternoNc5->id));
        $resNc5Pdf->assertNotFound();
    }

    public function test_usuario_sem_permissao_de_admin_nao_acessa_rotas_do_admin(): void
    {
        [$clienteUser] = $this->createClienteWithUser('Cliente Sem Admin');

        $termo = TermoImagem::create([
            'titulo'          => 'Termo Restrito',
            'cedente_nome'    => 'Pessoa Teste',
            'cedente_cpf'     => '111.111.111-11',
            'conteudo'        => 'Texto restrito',
            'status'          => 'rascunho',
            'signature_token' => 'token-restrito-admin',
        ]);

        // Visitante não autenticado -> redireciona para login
        $this->get(route('admin.termos-imagem.index'))->assertRedirect(route('login'));

        // Usuário com role cliente -> 403 Forbidden
        $this->actingAs($clienteUser);
        $this->get(route('admin.termos-imagem.index'))->assertForbidden();
        $this->get(route('admin.termos-imagem.create'))->assertForbidden();
        $this->post(route('admin.termos-imagem.store'), [])->assertForbidden();
        $this->get(route('admin.termos-imagem.show', $termo))->assertForbidden();
        $this->get(route('admin.termos-imagem.edit', $termo))->assertForbidden();
        $this->put(route('admin.termos-imagem.update', $termo), [])->assertForbidden();
        $this->delete(route('admin.termos-imagem.destroy', $termo))->assertForbidden();
    }

    public function test_admin_pode_enviar_termo_por_email(): void
    {
        Mail::fake();
        $admin = $this->createAdmin();

        $termo = TermoImagem::create([
            'titulo'          => 'Termo Envio Email',
            'cedente_nome'    => 'Beatriz Martins',
            'cedente_cpf'     => '888.777.666-55',
            'cedente_email'   => 'beatriz@example.com',
            'conteudo'        => 'Conteúdo do termo.',
            'status'          => 'rascunho',
            'signature_token' => 'token-email-beatriz',
        ]);

        $this->actingAs($admin);
        $res = $this->post(route('admin.termos-imagem.enviar-email', $termo));
        $res->assertSessionHas('success');

        Mail::assertSent(TermoImagemMail::class, function (TermoImagemMail $mail) use ($termo) {
            return $mail->hasTo('beatriz@example.com') && $mail->termo->id === $termo->id;
        });

        $termo->refresh();
        $this->assertEquals('enviado', $termo->status);
        $this->assertNotNull($termo->enviado_em);
    }
}
