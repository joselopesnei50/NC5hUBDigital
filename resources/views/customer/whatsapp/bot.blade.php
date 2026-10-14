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

            {{-- Dados basicos --}}
            <div class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm">
                <h3 class="font-display font-bold text-lg text-ink mb-1">2. Identificação do seu negócio</h3>
                <p class="text-slate text-sm mb-5">Dados básicos — o bot usa pra se apresentar e orientar o cliente.</p>

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
                        <label class="block text-sm font-bold text-ink mb-1">Tom de voz desejado</label>
                        <input type="text" name="tom_de_voz" value="{{ old('tom_de_voz', $training->tom_de_voz) }}" maxlength="500"
                               placeholder="Ex: Descolado e divertido, usa gírias leves. OU: Formal e discreto, chama o cliente de senhor(a)."
                               class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none">
                        <p class="text-slate text-xs mt-1">Isso sobrescreve a personalidade padrão do segmento.</p>
                    </div>
                </div>
            </div>

            {{-- Fundamentos --}}
            <div class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm">
                <h3 class="font-display font-bold text-lg text-ink mb-1">3. Fundamentos do negócio</h3>
                <p class="text-slate text-sm mb-5">Aqui é onde o bot deixa de ser genérico e vira um especialista no SEU negócio. Capriche nessas respostas — elas são o diferencial.</p>

                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-ink mb-1">História e propósito</label>
                        <textarea name="historia_negocio" rows="4" placeholder="Conte brevemente a origem do negócio, por que ele existe e o que vocês acreditam. Ex: Começamos em 2015 na garagem de casa, usando a receita da minha avó. Nosso propósito é entregar comida de família, feita com carinho."
                                  class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none">{{ old('historia_negocio', $training->historia_negocio) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-ink mb-1">Diferenciais (o que vocês fazem diferente)</label>
                        <textarea name="diferenciais" rows="4" placeholder="Ex: Carne 100% artesanal, nunca congelada. Pão brioche produzido na casa todo dia. Entrega em moto própria para a comida não esfriar."
                                  class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none">{{ old('diferenciais', $training->diferenciais) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-ink mb-1">Como o produto/serviço é feito ou entregue</label>
                        <textarea name="processo_producao" rows="6" placeholder="Explique o processo. Ex: Nossos lanches são montados na hora do pedido. A carne é temperada por 24h antes de ir para a chapa. Entregamos em moto, levamos entre 25 e 40 minutos dependendo do bairro."
                                  class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none">{{ old('processo_producao', $training->processo_producao) }}</textarea>
                        <p class="text-slate text-xs mt-1">Esse campo faz MUITA diferença — quando o cliente pergunta "como é feito?", o bot responde com confiança.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-ink mb-1">Público-alvo (quem é o cliente ideal)</label>
                        <textarea name="publico_alvo" rows="3" placeholder="Ex: Famílias do bairro, casais que querem jantar fora, universitários na madrugada."
                                  class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none">{{ old('publico_alvo', $training->publico_alvo) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Catalogo + politicas + FAQ --}}
            <div class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm">
                <h3 class="font-display font-bold text-lg text-ink mb-1">4. Catálogo, políticas e perguntas</h3>
                <p class="text-slate text-sm mb-5">O conteúdo que o bot vai consultar pra responder preços, prazos e dúvidas.</p>

                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-ink mb-1">Catálogo / Menu / Serviços</label>
                        <textarea name="catalogo_texto" rows="8" placeholder="Liste produtos, preços, serviços. Pode copiar e colar sua lista — o bot entende texto corrido."
                                  class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none font-mono text-xs">{{ old('catalogo_texto', $training->catalogo_texto) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-ink mb-1">Políticas (troca, cancelamento, garantia)</label>
                        <textarea name="politicas" rows="4" placeholder="Ex: Cancelamento até 10min após o pedido sem custo. Troca em até 7 dias com nota fiscal. Garantia de 3 meses em todos os serviços."
                                  class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none">{{ old('politicas', $training->politicas) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-ink mb-1">FAQ — Perguntas e respostas frequentes</label>
                        <textarea name="faq_custom" rows="6" placeholder="Ex:&#10;P: Vocês entregam no bairro X?&#10;R: Sim, com taxa de R$ 5.&#10;&#10;P: Aceitam cartão?&#10;R: Sim, PIX, débito e crédito."
                                  class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none">{{ old('faq_custom', $training->faq_custom) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Guardrails --}}
            <div class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm">
                <h3 class="font-display font-bold text-lg text-ink mb-1">5. Regras e exemplos (opcional, mas poderoso)</h3>
                <p class="text-slate text-sm mb-5">Guardrails e exemplos reais que calibram o bot pro seu jeito.</p>

                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-ink mb-1">Regras e proibições</label>
                        <textarea name="regras_proibicoes" rows="4" placeholder="Ex: Nunca prometer entrega abaixo de 30 minutos. Nunca passar valor de serviço antes de ver o veículo. Nunca falar mal da concorrência."
                                  class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none">{{ old('regras_proibicoes', $training->regras_proibicoes) }}</textarea>
                        <p class="text-slate text-xs mt-1">Essas regras têm prioridade sobre tudo.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-ink mb-1">Exemplos reais de atendimento modelo</label>
                        <textarea name="cases_exemplo" rows="6" placeholder="Cole 2–3 conversas reais que foram ótimas. Ex:&#10;&#10;Cliente: Tem X?&#10;Bom atendente: Temos sim! É em qual tamanho? Grande ou pequeno?&#10;&#10;Cliente: Vocês entregam no Centro?&#10;Bom atendente: Entregamos sim, em 30min. A taxa é R$ 7."
                                  class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none">{{ old('cases_exemplo', $training->cases_exemplo) }}</textarea>
                        <p class="text-slate text-xs mt-1">O bot vai imitar fielmente o estilo desses exemplos.</p>
                    </div>
                </div>
            </div>

            {{-- Agendamento --}}
            <div class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm">
                <h3 class="font-display font-bold text-lg text-ink mb-1">6. Agendamentos pelo WhatsApp</h3>
                <p class="text-slate text-sm mb-5">O bot pode criar agendamentos direto na sua <a href="{{ route('customer.agenda.index') }}" class="text-bruce font-bold hover:underline">agenda</a>. Configure duração e lembrete:</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-ink mb-1">Duração padrão (minutos)</label>
                        <input type="number" name="duracao_padrao_atendimento_min"
                               value="{{ old('duracao_padrao_atendimento_min', $training->duracao_padrao_atendimento_min ?: 60) }}"
                               min="5" max="1440"
                               class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none">
                        <p class="text-slate text-xs mt-1">Quanto tempo dura um atendimento típico (ex: corte 45, consulta 30, visita 60).</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-ink mb-1">Lembrete — quantas horas antes</label>
                        <input type="number" name="reminder_hours_before"
                               value="{{ old('reminder_hours_before', $training->reminder_hours_before ?: 2) }}"
                               min="1" max="48"
                               class="w-full px-4 py-2 border border-black/10 rounded-xl text-sm focus:border-bruce focus:outline-none">
                        <p class="text-slate text-xs mt-1">O bot envia uma mensagem automática no WhatsApp N horas antes do horário.</p>
                    </div>
                </div>
            </div>

            {{-- Ativar --}}
            <div class="bg-white border border-black/5 rounded-3xl p-6 sm:p-8 shadow-sm">
                <h3 class="font-display font-bold text-lg text-ink mb-1">7. Ativar o bot</h3>
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
