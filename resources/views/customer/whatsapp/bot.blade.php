<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold text-bruce uppercase tracking-widest mb-2">Atendimento</p>
            <h2 class="font-display font-bold text-3xl text-ink leading-tight">Bot IA — Configuração</h2>
            <p class="text-slate text-sm mt-1">Escolha o segmento do seu negócio, preencha os dados e deixe a IA atender seus clientes automaticamente.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3 rounded-xl text-sm">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-3 rounded-xl text-sm">
                <ul class="list-disc ml-5">
                    @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('customer.whatsapp.bot.update') }}" class="space-y-6">
            @csrf

            {{-- Segmento --}}
            <div class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm">
                <h3 class="font-display font-bold text-lg text-ink mb-1">1. Qual é o seu negócio?</h3>
                <p class="text-slate text-sm mb-5">O bot vai partir de uma base pronta pro seu segmento. Depois você personaliza com os campos abaixo.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($segments as $key => $seg)
                        <label class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer transition hover:border-bruce {{ ($profile->segment ?? '') === $key ? 'border-bruce bg-bruce/5' : 'border-black/10' }}">
                            <input type="radio" name="segment" value="{{ $key }}" {{ ($profile->segment ?? '') === $key ? 'checked' : '' }} class="mt-1" required>
                            <div>
                                <p class="font-bold text-ink text-sm">{{ $seg['label'] }}</p>
                                <p class="text-slate text-xs mt-1 line-clamp-2">{{ $seg['persona'] }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Dados do negocio --}}
            <div class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm">
                <h3 class="font-display font-bold text-lg text-ink mb-1">2. Dados do seu negócio</h3>
                <p class="text-slate text-sm mb-5">Quanto mais você preencher, melhor o bot responde. Nada aqui é obrigatório, mas tudo ajuda.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-ink mb-1">Nome do negócio</label>
                        <input type="text" name="business_name" value="{{ old('business_name', $training->business_name) }}" maxlength="255"
                               class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-ink mb-1">Horário de atendimento</label>
                        <input type="text" name="horario_atendimento" value="{{ old('horario_atendimento', $training->horario_atendimento) }}" maxlength="255" placeholder="Ex: Seg–Sex 9h às 18h, Sáb 9h às 13h"
                               class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-bold text-ink mb-1">Endereço</label>
                        <input type="text" name="endereco" value="{{ old('endereco', $training->endereco) }}" maxlength="255" placeholder="Rua, número, bairro, cidade"
                               class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-bold text-ink mb-1">Catálogo / Menu / Serviços</label>
                        <textarea name="catalogo_texto" rows="8" placeholder="Liste produtos, preços, serviços. Pode copiar e colar sua lista — o bot entende texto corrido."
                                  class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none font-mono text-xs">{{ old('catalogo_texto', $training->catalogo_texto) }}</textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-bold text-ink mb-1">FAQ — Perguntas e respostas frequentes</label>
                        <textarea name="faq_custom" rows="6" placeholder="Ex:&#10;P: Vocês entregam no bairro X?&#10;R: Sim, com taxa de R$ 5.&#10;&#10;P: Aceitam cartão?&#10;R: Sim, PIX, débito e crédito."
                                  class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none">{{ old('faq_custom', $training->faq_custom) }}</textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-bold text-ink mb-1">Regras e proibições</label>
                        <textarea name="regras_proibicoes" rows="4" placeholder="Ex: Nunca prometer entrega abaixo de 30 minutos. Nunca passar valor de serviço antes de ver o veículo."
                                  class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none">{{ old('regras_proibicoes', $training->regras_proibicoes) }}</textarea>
                        <p class="text-slate text-xs mt-1">Essas regras têm prioridade sobre a base pronta.</p>
                    </div>
                </div>
            </div>

            {{-- Ativar --}}
            <div class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm">
                <h3 class="font-display font-bold text-lg text-ink mb-1">3. Ativar o bot</h3>
                <p class="text-slate text-sm mb-5">Quando ativado, o bot responde automaticamente toda mensagem recebida. Você pode pausar o bot em uma conversa específica pelo inbox.</p>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $profile->is_active) ? 'checked' : '' }} class="w-5 h-5 rounded">
                    <span class="text-sm font-bold text-ink">Ativar bot para responder automaticamente</span>
                </label>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-3 bg-bruce hover:bg-bruce/90 text-white rounded-xl font-bold text-sm transition-colors">
                    Salvar configurações
                </button>
            </div>
        </form>

        @if($profile->exists && $profile->segment)
            <div class="bg-slate-50 border border-black/5 rounded-3xl p-6 sm:p-8">
                <h3 class="font-display font-bold text-lg text-ink mb-3">Prévia da base pronta — {{ $segments[$profile->segment]['label'] }}</h3>
                <p class="text-slate text-sm mb-4">É assim que o bot vai se comportar antes do seu treinamento. Seus campos acima refinam esse comportamento.</p>
                <div class="bg-white border border-black/5 rounded-xl p-4 text-xs text-slate whitespace-pre-wrap font-mono">{{ $segments[$profile->segment]['prompt_base'] }}</div>
            </div>
        @endif
    </div>
</x-app-layout>
