<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold uppercase tracking-widest bg-orange-50 text-orange-700 border border-orange-200">
                        Hub NC5 — Memória Unificada
                    </span>
                    @if($profile->is_active)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Bot Ativo no WhatsApp
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-gray-100 text-gray-600 border border-gray-200">
                            Modo Treinamento (Pausado)
                        </span>
                    @endif
                </div>
                <h2 class="font-display font-extrabold text-2xl sm:text-3xl text-[#0A1128] tracking-tight">
                    Central de Treinamento do Agente IA
                </h2>
                <p class="text-[#64748B] text-xs sm:text-sm mt-1">
                    Configure a inteligência de atendimento no WhatsApp e sincronize com o catálogo e regras do Hub NC5.
                </p>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                <form method="POST" action="{{ route('admin.hub.whatsapp.bot.sync') }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-[#0A1128] text-white hover:bg-orange-600 transition shadow-2xs cursor-pointer">
                        <span>🚀</span>
                        <span>Carregar / Restaurar Treinamento Oficial NC5 (BruceIA 100%)</span>
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div x-data="botTrainingPage({
            initialSegment: @js(old('segment', $profile->segment ?: 'nc5_hub_closer')),
            templates: @js($quickTemplates ?? []),
            simulateUrl: @js(route('admin.hub.whatsapp.bot.simulate')),
            csrfToken: @js(csrf_token())
         })"
         class="space-y-6">

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-2xl text-sm font-medium flex items-center gap-2.5 shadow-2xs">
                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold shrink-0">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-3.5 rounded-2xl text-sm">
                <ul class="list-disc ml-5 space-y-1">
                    @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div x-show="templateToast" x-cloak
             x-transition
             class="fixed bottom-6 right-6 z-50 bg-[#0A1128] text-white px-5 py-3.5 rounded-2xl shadow-xl flex items-center gap-3 text-xs font-bold border border-white/10">
            <span class="w-5 h-5 rounded-full bg-orange-500 text-white flex items-center justify-center">✨</span>
            <span x-text="templateToastMessage"></span>
        </div>

        {{-- Painel de Maturidade --}}
        <div class="bg-white border border-gray-200 rounded-3xl p-6 sm:p-7 shadow-2xs">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                <div class="lg:col-span-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-extrabold uppercase tracking-widest text-orange-600">Nível de Treinamento da IA</p>
                            <h3 class="font-display font-extrabold text-xl text-[#0A1128] mt-0.5">{{ $readiness['level'] }}</h3>
                        </div>
                        <div class="text-right">
                            <span class="font-display font-black text-3xl {{ $readiness['score'] >= 75 ? 'text-emerald-600' : 'text-orange-500' }}">
                                {{ $readiness['score'] }}%
                            </span>
                        </div>
                    </div>

                    <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500 {{ $readiness['score'] >= 75 ? 'bg-emerald-500' : 'bg-orange-500' }}"
                             style="width: {{ $readiness['score'] }}%"></div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span>📦</span>
                            <span><strong>{{ $catalogCount }}</strong> item(ns) no Catálogo</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                            <span>📚</span>
                            <span><strong>{{ $knowledgeCount }}</strong> diretriz(es) na Base do Bruce</span>
                        </span>
                    </div>
                </div>

                <div class="lg:col-span-7 border-t lg:border-t-0 lg:border-l border-gray-100 pt-4 lg:pt-0 lg:pl-6">
                    <div class="flex items-center justify-between mb-2.5">
                        <p class="text-xs font-bold text-[#0A1128] uppercase tracking-wider">Checklist de Inteligência</p>
                        <button type="button"
                                @click="applySegmentTemplate(false)"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-orange-50 text-orange-700 border border-orange-200 hover:bg-orange-100 transition cursor-pointer">
                            <span>✨</span>
                            <span>Preencher Campos Vazios com Modelo</span>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach($readiness['items'] as $item)
                            <div class="flex items-start gap-2 p-2 rounded-xl {{ $item['done'] ? 'bg-emerald-50/50' : 'bg-gray-50' }}">
                                @if($item['done'])
                                    <span class="w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">✓</span>
                                @else
                                    <span class="w-4 h-4 rounded-full border-2 border-gray-300 shrink-0 mt-0.5"></span>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-xs font-bold {{ $item['done'] ? 'text-[#0A1128]' : 'text-gray-600' }}">{{ $item['label'] }}</p>
                                    <p class="text-[11px] text-gray-500 truncate">{{ $item['hint'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <form method="POST" action="{{ route('admin.hub.whatsapp.bot.update') }}" class="lg:col-span-8 space-y-6">
                @csrf

                <div class="bg-white border border-gray-200 rounded-3xl p-6 sm:p-7 shadow-2xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                        <div>
                            <h3 class="font-display font-bold text-lg text-[#0A1128]">1. Segmento &amp; Comportamento Base</h3>
                            <p class="text-gray-500 text-xs sm:text-sm">Escolha o perfil base de atendimento.</p>
                        </div>
                        <button type="button"
                                @click="applySegmentTemplate(true)"
                                class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-gray-100 hover:bg-orange-50 hover:text-orange-700 text-gray-700 transition cursor-pointer">
                            <span>📋</span>
                            <span>Carregar Exemplo do Segmento</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($segments as $key => $seg)
                            <label class="flex items-start gap-3 p-4 border rounded-2xl cursor-pointer transition hover:border-orange-500"
                                   :class="selectedSegment === '{{ $key }}' ? 'border-orange-500 bg-orange-50/40 ring-1 ring-orange-500' : 'border-gray-200'">
                                <input type="radio" name="segment" value="{{ $key }}"
                                       x-model="selectedSegment"
                                       class="mt-1 text-orange-600 focus:ring-orange-500" required>
                                <div>
                                    <p class="font-bold text-[#0A1128] text-sm">{{ $seg['label'] }}</p>
                                    <p class="text-gray-500 text-xs mt-1 line-clamp-2">{{ $seg['persona'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-3xl p-6 sm:p-7 shadow-2xs">
                    <h3 class="font-display font-bold text-lg text-[#0A1128] mb-1">2. Identidade &amp; Tom de Voz</h3>
                    <p class="text-gray-500 text-xs sm:text-sm mb-5">Dados básicos usados pelo agente para se apresentar.</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Nome do Negócio</label>
                            <input type="text" name="business_name" value="{{ old('business_name', $training->business_name) }}" maxlength="255"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:border-orange-500 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Horário de Atendimento</label>
                            <input type="text" name="horario_atendimento" value="{{ old('horario_atendimento', $training->horario_atendimento) }}" maxlength="255"
                                   placeholder="Ex: Seg–Sex 9h às 18h"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:border-orange-500 focus:ring-orange-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Endereço</label>
                            <input type="text" name="endereco" value="{{ old('endereco', $training->endereco) }}" maxlength="255"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:border-orange-500 focus:ring-orange-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Tom de Voz Desejado</label>
                            <input type="text" name="tom_de_voz" id="field_tom_de_voz" value="{{ old('tom_de_voz', $training->tom_de_voz) }}" maxlength="500"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:border-orange-500 focus:ring-orange-500">
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-3xl p-6 sm:p-7 shadow-2xs">
                    <h3 class="font-display font-bold text-lg text-[#0A1128] mb-1">3. DNA, Diferenciais &amp; Como Funciona</h3>
                    <div class="grid grid-cols-1 gap-5 mt-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Diferenciais Competitivos</label>
                            <textarea name="diferenciais" id="field_diferenciais" rows="3"
                                      class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:border-orange-500 focus:ring-orange-500">{{ old('diferenciais', $training->diferenciais) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Como o Produto/Serviço é Feito ou Entregue</label>
                            <textarea name="processo_producao" id="field_processo_producao" rows="4"
                                      class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:border-orange-500 focus:ring-orange-500">{{ old('processo_producao', $training->processo_producao) }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">História e Propósito</label>
                                <textarea name="historia_negocio" rows="3"
                                          class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:border-orange-500 focus:ring-orange-500">{{ old('historia_negocio', $training->historia_negocio) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Público-Alvo</label>
                                <textarea name="publico_alvo" rows="3"
                                          class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:border-orange-500 focus:ring-orange-500">{{ old('publico_alvo', $training->publico_alvo) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-3xl p-6 sm:p-7 shadow-2xs">
                    <h3 class="font-display font-bold text-lg text-[#0A1128] mb-1">4. Catálogo, Políticas &amp; FAQ</h3>
                    <div class="grid grid-cols-1 gap-5 mt-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Catálogo / Menu / Serviços em Texto</label>
                            <textarea name="catalogo_texto" rows="6"
                                      class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:border-orange-500 focus:ring-orange-500 font-mono text-xs">{{ old('catalogo_texto', $training->catalogo_texto) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Políticas</label>
                            <textarea name="politicas" id="field_politicas" rows="4"
                                      class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:border-orange-500 focus:ring-orange-500">{{ old('politicas', $training->politicas) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">FAQ — Perguntas e Respostas Frequentes</label>
                            <textarea name="faq_custom" id="field_faq_custom" rows="5"
                                      class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:border-orange-500 focus:ring-orange-500">{{ old('faq_custom', $training->faq_custom) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-3xl p-6 sm:p-7 shadow-2xs">
                    <h3 class="font-display font-bold text-lg text-[#0A1128] mb-1">5. Guardrails &amp; Exemplos</h3>
                    <div class="grid grid-cols-1 gap-5 mt-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Regras e Proibições</label>
                            <textarea name="regras_proibicoes" id="field_regras_proibicoes" rows="4"
                                      class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:border-orange-500 focus:ring-orange-500">{{ old('regras_proibicoes', $training->regras_proibicoes) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Exemplos Reais de Atendimento Modelo</label>
                            <textarea name="cases_exemplo" rows="4"
                                      class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:border-orange-500 focus:ring-orange-500">{{ old('cases_exemplo', $training->cases_exemplo) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-3xl p-6 sm:p-7 shadow-2xs">
                    <h3 class="font-display font-bold text-lg text-[#0A1128] mb-1">6. Agendamentos pelo WhatsApp</h3>
                    <p class="text-gray-500 text-xs sm:text-sm mb-5">
                        Integração direta com a <a href="{{ route('admin.hub.agenda.index') }}" class="text-orange-600 font-bold hover:underline">Agenda do Hub</a>.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Duração Padrão (min)</label>
                            <input type="number" name="duracao_padrao_atendimento_min"
                                   value="{{ old('duracao_padrao_atendimento_min', $training->duracao_padrao_atendimento_min ?: 60) }}"
                                   min="5" max="1440"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">1º Lembrete (Horas antes)</label>
                            <input type="number" name="reminder_hours_before"
                                   value="{{ old('reminder_hours_before', $training->reminder_hours_before ?: 2) }}"
                                   min="1" max="48"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Nome do Recurso</label>
                            <input type="text" name="resource_label"
                                   value="{{ old('resource_label', $training->resource_label ?: 'Profissional') }}"
                                   maxlength="40"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm">
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-4">
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="enable_second_reminder" value="1"
                                   {{ old('enable_second_reminder', $training->enable_second_reminder) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded text-orange-600">
                            <span class="text-xs sm:text-sm font-bold text-[#0A1128]">Enviar segundo lembrete antecipado</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">Horas antes:</span>
                            <input type="number" name="reminder_hours_before_second"
                                   value="{{ old('reminder_hours_before_second', $training->reminder_hours_before_second ?: 24) }}"
                                   min="1" max="168"
                                   class="w-20 px-3 py-1.5 border border-gray-300 rounded-xl text-xs">
                            <a href="{{ route('admin.hub.agenda.recursos.index') }}" class="ml-2 text-xs font-bold text-orange-600 hover:underline">
                                Gerenciar Recursos &rarr;
                            </a>
                        </div>
                    </div>

                    <div class="mt-5 border-t border-gray-100 pt-5">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">🔔 Notificações do Gestor no WhatsApp</p>
                        <p class="text-gray-500 text-xs mb-3">Receba no seu celular um aviso sempre que a BruceIA agendar, o cliente confirmar ou cancelar um horário. As notificações também aparecem no sininho do painel.</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-start">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">WhatsApp do Gestor (com DDD)</label>
                                <input type="tel" name="manager_notification_phone"
                                       value="{{ old('manager_notification_phone', $training->manager_notification_phone) }}"
                                       maxlength="32" placeholder="Ex: 11 99999-8888"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:border-orange-500 focus:ring-orange-500">
                                <p class="text-gray-400 text-xs mt-1">Deixe em branco para receber apenas no painel. Use um número diferente do WhatsApp conectado ao bot.</p>
                            </div>
                            <div class="space-y-2 sm:pt-6">
                                @foreach([
                                    'notify_manager_on_booking' => '📅 Novo agendamento',
                                    'notify_manager_on_confirm' => '✅ Cliente confirmou presença',
                                    'notify_manager_on_cancel'  => '❌ Cliente cancelou (vaga reaberta)',
                                ] as $nKey => $nLabel)
                                    <label class="flex items-center gap-2.5 cursor-pointer">
                                        <input type="hidden" name="{{ $nKey }}" value="0">
                                        <input type="checkbox" name="{{ $nKey }}" value="1"
                                               {{ old($nKey, $training->{$nKey} ?? true) ? 'checked' : '' }}
                                               class="w-4 h-4 rounded text-orange-600 focus:ring-orange-500">
                                        <span class="text-xs sm:text-sm font-bold text-[#0A1128]">{{ $nLabel }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 border-t border-gray-100 pt-5">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-700 mb-3">Horário Comercial</p>
                        @php
                            $horario = old('horario_comercial', $training->horario_comercial ?? []);
                            $dias = \App\Models\WhatsappBotTraining::DIAS_SEMANA;
                            $labels = \App\Models\WhatsappBotTraining::DIAS_SEMANA_LABEL;
                        @endphp
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            @foreach($dias as $dia)
                                @php
                                    $d = $horario[$dia] ?? [];
                                    $aberto = (bool) ($d['aberto'] ?? false);
                                    $abre = $d['abre'] ?? '09:00';
                                    $fecha = $d['fecha'] ?? '18:00';
                                @endphp
                                <div class="flex items-center justify-between gap-2 p-2.5 rounded-xl border border-gray-100 bg-gray-50/60">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="horario_comercial[{{ $dia }}][aberto]" value="1"
                                               {{ $aberto ? 'checked' : '' }}
                                               class="rounded text-orange-600">
                                        <span class="text-xs font-bold text-[#0A1128] w-16">{{ $labels[$dia] }}</span>
                                    </label>
                                    <div class="flex items-center gap-1.5">
                                        <input type="time" name="horario_comercial[{{ $dia }}][abre]" value="{{ $abre }}"
                                               class="px-2 py-1 border border-gray-200 rounded-lg text-xs bg-white">
                                        <span class="text-gray-400 text-xs">às</span>
                                        <input type="time" name="horario_comercial[{{ $dia }}][fecha]" value="{{ $fecha }}"
                                               class="px-2 py-1 border border-gray-200 rounded-lg text-xs bg-white">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-3xl p-6 sm:p-7 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $profile->is_active) ? 'checked' : '' }}
                               class="w-5 h-5 rounded text-emerald-600">
                        <div>
                            <span class="block text-sm font-bold text-[#0A1128]">Ativar Agente IA no WhatsApp</span>
                            <span class="block text-xs text-gray-500">Responde automaticamente novas mensagens recebidas.</span>
                        </div>
                    </label>

                    <button type="submit"
                            class="px-6 py-3 bg-[#0A1128] hover:bg-orange-600 text-white rounded-2xl font-bold text-sm transition shadow-sm shrink-0 cursor-pointer">
                        Salvar e Treinar Agente
                    </button>
                </div>
            </form>

            {{-- Simulador Lateral --}}
            <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-6">
                <div class="bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm flex flex-col h-[560px]">
                    <div class="bg-[#075E54] text-white px-5 py-4 flex items-center justify-between shrink-0">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-full bg-emerald-500/30 border border-white/20 flex items-center justify-center font-bold text-sm shrink-0">
                                🤖
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-bold text-sm leading-tight truncate">Simulador do Agente IA</h4>
                                <p class="text-[11px] text-emerald-100">Teste em tempo real</p>
                            </div>
                        </div>
                        <button type="button"
                                @click="clearSimulation()"
                                class="text-[11px] font-semibold bg-white/10 hover:bg-white/20 px-2.5 py-1 rounded-lg transition cursor-pointer">
                            Limpar
                        </button>
                    </div>

                    <div id="sim-messages" class="flex-1 p-4 overflow-y-auto space-y-3 bg-[#ECE5DD]/60 text-xs">
                        <div class="bg-amber-50 border border-amber-200/80 text-amber-900 px-3 py-2 rounded-xl text-[11px] leading-relaxed">
                            💡 Salve as alterações no formulário e envie uma pergunta abaixo para testar o comportamento do agente!
                        </div>

                        <template x-for="(m, idx) in simHistory" :key="idx">
                            <div :class="m.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                                <div :class="m.role === 'user'
                                        ? 'bg-[#DCF8C6] text-gray-900 rounded-2xl rounded-tr-xs'
                                        : 'bg-white text-gray-900 rounded-2xl rounded-tl-xs border border-gray-200/60'"
                                     class="max-w-[85%] px-3.5 py-2.5 shadow-2xs whitespace-pre-wrap leading-relaxed">
                                    <p x-text="m.content"></p>
                                    <span x-show="m.elapsed" class="block text-[9px] text-gray-400 text-right mt-1" x-text="m.elapsed + ' ms'"></span>
                                </div>
                            </div>
                        </template>

                        <div x-show="simLoading" class="flex justify-start">
                            <div class="bg-white text-gray-500 px-4 py-2.5 rounded-2xl rounded-tl-xs shadow-2xs text-xs font-medium flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                <span>Digitando resposta...</span>
                            </div>
                        </div>
                    </div>

                    <div class="px-3 py-2 bg-gray-50 border-t border-gray-200 flex items-center gap-1.5 overflow-x-auto no-scrollbar shrink-0">
                        <button type="button" @click="sendSimulated('Oi, vi o anúncio de vocês! Tenho uma empresa e quero saber como funciona.')"
                                class="px-2.5 py-1 rounded-full bg-white border border-gray-200 hover:border-emerald-500 text-[11px] font-medium text-gray-700 shrink-0 cursor-pointer">
                            🎯 Novo Lead
                        </button>
                        <button type="button" @click="sendSimulated('Quanto custa esse sistema e os serviços da NC5?')"
                                class="px-2.5 py-1 rounded-full bg-white border border-gray-200 hover:border-emerald-500 text-[11px] font-medium text-gray-700 shrink-0 cursor-pointer">
                            💰 Objeção Preço
                        </button>
                        <button type="button" @click="sendSimulated('Sou cliente da NC5: como faço pra IA parar de responder uma conversa específica?')"
                                class="px-2.5 py-1 rounded-full bg-white border border-gray-200 hover:border-emerald-500 text-[11px] font-medium text-gray-700 shrink-0 cursor-pointer">
                            🛠️ Dúvida BruceOS
                        </button>
                    </div>

                    <form @submit.prevent="sendSimulated()" class="p-3 bg-white border-t border-gray-200 flex items-center gap-2 shrink-0">
                        <input type="text"
                               x-model="simInput"
                               :disabled="simLoading"
                               placeholder="Digite como um cliente..."
                               class="flex-1 px-3.5 py-2 rounded-full border border-gray-300 text-xs focus:border-emerald-600 focus:ring-emerald-600">
                        <button type="submit"
                                :disabled="simLoading || !simInput.trim()"
                                class="w-9 h-9 rounded-full bg-[#075E54] hover:bg-emerald-700 disabled:opacity-40 text-white flex items-center justify-center transition shrink-0 cursor-pointer">
                            ➤
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function botTrainingPage({ initialSegment, templates, simulateUrl, csrfToken }) {
            return {
                selectedSegment: initialSegment || 'nc5_hub_closer',
                templates: templates || {},
                simulateUrl,
                csrfToken,
                templateToast: false,
                templateToastMessage: '',
                simInput: '',
                simLoading: false,
                simHistory: [],

                applySegmentTemplate(overwrite = false) {
                    const tpl = this.templates[this.selectedSegment];
                    if (!tpl) return;

                    const fields = [
                        'tom_de_voz',
                        'diferenciais',
                        'processo_producao',
                        'politicas',
                        'faq_custom',
                        'regras_proibicoes'
                    ];

                    let filledCount = 0;
                    fields.forEach((f) => {
                        const el = document.getElementById('field_' + f);
                        if (el && tpl[f]) {
                            if (overwrite || !el.value.trim()) {
                                el.value = tpl[f];
                                filledCount++;
                            }
                        }
                    });

                    this.templateToastMessage = filledCount > 0
                        ? `Sugestões preenchidas em ${filledCount} campo(s)! Revise e clique em Salvar.`
                        : 'Todos os campos já estão preenchidos.';
                    this.templateToast = true;
                    setTimeout(() => { this.templateToast = false; }, 4000);
                },

                clearSimulation() {
                    this.simHistory = [];
                },

                async sendSimulated(presetText = null) {
                    const text = (presetText ?? this.simInput).trim();
                    if (!text || this.simLoading) return;

                    if (!presetText) {
                        this.simInput = '';
                    }

                    const historyPayload = this.simHistory.map(h => ({
                        role: h.role,
                        content: h.content
                    }));

                    this.simHistory.push({ role: 'user', content: text });
                    this.simLoading = true;
                    this.scrollSim();

                    try {
                        const res = await fetch(this.simulateUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken
                            },
                            body: JSON.stringify({
                                message: text,
                                history: historyPayload,
                                segment: this.selectedSegment
                            })
                        });
                        const data = await res.json();
                        this.simHistory.push({
                            role: 'assistant',
                            content: data.reply || 'Sem resposta.',
                            elapsed: data.elapsed_ms || null
                        });
                    } catch (err) {
                        this.simHistory.push({
                            role: 'assistant',
                            content: 'Erro de conexão ao testar o simulador.'
                        });
                    } finally {
                        this.simLoading = false;
                        this.scrollSim();
                    }
                },

                scrollSim() {
                    setTimeout(() => {
                        const box = document.getElementById('sim-messages');
                        if (box) box.scrollTop = box.scrollHeight;
                    }, 50);
                }
            };
        }
    </script>
</x-admin-layout>
