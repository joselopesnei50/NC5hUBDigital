@extends('layouts.public')

@section('title', 'Contato')

@section('content')
<main class="min-h-screen bg-mist" x-data="{ loading: false }">
    <!-- Hero Section -->
    <section class="bg-ink text-white py-24 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPgo8cmVjdCB3aWR0aD0iOCIgaGVpZ2h0PSI4IiBmaWxsPSIjZmZmIj48L3JlY3Q+CjxwYXRoIGQ9Ik0wIDBMOCA4Wk04IDBMMCA4WiIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utd2lkdGg9IjEiPjwvcGF0aD4KPC9zdmc+')] mix-blend-overlay"></div>
        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10 text-center">
            <h1 class="font-display text-4xl md:text-5xl font-bold mb-4 tracking-tight">Fale com a NC5 Hub</h1>
            <p class="font-sans text-lg text-slate-300 max-w-2xl mx-auto">Nossa equipe está pronta para entender o seu negócio e encontrar a solução perfeita para a sua necessidade.</p>
        </div>
    </section>

    <!-- Content Section -->
    <section class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Left: Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 md:p-10 relative overflow-hidden">
                        
                        <!-- Loading Overlay -->
                        <div x-show="loading" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-50 flex flex-col items-center justify-center transition-all duration-300" style="display: none;">
                            <svg class="animate-spin h-10 w-10 text-bruce mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="font-sans font-medium text-ink">Enviando mensagem...</span>
                        </div>

                        <h2 class="font-display text-2xl font-bold text-ink mb-8">Envie uma mensagem</h2>

                        @if (session('success'))
                            <div class="mb-8 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 flex items-start">
                                <svg class="w-5 h-5 text-emerald-500 mt-0.5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="font-sans text-sm">{{ session('success') }}</div>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-8 bg-red-50 border border-red-200 text-red-800 rounded-xl p-4 flex items-start">
                                <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <ul class="font-sans text-sm list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('contato.store') }}" method="POST" @submit="loading = true" class="space-y-6">
                            @csrf
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nome -->
                                <div>
                                    <label for="nome" class="block font-sans text-sm font-medium text-slate-700 mb-1.5">Nome Completo *</label>
                                    <input type="text" name="nome" id="nome" required value="{{ old('nome') }}" class="w-full rounded-xl border-slate-300 focus:border-bruce focus:ring focus:ring-bruce/20 transition-colors font-sans py-2.5 px-4 bg-slate-50 focus:bg-white" placeholder="Seu nome completo">
                                </div>
                                <!-- WhatsApp -->
                                <div>
                                    <label for="whatsapp" class="block font-sans text-sm font-medium text-slate-700 mb-1.5">WhatsApp (Opcional)</label>
                                    <input type="text" name="whatsapp" id="whatsapp" value="{{ old('whatsapp') }}" class="w-full rounded-xl border-slate-300 focus:border-bruce focus:ring focus:ring-bruce/20 transition-colors font-sans py-2.5 px-4 bg-slate-50 focus:bg-white" placeholder="(00) 00000-0000">
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block font-sans text-sm font-medium text-slate-700 mb-1.5">Email corporativo *</label>
                                <input type="email" name="email" id="email" required value="{{ old('email') }}" class="w-full rounded-xl border-slate-300 focus:border-bruce focus:ring focus:ring-bruce/20 transition-colors font-sans py-2.5 px-4 bg-slate-50 focus:bg-white" placeholder="voce@suaempresa.com.br">
                            </div>

                            <!-- Assunto -->
                            <div>
                                <label for="assunto" class="block font-sans text-sm font-medium text-slate-700 mb-1.5">Assunto *</label>
                                <select name="assunto" id="assunto" required class="w-full rounded-xl border-slate-300 focus:border-bruce focus:ring focus:ring-bruce/20 transition-colors font-sans py-2.5 px-4 bg-slate-50 focus:bg-white text-slate-700">
                                    <option value="" disabled {{ old('assunto') ? '' : 'selected' }}>Selecione o assunto</option>
                                    <option value="Quero contratar um serviço" {{ old('assunto') == 'Quero contratar um serviço' ? 'selected' : '' }}>Quero contratar um serviço</option>
                                    <option value="Tenho dúvidas sobre os planos" {{ old('assunto') == 'Tenho dúvidas sobre os planos' ? 'selected' : '' }}>Tenho dúvidas sobre os planos</option>
                                    <option value="Preciso de suporte" {{ old('assunto') == 'Preciso de suporte' ? 'selected' : '' }}>Preciso de suporte</option>
                                    <option value="Parceria ou Indicação" {{ old('assunto') == 'Parceria ou Indicação' ? 'selected' : '' }}>Parceria ou Indicação</option>
                                    <option value="Outro assunto" {{ old('assunto') == 'Outro assunto' ? 'selected' : '' }}>Outro assunto</option>
                                </select>
                            </div>

                            <!-- Mensagem -->
                            <div>
                                <label for="mensagem" class="block font-sans text-sm font-medium text-slate-700 mb-1.5">Como podemos ajudar? *</label>
                                <textarea name="mensagem" id="mensagem" rows="5" required class="w-full rounded-xl border-slate-300 focus:border-bruce focus:ring focus:ring-bruce/20 transition-colors font-sans py-3 px-4 bg-slate-50 focus:bg-white resize-none" placeholder="Descreva sua necessidade...">{{ old('mensagem') }}</textarea>
                            </div>

                            <!-- Botão -->
                            <div class="pt-4">
                                <button type="submit" class="w-full md:w-auto bg-bruce hover:bg-orange-600 text-white font-sans font-semibold py-3 px-8 rounded-xl transition-all duration-200 shadow-sm hover:shadow active:scale-95 flex items-center justify-center gap-2">
                                    <span>Enviar mensagem</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right: Info -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-start gap-4 hover:border-bruce/30 transition-colors group">
                        <div class="w-12 h-12 bg-bruce/10 text-bruce rounded-xl flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-display font-semibold text-ink mb-1">WhatsApp</h3>
                            <p class="font-sans text-slate-600 text-sm">(11) 99999-9999</p>
                            <a href="https://wa.me/5511999999999" class="font-sans text-sm text-bruce font-medium mt-2 inline-block hover:underline" target="_blank">Iniciar conversa &rarr;</a>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-start gap-4 hover:border-bruce/30 transition-colors group">
                        <div class="w-12 h-12 bg-bruce/10 text-bruce rounded-xl flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-display font-semibold text-ink mb-1">Email</h3>
                            <p class="font-sans text-slate-600 text-sm">contato@nc5hub.com.br</p>
                            <a href="mailto:contato@nc5hub.com.br" class="font-sans text-sm text-bruce font-medium mt-2 inline-block hover:underline">Enviar email &rarr;</a>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-start gap-4 hover:border-bruce/30 transition-colors group">
                        <div class="w-12 h-12 bg-bruce/10 text-bruce rounded-xl flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-display font-semibold text-ink mb-1">Localização</h3>
                            <p class="font-sans text-slate-600 text-sm leading-relaxed">São Paulo - SP<br>Atendimento online para todo o Brasil.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
