<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\CustomerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Páginas Públicas (Dinâmicas via CMS)
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/servicos', [PublicController::class, 'servicos'])->name('servicos');
Route::get('/blog', [PublicController::class, 'blog'])->name('blog');
Route::get('/blog/{slug}', [PublicController::class, 'post'])->name('blog.post');

// Rotas Públicas de Propostas
Route::get('/proposta/{token}', [\App\Http\Controllers\PublicPedidoController::class, 'show'])->name('public.pedido.show');
Route::post('/proposta/{token}/approve', [\App\Http\Controllers\PublicPedidoController::class, 'approve'])->name('public.pedido.approve');
Route::get('/proposta/{token}/pdf', [\App\Http\Controllers\PublicPedidoController::class, 'downloadPdf'])->name('public.pedido.pdf');

// Landing Pages Públicas dos Clientes
Route::get('/lp/{slug}', [\App\Http\Controllers\Public\PublicLandingPageController::class, 'show'])->name('public.landing-page.show');
Route::post('/lp/{slug}/lead', [\App\Http\Controllers\Public\PublicLandingPageController::class, 'storeLead'])->middleware('throttle:15,1')->name('public.landing-page.lead');
Route::get('/lp/{slug}/whatsapp', [\App\Http\Controllers\Public\PublicLandingPageController::class, 'redirectWhatsapp'])->name('public.landing-page.whatsapp');

// Export de agenda (ICS publico por token)
Route::get('/agenda/ical/cliente/{token}.ics', [\App\Http\Controllers\Public\CalendarController::class, 'cliente'])
    ->name('public.calendar.cliente')
    ->where('token', '[A-Za-z0-9]+');
Route::get('/agenda/ical/final/{token}.ics', [\App\Http\Controllers\Public\CalendarController::class, 'clienteFinal'])
    ->name('public.calendar.cliente-final')
    ->where('token', '[A-Za-z0-9]+');

// Análise com Inteligência Artificial
Route::get('/analise-gratuita', [\App\Http\Controllers\AnalysisController::class, 'index'])->name('analise.index');
Route::post('/analise-gratuita/processar', [\App\Http\Controllers\AnalysisController::class, 'process'])->name('analise.process');

// Contato Público
Route::get('/contato', [\App\Http\Controllers\ContatoController::class, 'index'])->name('contato.index');
Route::post('/contato', [\App\Http\Controllers\ContatoController::class, 'store'])->name('contato.store');

// Assinatura pública de contrato de prestador (link por token)
Route::get('/contrato-prestador/{token}', [\App\Http\Controllers\PublicContratoPrestadorController::class, 'show'])->name('public.contrato-prestador.show');
Route::post('/contrato-prestador/{token}/assinar', [\App\Http\Controllers\PublicContratoPrestadorController::class, 'sign'])->middleware('throttle:5,1')->name('public.contrato-prestador.sign');
Route::get('/contrato-prestador/{token}/pdf', [\App\Http\Controllers\PublicContratoPrestadorController::class, 'downloadPdf'])->name('public.contrato-prestador.pdf');

// Páginas legais
Route::view('/privacidade', 'public.privacidade')->name('privacidade');
Route::view('/termos', 'public.termos')->name('termos');
Route::view('/cookies', 'public.cookies')->name('cookies');

// SEO
Route::get('/sitemap.xml', [PublicController::class, 'sitemap'])->name('sitemap');

// Dashboard padrão do Breeze (redireciona por role)
Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('customer.index');
})->middleware(['auth'])->name('dashboard');

