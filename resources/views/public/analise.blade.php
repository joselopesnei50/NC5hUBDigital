@extends('layouts.public')

@section('title', 'Análise Gratuita com BruceIA · NC5 Hub')

@section('content')

    {{-- ============================================================
         BANNER PREMIUM BRUCEIA
         ============================================================ --}}
    <section class="relative overflow-hidden bg-bruceInk text-white pt-16 lg:pt-20 pb-14 lg:pb-20">
        <!-- glow laranja no fundo -->
        <div class="absolute -top-40 right-1/4 w-[700px] h-[500px] bg-bruce/15 rounded-full blur-[140px] pointer-events-none"></div>
        <div class="absolute -bottom-40 -left-32 w-[500px] h-[500px] bg-bruce/10 rounded-full blur-[120px] pointer-events-none"></div>
        <!-- padrão sutil de grade -->
        <div class="absolute inset-0 opacity-[0.04] pointer-events-none"
             style="background-image: linear-gradient(#FF7A1A 1px, transparent 1px), linear-gradient(90deg, #FF7A1A 1px, transparent 1px); background-size: 40px 40px;"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center">

                <!-- lado esquerdo: identidade Bruce -->
                <div class="lg:col-span-7">
                    <div class="inline-flex items-center gap-2 bg-white/5 border border-white/10 backdrop-blur-sm px-4 py-1.5 rounded-full mb-8">
                        <span class="w-2 h-2 bg-bruce rounded-full animate-pulse"></span>
                        <span class="text-[11px] font-bold uppercase tracking-widest text-white/70">Inteligência Artificial NC5</span>
                    </div>

                    <div class="flex items-center gap-5 mb-8">
                        <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="BruceIA" class="w-20 h-20 lg:w-24 lg:h-24 flex-shrink-0 drop-shadow-[0_0_40px_rgba(255,122,26,0.35)]">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.3em] text-bruce mb-1">Conheça</p>
                            <h1 class="font-display font-black text-5xl lg:text-7xl leading-none tracking-tight text-white">
                                Bruce<span class="text-bruce">IA</span>
                            </h1>
                        </div>
                    </div>

                    <p class="font-display text-2xl lg:text-4xl leading-tight text-white mb-6">
                        O consultor de <em class="not-italic text-bruce">performance</em> que nunca dorme.
                    </p>

                    <p class="text-white/60 text-base lg:text-lg leading-relaxed max-w-xl">
                        Treinado pelos estrategistas da NC5, o Bruce lê sua marca, site ou perfil social e devolve um parecer premium em minutos — direto ao ponto e pronto para virar plano de ação.
                    </p>

                    <div class="mt-10 flex flex-wrap items-center gap-x-8 gap-y-4 text-sm">
                        <div class="flex items-center gap-2 text-white/70">
                            <svg class="w-4 h-4 text-bruce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            100% gratuito
                        </div>
                        <div class="flex items-center gap-2 text-white/70">
                            <svg class="w-4 h-4 text-bruce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            Sem cartão de crédito
                        </div>
                        <div class="flex items-center gap-2 text-white/70">
                            <svg class="w-4 h-4 text-bruce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            Resultado em ~30 segundos
                        </div>
                    </div>
                </div>

                <!-- lado direito: card com stats + ícone gigante -->
                <div class="lg:col-span-5">
                    <div class="relative bg-gradient-to-br from-bruce to-bruceDark rounded-3xl p-8 lg:p-10 shadow-2xl shadow-bruce/30 overflow-hidden">
                        <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/10 rounded-full blur-[60px] pointer-events-none"></div>

                        <div class="relative">
                            <div class="flex items-start justify-between mb-8">
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-bruceInk/80 mb-2">Motor de análise</p>
                                    <p class="font-display font-black text-3xl text-bruceInk leading-none">Bruce<span class="text-white">IA</span> · v1</p>
                                </div>
                                <img src="{{ asset('images/bruce/bruceia-icone-fundo-claro.svg') }}" alt="" class="w-14 h-14 flex-shrink-0">
                            </div>

                            <ul class="space-y-3 text-sm text-bruceInk font-medium">
                                <li class="flex items-start gap-3 bg-white/25 backdrop-blur-sm rounded-xl px-4 py-3">
                                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4"></path></svg>
                                    <span>Diagnóstico de UX, oferta e posicionamento</span>
                                </li>
                                <li class="flex items-start gap-3 bg-white/25 backdrop-blur-sm rounded-xl px-4 py-3">
                                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4"></path></svg>
                                    <span>Pontos fortes e oportunidades críticas</span>
                                </li>
                                <li class="flex items-start gap-3 bg-white/25 backdrop-blur-sm rounded-xl px-4 py-3">
                                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4"></path></svg>
                                    <span>Parecer estratégico da agência</span>
                                </li>
                            </ul>

                            <div class="mt-8 pt-6 border-t border-bruceInk/15 flex items-center justify-between">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-bruceInk/70">Treinado por</span>
                                <span class="text-bruceInk font-bold text-sm">Estrategistas NC5 Hub</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         FORMULÁRIO
         ============================================================ --}}
    <section class="relative py-16 lg:py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ loading: false }">

            <div class="text-center mb-12">
                <p class="text-[11px] font-bold uppercase tracking-[0.3em] text-bruce mb-4">Gere sua análise</p>
                <h2 class="font-display font-bold text-4xl md:text-5xl text-ink leading-tight tracking-tight">
                    Descubra em 30s o que <em class="not-italic text-bruce">trava</em> sua conversão.
                </h2>
                <p class="mt-5 text-lg text-slate max-w-2xl mx-auto">Preencha os dados e o Bruce devolve um parecer premium sobre marca, site ou perfil social — direto e valioso.</p>
            </div>

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-2xl mb-6 text-center font-medium">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-2xl mb-6">
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-2xl shadow-bruceInk/10 overflow-hidden flex flex-col md:flex-row border border-black/5 relative">

                <div x-show="loading" style="display: none;" class="absolute inset-0 z-10 bg-white/95 backdrop-blur-sm flex flex-col items-center justify-center px-8">
                    <div class="relative w-20 h-20 mb-6">
                        <div class="absolute inset-0 border-4 border-mist border-t-bruce rounded-full animate-spin"></div>
                        <img src="{{ asset('images/bruce/bruceia-icone-fundo-claro.svg') }}" alt="" class="absolute inset-2 w-16 h-16">
                    </div>
                    <h3 class="font-display text-2xl font-bold text-ink text-center">O Bruce está analisando…</h3>
                    <p class="text-slate mt-3 text-center max-w-sm">Estamos varrendo os dados enviados para montar seu diagnóstico premium. Isso pode levar até 30 segundos.</p>
                </div>

                <!-- coluna form -->
                <div class="p-8 md:p-12 md:w-3/5" x-data="{ tipo: 'site' }">
                    <form action="{{ route('analise.process') }}" method="POST" @submit="loading = true">
                        @csrf

                        <h3 class="font-display text-xl font-bold text-ink mb-6">Seus dados</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Nome</label>
                                <input type="text" name="nome" value="{{ old('nome') }}" required class="w-full rounded-xl border-gray-200 focus:border-bruce focus:ring-bruce bg-mist" placeholder="Ex: João Silva">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">WhatsApp</label>
                                <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" required class="w-full rounded-xl border-gray-200 focus:border-bruce focus:ring-bruce bg-mist" placeholder="(11) 99999-9999">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">E-mail corporativo</label>
                                <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-xl border-gray-200 focus:border-bruce focus:ring-bruce bg-mist" placeholder="voce@suaempresa.com.br">
                            </div>
                        </div>

                        <h3 class="font-display text-xl font-bold text-ink mb-6">O que vamos analisar?</h3>
                        <div class="space-y-5">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Tipo</label>
                                <select name="tipo_analise" x-model="tipo" class="w-full rounded-xl border-gray-200 focus:border-bruce focus:ring-bruce bg-mist">
                                    <option value="site">Meu site / landing page</option>
                                    <option value="redes_sociais">Meu perfil no Instagram / rede social</option>
                                    <option value="marca">Minha marca (vou descrevê-la)</option>
                                </select>
                            </div>
                            
                            <!-- Campos para SITE -->
                            <div x-show="tipo === 'site'" x-transition>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">URL do Site</label>
                                <input type="text" name="url_site" :required="tipo === 'site'" class="w-full rounded-xl border-gray-200 focus:border-bruce focus:ring-bruce bg-mist" placeholder="https://seudominio.com.br">
                                <p class="text-xs text-slate mt-2">O Bruce acessará o link para ler o conteúdo.</p>
                            </div>

                            <!-- Campos para REDES SOCIAIS -->
                            <div x-show="tipo === 'redes_sociais'" x-transition style="display: none;">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Seu @ do Instagram</label>
                                        <input type="text" name="url_social" :required="tipo === 'redes_sociais'" class="w-full rounded-xl border-gray-200 focus:border-bruce focus:ring-bruce bg-mist" placeholder="@sua_agencia">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Cole sua Bio atual exatamente como está</label>
                                        <textarea name="bio_social" rows="2" class="w-full rounded-xl border-gray-200 focus:border-bruce focus:ring-bruce bg-mist" placeholder="Ajudamos empresas a faturarem o triplo..."></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Qual o principal serviço/produto que você vende?</label>
                                        <textarea name="produto_social" rows="2" class="w-full rounded-xl border-gray-200 focus:border-bruce focus:ring-bruce bg-mist" placeholder="Consultoria B2B e Mentoria"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Campos para MARCA -->
                            <div x-show="tipo === 'marca'" x-transition style="display: none;">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Nome da Marca</label>
                                        <input type="text" name="url_marca" :required="tipo === 'marca'" class="w-full rounded-xl border-gray-200 focus:border-bruce focus:ring-bruce bg-mist" placeholder="Ex: Acme Corp">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Qual a sua promessa principal (Slogan/Pitch)?</label>
                                        <textarea name="promessa_marca" rows="2" class="w-full rounded-xl border-gray-200 focus:border-bruce focus:ring-bruce bg-mist" placeholder="Nós somos a única empresa que..."></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate mb-2">Quem é o seu público alvo?</label>
                                        <textarea name="publico_marca" rows="2" class="w-full rounded-xl border-gray-200 focus:border-bruce focus:ring-bruce bg-mist" placeholder="Mulheres de 25 a 35 anos..."></textarea>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <button type="submit" class="group w-full mt-10 bg-bruceInk hover:bg-bruce text-white py-4 rounded-2xl font-bold text-base transition-all shadow-xl shadow-bruceInk/20 inline-flex items-center justify-center gap-3">
                            <img src="{{ asset('images/bruce/bruceia-icone-fundo-escuro.svg') }}" alt="" class="w-6 h-6 group-hover:hidden">
                            <img src="{{ asset('images/bruce/bruceia-icone-fundo-claro.svg') }}" alt="" class="w-6 h-6 hidden group-hover:block">
                            Gerar análise agora com o Bruce
                        </button>

                        <p class="text-center text-xs text-slate mt-4">Ao enviar, você concorda em receber comunicações da NC5 Hub. Prometemos não enviar spam.</p>
                    </form>
                </div>

                <!-- coluna Bruce -->
                <div class="bg-bruceInk text-white p-8 md:p-12 md:w-2/5 flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute -top-20 -right-20 w-60 h-60 bg-bruce/20 rounded-full blur-[80px] pointer-events-none"></div>

                    <div class="relative">
                        <img src="{{ asset('images/bruce/bruceia-logo-fundo-escuro.svg') }}" alt="BruceIA" class="h-9 mb-8">
                        <h3 class="font-display text-2xl font-bold mb-4 leading-tight">O que você recebe do Bruce.</h3>
                        <ul class="space-y-3 text-sm text-white/80">
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-bruce mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                <span>Visão geral (primeira impressão profissional)</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-bruce mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                <span>Pontos fortes que já funcionam</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-bruce mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                <span>Oportunidades críticas para faturar mais</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-bruce mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                <span>Veredito estratégico da agência</span>
                            </li>
                        </ul>
                    </div>

                    <div class="relative mt-10 pt-6 border-t border-white/10">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-white/40">Ecossistema</p>
                        <p class="text-sm text-white/80 mt-1">Bruce faz parte da esteira NC5 Hub — do briefing ao pixel.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
