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

// Análise com Inteligência Artificial
Route::get('/analise-gratuita', [\App\Http\Controllers\AnalysisController::class, 'index'])->name('analise.index');
Route::post('/analise-gratuita/processar', [\App\Http\Controllers\AnalysisController::class, 'process'])->name('analise.process');

// Contato Público
Route::get('/contato', [\App\Http\Controllers\ContatoController::class, 'index'])->name('contato.index');
Route::post('/contato', [\App\Http\Controllers\ContatoController::class, 'store'])->name('contato.store');

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
    
    // Briefings no painel Admin (criar e visualizar resposta)
    Route::post('clientes/{cliente}/briefings', [\App\Http\Controllers\Admin\BriefingController::class, 'store'])->name('clientes.briefings.store');
    
    Route::resource('contratos', \App\Http\Controllers\Admin\ContratoController::class);
    Route::get('contratos/{id}/pdf', [\App\Http\Controllers\Admin\ContratoController::class, 'downloadPdf'])->name('contratos.pdf');
    Route::resource('faturas', \App\Http\Controllers\Admin\FaturaController::class);
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
    Route::get('/contatos/{contato}', [\App\Http\Controllers\Admin\ContatoController::class, 'show'])->name('contatos.show');
    Route::put('/contatos/{contato}/status', [\App\Http\Controllers\Admin\ContatoController::class, 'updateStatus'])->name('contatos.status');
    Route::delete('/contatos/{contato}', [\App\Http\Controllers\Admin\ContatoController::class, 'destroy'])->name('contatos.destroy');

    // Leads (IA)
    Route::get('/leads', [\App\Http\Controllers\Admin\LeadController::class, 'index'])->name('leads.index');
    Route::get('/leads/export', [\App\Http\Controllers\Admin\LeadController::class, 'export'])->name('leads.export');

    // Lembretes & Notificações
    Route::get('/lembretes', [\App\Http\Controllers\Admin\LembreteController::class, 'create'])->name('lembretes.create');
    Route::post('/lembretes', [\App\Http\Controllers\Admin\LembreteController::class, 'store'])->name('lembretes.store');
});

// Área do Cliente (Isolamento via Role)
Route::middleware(['auth', 'role:cliente'])->prefix('area-cliente')->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('customer.index');
    Route::get('/contratos', [CustomerController::class, 'contracts'])->name('customer.contracts');
    Route::post('/contratos/{id}/assinar', [CustomerController::class, 'signContract'])->name('customer.contracts.sign');
    Route::get('/contratos/{id}/pdf', [CustomerController::class, 'downloadContract'])->name('customer.contracts.pdf');
    Route::get('/faturas', [CustomerController::class, 'invoices'])->name('customer.invoices');
    Route::get('/materiais', [CustomerController::class, 'materiais'])->name('customer.materiais');
    Route::post('/materiais/{id}/avaliar', [CustomerController::class, 'evaluateMaterial'])->name('customer.materiais.evaluate');
    Route::get('/briefings', [CustomerController::class, 'briefings'])->name('customer.briefings');
    Route::post('/briefings/{id}/responder', [CustomerController::class, 'answerBriefing'])->name('customer.briefings.answer');
    
    // Suporte (Tickets)
    Route::get('/suporte', [CustomerController::class, 'support'])->name('customer.support');
    Route::post('/suporte', [CustomerController::class, 'storeTicket'])->name('customer.support.store');
    Route::get('/suporte/{ticket}', [CustomerController::class, 'showTicket'])->name('customer.support.show');
    Route::post('/suporte/{ticket}/reply', [CustomerController::class, 'replyTicket'])->name('customer.support.reply');
});

require __DIR__.'/auth.php';