// Área do Administrador (Isolamento via Role)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
    Route::resource('clientes', \App\Http\Controllers\Admin\ClienteController::class);

    // WhatsApp Bot — Financeiro (consumo DeepSeek por cliente)
    Route::get('/whatsapp-financeiro', [\App\Http\Controllers\Admin\WhatsappFinanceiroController::class, 'index'])
        ->name('whatsapp-financeiro.index');

    // Hub NC5 — entrar como Cliente NC5 Interno pra usar WhatsApp/Bot/Google Business
    Route::get('/hub-nc5/enter', [\App\Http\Controllers\Admin\ImpersonateController::class, 'enter'])
        ->name('impersonate.enter');

    // Hub NC5 — Agenda direto no painel admin (standalone, sem impersonate)
    Route::prefix('hub-nc5')->name('hub.')->group(function () {
        Route::prefix('agenda')->name('agenda.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\Hub\AppointmentController::class, 'index'])->name('index');
            Route::get('/criar', [\App\Http\Controllers\Admin\Hub\AppointmentController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\Hub\AppointmentController::class, 'store'])->name('store');
            Route::get('/{agenda}/editar', [\App\Http\Controllers\Admin\Hub\AppointmentController::class, 'edit'])->name('edit');
            Route::put('/{agenda}', [\App\Http\Controllers\Admin\Hub\AppointmentController::class, 'update'])->name('update');
            Route::delete('/{agenda}', [\App\Http\Controllers\Admin\Hub\AppointmentController::class, 'destroy'])->name('destroy');
            Route::post('/{agenda}/concluir', [\App\Http\Controllers\Admin\Hub\AppointmentController::class, 'markCompleted'])->name('complete');
            Route::post('/{agenda}/cancelar', [\App\Http\Controllers\Admin\Hub\AppointmentController::class, 'markCancelled'])->name('cancel');

            Route::prefix('recursos')->name('recursos.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\Hub\AppointmentResourceController::class, 'index'])->name('index');
                Route::get('/criar', [\App\Http\Controllers\Admin\Hub\AppointmentResourceController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Admin\Hub\AppointmentResourceController::class, 'store'])->name('store');
                Route::get('/{recurso}/editar', [\App\Http\Controllers\Admin\Hub\AppointmentResourceController::class, 'edit'])->name('edit');
                Route::put('/{recurso}', [\App\Http\Controllers\Admin\Hub\AppointmentResourceController::class, 'update'])->name('update');
                Route::delete('/{recurso}', [\App\Http\Controllers\Admin\Hub\AppointmentResourceController::class, 'destroy'])->name('destroy');
            });
        });

        // Regenerar token ICS do cliente NC5 Interno
        Route::post('/calendario/regenerate', [\App\Http\Controllers\Admin\Hub\CalendarLinkController::class, 'regenerateCliente'])
            ->name('calendar.cliente.regenerate');

        // WhatsApp — Conexão, Inbox, Bot, Dashboard
        Route::prefix('whatsapp')->name('whatsapp.')->group(function () {
            Route::get('conexao', [\App\Http\Controllers\Admin\Hub\WhatsappConnectionController::class, 'index'])->name('conexao');
            Route::post('conexao/connect', [\App\Http\Controllers\Admin\Hub\WhatsappConnectionController::class, 'connect'])->name('connect');
            Route::get('conexao/qr', [\App\Http\Controllers\Admin\Hub\WhatsappConnectionController::class, 'qr'])->name('qr');
            Route::get('conexao/status', [\App\Http\Controllers\Admin\Hub\WhatsappConnectionController::class, 'status'])->name('status');
            Route::post('conexao/disconnect', [\App\Http\Controllers\Admin\Hub\WhatsappConnectionController::class, 'disconnect'])->name('disconnect');

            Route::get('inbox', [\App\Http\Controllers\Admin\Hub\WhatsappInboxController::class, 'index'])->name('inbox');
            Route::get('inbox/feed', [\App\Http\Controllers\Admin\Hub\WhatsappInboxController::class, 'feedInbox'])->name('inbox.feed');
            Route::get('inbox/{chat}', [\App\Http\Controllers\Admin\Hub\WhatsappInboxController::class, 'show'])->name('chat');
            Route::get('inbox/{chat}/feed', [\App\Http\Controllers\Admin\Hub\WhatsappInboxController::class, 'feedChat'])->name('chat.feed');
            Route::post('inbox/{chat}/send', [\App\Http\Controllers\Admin\Hub\WhatsappInboxController::class, 'send'])->name('send');

            Route::get('bot', [\App\Http\Controllers\Admin\Hub\WhatsappBotController::class, 'index'])->name('bot.index');
            Route::post('bot', [\App\Http\Controllers\Admin\Hub\WhatsappBotController::class, 'update'])->name('bot.update');
            Route::post('inbox/{chat}/bot-toggle', [\App\Http\Controllers\Admin\Hub\WhatsappBotController::class, 'toggleChatBot'])->name('bot.toggle');

            Route::get('dashboard', [\App\Http\Controllers\Admin\Hub\WhatsappDashboardController::class, 'index'])->name('dashboard');
        });

        // Google Meu Negócio — Direto no painel Admin (sem impersonate)
        Route::prefix('google-business')->name('google-business.')->group(function () {
            Route::get('/', [\App\Http\Controllers\GoogleBusinessController::class, 'index'])->name('index');
            Route::get('/connect', [\App\Http\Controllers\GoogleBusinessController::class, 'redirectToGoogle'])->name('connect');
            Route::get('/callback', [\App\Http\Controllers\GoogleBusinessController::class, 'handleGoogleCallback'])->name('callback');
            Route::post('/disconnect', [\App\Http\Controllers\GoogleBusinessController::class, 'disconnect'])->name('disconnect');
            Route::post('/ficha', [\App\Http\Controllers\GoogleBusinessController::class, 'selectLocation'])->name('location');
            Route::patch('/ficha', [\App\Http\Controllers\GoogleBusinessController::class, 'updateLocation'])->middleware('throttle:15,1')->name('location.update');
            Route::post('/ficha/sugerir-descricao', [\App\Http\Controllers\GoogleBusinessController::class, 'suggestDescription'])->middleware('throttle:15,1')->name('location.suggest-description');
            Route::post('/posts', [\App\Http\Controllers\GoogleBusinessController::class, 'storePost'])->middleware('throttle:10,1')->name('posts.store');
            Route::delete('/posts', [\App\Http\Controllers\GoogleBusinessController::class, 'destroyPost'])->name('posts.destroy');
            Route::post('/avaliacoes/responder', [\App\Http\Controllers\GoogleBusinessController::class, 'replyReview'])->middleware('throttle:20,1')->name('reviews.reply');
            Route::post('/avaliacoes/sugerir', [\App\Http\Controllers\GoogleBusinessController::class, 'suggestReply'])->middleware('throttle:20,1')->name('reviews.suggest');
            Route::get('/exportar/csv', [\App\Http\Controllers\GoogleBusinessController::class, 'exportCsv'])->name('export.csv');
            Route::get('/exportar/pdf', [\App\Http\Controllers\GoogleBusinessController::class, 'exportPdf'])->name('export.pdf');
        });
    });

    // Usuários do painel do cliente (limite 5)
    Route::post('clientes/{cliente}/usuarios', [\App\Http\Controllers\Admin\ClienteController::class, 'usersStore'])->name('clientes.usuarios.store');
    Route::delete('clientes/{cliente}/usuarios/{user}', [\App\Http\Controllers\Admin\ClienteController::class, 'usersDestroy'])->name('clientes.usuarios.destroy');

    // Briefings no painel Admin (criar e visualizar resposta)
    Route::post('clientes/{cliente}/briefings', [\App\Http\Controllers\Admin\BriefingController::class, 'store'])->name('clientes.briefings.store');
    
    Route::resource('contratos', \App\Http\Controllers\Admin\ContratoController::class);
    Route::get('contratos/{id}/pdf', [\App\Http\Controllers\Admin\ContratoController::class, 'downloadPdf'])->name('contratos.pdf');

    // Prestadores de serviço (NC5)
    Route::resource('prestadores', \App\Http\Controllers\Admin\PrestadorController::class)
        ->parameters(['prestadores' => 'prestador']);

    // Contratos com prestadores (template separado)
    Route::get('contratos-prestadores/{contrato}/pdf', [\App\Http\Controllers\Admin\ContratoPrestadorController::class, 'downloadPdf'])->name('contratos-prestadores.pdf');
    Route::post('contratos-prestadores/{contrato}/enviar-email', [\App\Http\Controllers\Admin\ContratoPrestadorController::class, 'enviarEmail'])->name('contratos-prestadores.enviar-email');
    Route::resource('contratos-prestadores', \App\Http\Controllers\Admin\ContratoPrestadorController::class)
        ->parameters(['contratos-prestadores' => 'contrato']);
    Route::resource('faturas', \App\Http\Controllers\Admin\FaturaController::class);
    Route::post('faturas/{id}/enviar-email', [\App\Http\Controllers\Admin\FaturaController::class, 'enviarEmail'])->name('faturas.enviar_email');
    Route::resource('materiais', \App\Http\Controllers\Admin\MaterialController::class);
    Route::post('materiais/{id}/replies', [\App\Http\Controllers\Admin\MaterialController::class, 'storeReply'])->name('materiais.replies.store');
    
    // Tickets no painel Admin
    Route::get('/tickets', [\App\Http\Controllers\Admin\TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [\App\Http\Controllers\Admin\TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply', [\App\Http\Controllers\Admin\TicketController::class, 'reply'])->name('tickets.reply');
    Route::put('/tickets/{ticket}/close', [\App\Http\Controllers\Admin\TicketController::class, 'close'])->name('tickets.close');
    
    // CMS
    Route::resource('paginas', \App\Http\Controllers\Admin\PaginaController::class);
    Route::resource('posts', \App\Http\Controllers\Admin\PostController::class);
    Route::resource('servicos', \App\Http\Controllers\Admin\ServicoController::class);
    
    // E-mail Marketing (Campanhas)
    Route::get('/campanhas', [\App\Http\Controllers\Admin\CampanhaController::class, 'index'])->name('campanhas.index');
    Route::get('/campanhas/create', [\App\Http\Controllers\Admin\CampanhaController::class, 'create'])->name('campanhas.create');
    Route::post('/campanhas', [\App\Http\Controllers\Admin\CampanhaController::class, 'store'])->name('campanhas.store');

    // Configurações Globais
    Route::get('configuracoes', [\App\Http\Controllers\Admin\ConfiguracaoController::class, 'index'])->name('configuracoes.index');
    Route::post('configuracoes', [\App\Http\Controllers\Admin\ConfiguracaoController::class, 'store'])->name('configuracoes.store');

    // Contatos recebidos
    Route::get('/contatos', [\App\Http\Controllers\Admin\ContatoController::class, 'index'])->name('contatos.index');
    Route::post('/contatos/bulk-destroy', [\App\Http\Controllers\Admin\ContatoController::class, 'bulkDestroy'])->name('contatos.bulk_destroy');
    Route::get('/contatos/{contato}', [\App\Http\Controllers\Admin\ContatoController::class, 'show'])->name('contatos.show');
    Route::put('/contatos/{contato}/status', [\App\Http\Controllers\Admin\ContatoController::class, 'updateStatus'])->name('contatos.status');
    Route::delete('/contatos/{contato}', [\App\Http\Controllers\Admin\ContatoController::class, 'destroy'])->name('contatos.destroy');

    // Leads (IA)
    Route::get('/leads', [\App\Http\Controllers\Admin\LeadController::class, 'index'])->name('leads.index');
    Route::get('/leads/export', [\App\Http\Controllers\Admin\LeadController::class, 'export'])->name('leads.export');
    Route::delete('/leads/{lead}', [\App\Http\Controllers\Admin\LeadController::class, 'destroy'])->name('leads.destroy');

    // Lembretes & Notificações
    Route::get('/lembretes', [\App\Http\Controllers\Admin\LembreteController::class, 'create'])->name('lembretes.create');
    Route::post('/lembretes', [\App\Http\Controllers\Admin\LembreteController::class, 'store'])->name('lembretes.store');
});

// Sair do modo "Entrar como" — sessao atual eh cliente, nao bate em role:admin
Route::post('/impersonate/leave', [\App\Http\Controllers\Admin\ImpersonateController::class, 'leave'])
    ->middleware('auth')
    ->name('admin.impersonate.leave');

// Callback OAuth do Google Meu Negócio (acessível por Admin e Cliente sem exigir role:cliente)
Route::get('/area-cliente/google-business/callback', [\App\Http\Controllers\GoogleBusinessController::class, 'handleGoogleCallback'])
    ->middleware('auth')
    ->name('customer.google-business.callback');

// Área do Cliente (Isolamento via Role)
Route::middleware(['auth', 'role:cliente'])->prefix('area-cliente')->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('customer.index');
    Route::get('/contratos', [CustomerController::class, 'contracts'])->name('customer.contracts');
    Route::post('/contratos/{id}/assinar', [CustomerController::class, 'signContract'])->name('customer.contracts.sign');
    Route::get('/contratos/{id}/pdf', [CustomerController::class, 'downloadContract'])->name('customer.contracts.pdf');
    Route::get('/faturas', [CustomerController::class, 'invoices'])->name('customer.invoices');
    Route::get('/faturas/{id}', [CustomerController::class, 'showInvoice'])->name('customer.invoices.show');
    Route::post('/faturas/{id}/comprovante', [CustomerController::class, 'uploadComprovante'])->name('customer.invoices.comprovante');
    Route::get('/materiais', [CustomerController::class, 'materiais'])->name('customer.materiais');
    Route::post('/materiais/{id}/avaliar', [CustomerController::class, 'evaluateMaterial'])->name('customer.materiais.evaluate');
    Route::get('/briefings', [CustomerController::class, 'briefings'])->name('customer.briefings');
    Route::post('/briefings/{id}/responder', [CustomerController::class, 'answerBriefing'])->name('customer.briefings.answer');
    
    // Suporte (Tickets)
    Route::get('/suporte', [CustomerController::class, 'support'])->name('customer.support');
    Route::post('/suporte', [CustomerController::class, 'storeTicket'])->name('customer.support.store');
    Route::get('/suporte/{ticket}', [CustomerController::class, 'showTicket'])->name('customer.support.show');
    Route::post('/suporte/{ticket}/reply', [CustomerController::class, 'replyTicket'])->name('customer.support.reply');

    // Integração Google Meu Negócio
    Route::get('/google-business', [\App\Http\Controllers\GoogleBusinessController::class, 'index'])->name('customer.google-business.index');
    Route::get('/google-business/connect', [\App\Http\Controllers\GoogleBusinessController::class, 'redirectToGoogle'])->name('customer.google-business.connect');
    Route::post('/google-business/disconnect', [\App\Http\Controllers\GoogleBusinessController::class, 'disconnect'])->name('customer.google-business.disconnect');
    Route::post('/google-business/ficha', [\App\Http\Controllers\GoogleBusinessController::class, 'selectLocation'])->name('customer.google-business.location');
    Route::patch('/google-business/ficha', [\App\Http\Controllers\GoogleBusinessController::class, 'updateLocation'])->middleware('throttle:15,1')->name('customer.google-business.location.update');
    Route::post('/google-business/ficha/sugerir-descricao', [\App\Http\Controllers\GoogleBusinessController::class, 'suggestDescription'])->middleware('throttle:15,1')->name('customer.google-business.location.suggest-description');
    Route::post('/google-business/posts', [\App\Http\Controllers\GoogleBusinessController::class, 'storePost'])->middleware('throttle:10,1')->name('customer.google-business.posts.store');
    Route::delete('/google-business/posts', [\App\Http\Controllers\GoogleBusinessController::class, 'destroyPost'])->name('customer.google-business.posts.destroy');
    Route::post('/google-business/avaliacoes/responder', [\App\Http\Controllers\GoogleBusinessController::class, 'replyReview'])->middleware('throttle:20,1')->name('customer.google-business.reviews.reply');
    Route::post('/google-business/avaliacoes/sugerir', [\App\Http\Controllers\GoogleBusinessController::class, 'suggestReply'])->middleware('throttle:20,1')->name('customer.google-business.reviews.suggest');
    Route::get('/google-business/exportar/csv', [\App\Http\Controllers\GoogleBusinessController::class, 'exportCsv'])->name('customer.google-business.export.csv');
    Route::get('/google-business/exportar/pdf', [\App\Http\Controllers\GoogleBusinessController::class, 'exportPdf'])->name('customer.google-business.export.pdf');

    // Meu Site / Criador de Landing Pages (4 Modelos)
    Route::get('/meu-site', [\App\Http\Controllers\Customer\LandingPageController::class, 'edit'])->name('customer.landing-page.edit');
    Route::put('/meu-site', [\App\Http\Controllers\Customer\LandingPageController::class, 'update'])->name('customer.landing-page.update');
    Route::post('/meu-site/importar-catalogo', [\App\Http\Controllers\Customer\LandingPageController::class, 'importCatalog'])->name('customer.landing-page.import-catalog');
    Route::post('/meu-site/gerar-ia', [\App\Http\Controllers\Customer\LandingPageController::class, 'generateWithAi'])->middleware('throttle:10,1')->name('customer.landing-page.ai-generate');

    // Gestão de Clientes (CRM do Cliente)
    Route::resource('gestao-clientes', \App\Http\Controllers\Customer\ClienteFinalController::class)
        ->names('customer.clientes-finais')
        ->parameters(['gestao-clientes' => 'clientes_finai']);

    // Fornecedores
    Route::resource('fornecedores', \App\Http\Controllers\Customer\FornecedorController::class)
        ->names('customer.fornecedores')
        ->except(['show']);

    // Dashboard de Vendas
    Route::get('dashboard-vendas', [\App\Http\Controllers\Customer\DashboardVendasController::class, 'index'])
        ->name('customer.dashboard-vendas');

    // Gestão de Pedidos (Vendas)
    Route::get('pedidos-kanban', [\App\Http\Controllers\Customer\PedidoClienteController::class, 'kanban'])
        ->name('customer.pedidos.kanban');
    Route::patch('pedidos/{pedido}/status', [\App\Http\Controllers\Customer\PedidoClienteController::class, 'updateStatus'])
        ->name('customer.pedidos.update-status');
    Route::post('pedidos/{pedido}/enviar-email', [\App\Http\Controllers\Customer\PedidoClienteController::class, 'enviarEmail'])
        ->name('customer.pedidos.enviar-email');
    Route::get('pedidos/{pedido}/pdf', [\App\Http\Controllers\Customer\PedidoClienteController::class, 'downloadPdf'])
        ->name('customer.pedidos.pdf');
    Route::resource('pedidos', \App\Http\Controllers\Customer\PedidoClienteController::class)
        ->names('customer.pedidos')
        ->parameters(['pedidos' => 'pedido']);

    // Produtos e Serviços (Catálogo do Cliente)
    Route::post('produtos-importar', [\App\Http\Controllers\Customer\ProdutoServicoController::class, 'importCsv'])
        ->name('customer.produtos.importar');
    Route::resource('produtos-servicos', \App\Http\Controllers\Customer\ProdutoServicoController::class)
        ->names('customer.produtos')
        ->parameters(['produtos-servicos' => 'produto']);

    // Financeiro, Projetos e Documentos (Hub)
    Route::resource('lancamentos', \App\Http\Controllers\LancamentoFinanceiroController::class)->names('customer.lancamentos');
    Route::resource('projetos', \App\Http\Controllers\ProjetoController::class)->names('customer.projetos');
    Route::post('projetos/{projeto}/tarefas', [\App\Http\Controllers\ProjetoController::class, 'storeTarefa'])->name('customer.projetos.tarefas.store');
    Route::patch('tarefas/{tarefa}/toggle', [\App\Http\Controllers\ProjetoController::class, 'toggleTarefa'])->name('customer.projetos.tarefas.toggle');
    // Documentos
    Route::resource('documentos', \App\Http\Controllers\DocumentoController::class)->names('customer.documentos');

    // Rascunhos da IA
    Route::get('mensagens-ia', [\App\Http\Controllers\Customer\AgentDraftController::class, 'index'])->name('customer.agent-drafts.index');
    Route::post('mensagens-ia/{draft}/approve', [\App\Http\Controllers\Customer\AgentDraftController::class, 'approve'])->name('customer.agent-drafts.approve');
    Route::post('mensagens-ia/{draft}/discard', [\App\Http\Controllers\Customer\AgentDraftController::class, 'discard'])->name('customer.agent-drafts.discard');

    // Base de Conhecimento do Bruce (RAG do cliente)
    Route::resource('conhecimento', \App\Http\Controllers\Customer\KnowledgeController::class)
        ->names('customer.conhecimento')
        ->except(['show']);

    // Trocar senha do próprio usuário logado
    Route::get('/senha', function () {
        return view('customer.senha');
    })->name('customer.senha');

    // Alertas proativos do Bruce
    Route::get('alertas', [\App\Http\Controllers\Customer\AlertsController::class, 'index'])
        ->name('customer.alertas.index');
    Route::post('alertas/{alerta}/lido', [\App\Http\Controllers\Customer\AlertsController::class, 'marcarLido'])
        ->name('customer.alertas.lido');
    Route::post('alertas/{alerta}/dispensar', [\App\Http\Controllers\Customer\AlertsController::class, 'dispensar'])
        ->name('customer.alertas.dispensar');

    // Agenda
    Route::prefix('agenda')->name('customer.agenda.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Customer\AppointmentController::class, 'index'])->name('index');
        Route::get('/criar', [\App\Http\Controllers\Customer\AppointmentController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Customer\AppointmentController::class, 'store'])->name('store');
        Route::get('/{agenda}/editar', [\App\Http\Controllers\Customer\AppointmentController::class, 'edit'])->name('edit');
        Route::put('/{agenda}', [\App\Http\Controllers\Customer\AppointmentController::class, 'update'])->name('update');
        Route::delete('/{agenda}', [\App\Http\Controllers\Customer\AppointmentController::class, 'destroy'])->name('destroy');
        Route::post('/{agenda}/concluir', [\App\Http\Controllers\Customer\AppointmentController::class, 'markCompleted'])->name('complete');
        Route::post('/{agenda}/cancelar', [\App\Http\Controllers\Customer\AppointmentController::class, 'markCancelled'])->name('cancel');

        // Recursos (profissionais / cadeiras / salas / etc)
        Route::prefix('recursos')->name('recursos.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Customer\AppointmentResourceController::class, 'index'])->name('index');
            Route::get('/criar', [\App\Http\Controllers\Customer\AppointmentResourceController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Customer\AppointmentResourceController::class, 'store'])->name('store');
            Route::get('/{recurso}/editar', [\App\Http\Controllers\Customer\AppointmentResourceController::class, 'edit'])->name('edit');
            Route::put('/{recurso}', [\App\Http\Controllers\Customer\AppointmentResourceController::class, 'update'])->name('update');
            Route::delete('/{recurso}', [\App\Http\Controllers\Customer\AppointmentResourceController::class, 'destroy'])->name('destroy');
        });
    });

    // Calendar links (regenerar tokens ICS)
    Route::post('/calendario/cliente/regenerate', [\App\Http\Controllers\Customer\CalendarLinkController::class, 'regenerateCliente'])
        ->name('customer.calendar.cliente.regenerate');
    Route::post('/clientes-finais/{clientes_finai}/calendario/regenerate', [\App\Http\Controllers\Customer\CalendarLinkController::class, 'regenerateClienteFinal'])
        ->name('customer.calendar.cliente-final.regenerate');

    // WhatsApp (Evolution API) - Onda 1: inbox mínimo
    Route::prefix('whatsapp')->name('customer.whatsapp.')->group(function () {
        Route::get('conexao', [\App\Http\Controllers\Customer\WhatsappConnectionController::class, 'index'])->name('conexao');
        Route::post('conexao/connect', [\App\Http\Controllers\Customer\WhatsappConnectionController::class, 'connect'])->name('connect');
        Route::get('conexao/qr', [\App\Http\Controllers\Customer\WhatsappConnectionController::class, 'qr'])->name('qr');
        Route::get('conexao/status', [\App\Http\Controllers\Customer\WhatsappConnectionController::class, 'status'])->name('status');
        Route::post('conexao/disconnect', [\App\Http\Controllers\Customer\WhatsappConnectionController::class, 'disconnect'])->name('disconnect');

        Route::get('inbox', [\App\Http\Controllers\Customer\WhatsappInboxController::class, 'index'])->name('inbox');
        Route::get('inbox/feed', [\App\Http\Controllers\Customer\WhatsappInboxController::class, 'feedInbox'])->name('inbox.feed');
        Route::get('inbox/{chat}', [\App\Http\Controllers\Customer\WhatsappInboxController::class, 'show'])->name('chat');
        Route::get('inbox/{chat}/feed', [\App\Http\Controllers\Customer\WhatsappInboxController::class, 'feedChat'])->name('chat.feed');
        Route::post('inbox/{chat}/send', [\App\Http\Controllers\Customer\WhatsappInboxController::class, 'send'])->name('send');

        // Bot IA
        Route::get('bot', [\App\Http\Controllers\Customer\WhatsappBotController::class, 'index'])->name('bot.index');
        Route::post('bot', [\App\Http\Controllers\Customer\WhatsappBotController::class, 'update'])->name('bot.update');
        Route::post('inbox/{chat}/bot-toggle', [\App\Http\Controllers\Customer\WhatsappBotController::class, 'toggleChatBot'])->name('bot.toggle');

        // Dashboard do bot
        Route::get('dashboard', [\App\Http\Controllers\Customer\WhatsappDashboardController::class, 'index'])->name('dashboard');
    });
});

require __DIR__.'/auth.php';
