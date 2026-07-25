<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.contatos.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-500 hover:text-ink transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h2 class="font-display font-semibold text-xl text-ink leading-tight">
                    Mensagem de {{ $contato->nome }}
                </h2>
            </div>
            
            <div>
                @if($contato->status === 'novo')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800 border border-orange-200">
                        Novo
                    </span>
                @elseif($contato->status === 'lido')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-slate-100 text-slate-800 border border-slate-200">
                        Lido
                    </span>
                @elseif($contato->status === 'respondido')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Respondido
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12 font-sans">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Informações do Remetente -->
                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-slate-200 p-6">
                        <h3 class="font-display font-semibold text-ink text-lg mb-6 border-b border-slate-100 pb-3">Detalhes do Contato</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <span class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Nome</span>
                                <div class="font-medium text-ink">{{ $contato->nome }}</div>
                            </div>
                            
                            <div>
                                <span class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Email</span>
                                <a href="mailto:{{ $contato->email }}" class="text-bruce hover:underline flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    {{ $contato->email }}
                                </a>
                            </div>

                            @if($contato->whatsapp)
                            <div>
                                <span class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">WhatsApp</span>
                                @php
                                    $waNumber = preg_replace('/[^0-9]/', '', $contato->whatsapp);
                                    if(!str_starts_with($waNumber, '55')) {
                                        $waNumber = '55' . $waNumber;
                                    }
                                @endphp
                                <a href="https://wa.me/{{ $waNumber }}" target="_blank" class="text-emerald-600 hover:underline flex items-center gap-1 font-medium">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    {{ $contato->whatsapp }}
                                </a>
                            </div>
                            @endif

                            <div>
                                <span class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Data de Envio</span>
                                <div class="text-slate-800">{{ $contato->created_at->format('d/m/Y \à\s H:i') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-slate-200 p-6">
                        <h3 class="font-display font-semibold text-ink text-lg mb-4 border-b border-slate-100 pb-3">Ações</h3>
                        
                        <div class="space-y-4">
                            <!-- Update Status -->
                            <form action="{{ route('admin.contatos.status', $contato->id) }}" method="POST" class="space-y-3">
                                @csrf
                                @method('PUT')
                                
                                <label for="status" class="block text-sm font-medium text-slate-700">Alterar Status</label>
                                <div class="flex gap-2">
                                    <select name="status" id="status" class="block w-full rounded-lg border-slate-300 focus:border-bruce focus:ring focus:ring-bruce/20 text-sm">
                                        <option value="novo" {{ $contato->status === 'novo' ? 'selected' : '' }}>Novo</option>
                                        <option value="lido" {{ $contato->status === 'lido' ? 'selected' : '' }}>Lido</option>
                                        <option value="respondido" {{ $contato->status === 'respondido' ? 'selected' : '' }}>Respondido</option>
                                    </select>
                                    <button type="submit" class="bg-ink hover:bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap">
                                        Atualizar
                                    </button>
                                </div>
                            </form>

                            <hr class="border-slate-100">

                            <!-- Excluir -->
                            <form action="{{ route('admin.contatos.destroy', $contato->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta mensagem? Esta ação não pode ser desfeita.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Excluir Mensagem
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Mensagem -->
                <div class="md:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-slate-200 h-full">
                        <div class="p-6 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                            <div>
                                <span class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Assunto</span>
                                <h3 class="font-display font-semibold text-ink text-xl">{{ $contato->assunto }}</h3>
                            </div>
                            
                            @if($contato->whatsapp)
                                @php
                                    $waMsg = urlencode("Olá " . explode(' ', $contato->nome)[0] . ", estou entrando em contato através da NC5 Hub referente à sua mensagem sobre: " . $contato->assunto);
                                @endphp
                                <a href="https://wa.me/{{ $waNumber }}?text={{ $waMsg }}" target="_blank" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2 shadow-sm">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    Responder no WhatsApp
                                </a>
                            @endif
                        </div>
                        
                        <div class="p-6">
                            <div class="prose max-w-none text-slate-700 whitespace-pre-wrap font-sans text-base leading-relaxed">{{ $contato->mensagem }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</x-admin-layout>
