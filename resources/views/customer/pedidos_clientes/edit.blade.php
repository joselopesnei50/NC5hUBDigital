<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Pedido') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if($pedido->nome_aprovacao)
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-md shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">
                                <strong>Pedido aprovado eletronicamente por:</strong> {{ $pedido->nome_aprovacao }}<br>
                                <span class="text-xs">Registrado em {{ $pedido->data_aprovacao->format('d/m/Y \à\s H:i') }} pelo IP: {{ $pedido->ip_aprovacao }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-md shadow-sm flex items-center justify-between">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">Este pedido ainda não foi assinado digitalmente pelo cliente final.</p>
                        </div>
                    </div>
                    @if($pedido->token_publico)
                        <button type="button" onclick="navigator.clipboard.writeText('{{ route('public.pedido.show', $pedido->token_publico) }}'); alert('Copiado!')" class="bg-blue-600 text-white px-3 py-1 text-xs rounded shadow hover:bg-blue-700">Copiar Link do Orçamento</button>
                    @endif
                </div>
            @endif

            <div class="mb-6 flex flex-wrap gap-4">
                <form action="{{ route('customer.pedidos.enviar-email', $pedido->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 flex items-center transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Enviar por E-mail
                    </button>
                </form>

                @php
                    $telefoneLimpo = preg_replace('/[^0-9]/', '', $pedido->clienteFinal->telefone);
                    $textoWhatsApp = urlencode("Olá {$pedido->clienteFinal->nome_responsavel}, segue o link da nossa proposta/pedido: " . route('public.pedido.show', $pedido->token_publico ?? ''));
                @endphp
                <a href="https://api.whatsapp.com/send?phone={{ $telefoneLimpo }}&text={{ $textoWhatsApp }}" target="_blank" class="bg-green-500 text-white px-4 py-2 rounded shadow hover:bg-green-600 flex items-center transition">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
65:                     </svg>
66:                     Enviar por WhatsApp
67:                 </a>
68:             </div>
69: 
70:             <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
71:                 <div class="p-6 bg-white border-b border-gray-200">
72:                     
73:                     <form action="{{ route('customer.pedidos.update', $pedido->id) }}" method="POST">
74:                         @csrf
75:                         @method('PUT')
76:                         
77:                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
78:                             
79:                             {{-- Cliente --}}
80:                             <div class="md:col-span-2">
81:                                 <label for="cliente_final_id" class="block font-medium text-sm text-gray-700">Selecione o Cliente *</label>
82:                                 <select name="cliente_final_id" id="cliente_final_id" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
83:                                     <option value="">Selecione um cliente...</option>
84:                                     @foreach($clientesFinais as $cliente)
85:                                         <option value="{{ $cliente->id }}" {{ old('cliente_final_id', $pedido->cliente_final_id) == $cliente->id ? 'selected' : '' }}>
86:                                             {{ $cliente->nome_empresa }} {{ $cliente->nome_responsavel ? '('.$cliente->nome_responsavel.')' : '' }}
87:                                         </option>
88:                                     @endforeach
89:                                 </select>
90:                                 @error('cliente_final_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
91:                             </div>
92: 
93:                             {{-- Título --}}
94:                             <div class="md:col-span-2">
95:                                 <label for="titulo" class="block font-medium text-sm text-gray-700">Título Geral do Pedido / Projeto *</label>
96:                                 <input type="text" name="titulo" id="titulo" value="{{ old('titulo', $pedido->titulo) }}" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
97:                                 @error('titulo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
98:                             </div>
99: 
100:                             {{-- Status --}}
101:                             <div>
102:                                 <label for="status" class="block font-medium text-sm text-gray-700">Status *</label>
103:                                 <select name="status" id="status" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
104:                                     <option value="Orçamento" {{ old('status', $pedido->status) == 'Orçamento' ? 'selected' : '' }}>Orçamento</option>
105:                                     <option value="Aguardando Pagamento" {{ old('status', $pedido->status) == 'Aguardando Pagamento' ? 'selected' : '' }}>Aguardando Pagamento</option>
106:                                     <option value="Em Andamento" {{ old('status', $pedido->status) == 'Em Andamento' ? 'selected' : '' }}>Em Andamento</option>
107:                                     <option value="Concluído" {{ old('status', $pedido->status) == 'Concluído' ? 'selected' : '' }}>Concluído</option>
108:                                     <option value="Cancelado" {{ old('status', $pedido->status) == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
109:                                 </select>
110:                                 @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
111:                             </div>
112: 
113:                             {{-- Data do Pedido --}}
114:                             <div>
115:                                 <label for="data_pedido" class="block font-medium text-sm text-gray-700">Data do Pedido</label>
116:                                 <input type="date" name="data_pedido" id="data_pedido" value="{{ old('data_pedido', $pedido->data_pedido ? $pedido->data_pedido->format('Y-m-d') : '') }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
117:                                 @error('data_pedido') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
118:                             </div>
119: 
120:                             {{-- Data de Entrega --}}
121:                             <div>
122:                                 <label for="data_entrega" class="block font-medium text-sm text-gray-700">Previsão de Entrega</label>
123:                                 <input type="date" name="data_entrega" id="data_entrega" value="{{ old('data_entrega', $pedido->data_entrega ? $pedido->data_entrega->format('Y-m-d') : '') }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
124:                                 @error('data_entrega') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
125:                             </div>
126: 
127:                             {{-- Descrição --}}
128:                             <div class="md:col-span-2">
129:                                 <label for="descricao" class="block font-medium text-sm text-gray-700">Observações / Detalhes</label>
130:                                 <textarea name="descricao" id="descricao" rows="2" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('descricao', $pedido->descricao) }}</textarea>
131:                                 @error('descricao') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
132:                             </div>
133:                         </div>
134: 
135:                         <hr class="my-6 border-gray-200">
136: 
137:                         <!-- Itens do Pedido (Carrinho) -->
138:                         <div class="mb-6">
139:                             <h3 class="text-lg font-bold text-gray-800 mb-2">Itens do Pedido</h3>
140:                             
141:                             @error('itens') <p class="text-red-500 text-sm mb-2 font-semibold">Adicione pelo menos um item ao pedido.</p> @enderror
142: 
143:                             <!-- Adicionar Produto Rápido -->
144:                             <div class="flex gap-2 mb-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
145:                                 <div class="flex-1">
146:                                     <label class="block text-xs font-medium text-gray-700 mb-1">Puxar do Catálogo</label>
147:                                     <select id="select_catalogo" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
148:                                         <option value="">Selecione um produto/serviço para adicionar...</option>
149:                                         @foreach($produtosServicos as $produto)
150:                                             <option value="{{ $produto->id }}" data-preco="{{ $produto->preco_padrao }}" data-nome="{{ $produto->nome }}">
151:                                                 [{{ $produto->tipo }}] {{ $produto->nome }} - R$ {{ number_format($produto->preco_padrao, 2, ',', '.') }}
152:                                             </option>
153:                                         @endforeach
154:                                     </select>
155:                                 </div>
156:                                 <div class="flex items-end">
157:                                     <button type="button" onclick="adicionarDoCatalogo()" class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 text-sm font-semibold transition">
158:                                         + Adicionar Item
159:                                     </button>
160:                                 </div>
161:                                 <div class="flex items-end ml-4">
162:                                     <button type="button" onclick="adicionarItemVazio()" class="bg-white text-gray-700 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50 text-sm font-semibold transition">
163:                                         + Item Avulso
164:                                     </button>
165:                                 </div>
166:                             </div>
167: 
168:                             <div class="overflow-x-auto">
169:                                 <table class="min-w-full divide-y divide-gray-200 border">
170:                                     <thead class="bg-gray-50">
171:                                         <tr>
172:                                             <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/2">Item / Descrição</th>
173:                                             <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
174:                                             <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">V. Unitário (R$)</th>
175:                                             <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total (R$)</th>
176:                                             <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
177:                                         </tr>
178:                                     </thead>
179:                                     <tbody id="tabela-itens" class="bg-white divide-y divide-gray-200">
180:                                         <!-- Itens injetados via JS -->
181:                                     </tbody>
182:                                     <tfoot>
183:                                         <tr class="bg-gray-50">
184:                                             <td colspan="3" class="px-4 py-4 text-right font-bold text-gray-700">Total do Pedido:</td>
185:                                             <td class="px-4 py-4 text-right font-bold text-green-600 text-lg" id="valor-total-geral">R$ {{ number_format($pedido->valor, 2, ',', '.') }}</td>
186:                                             <td></td>
187:                                         </tr>
188:                                     </tfoot>
189:                                 </table>
190:                             </div>
191:                         </div>
192: 
193:                         <div class="mt-6 flex items-center justify-end">
194:                             <a href="{{ route('customer.pedidos.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
195:                             <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
196:                                 Salvar Alterações
197:                             </button>
198:                         </div>
199:                     </form>
200: 
201:                 </div>
202:             </div>
203:         </div>
204:     </div>
205: 
206:     <script>
207:         let itemIndex = 0;
208: 
209:         function formatarMoeda(valor) {
210:             return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(valor);
211:         }
212: 
213:         function recalcularTotalItem(index) {
214:             let qtd = parseFloat(document.getElementById(`qtd_${index}`).value) || 0;
215:             let unitario = parseFloat(document.getElementById(`unitario_${index}`).value) || 0;
216:             let total = qtd * unitario;
217:             document.getElementById(`total_label_${index}`).innerText = formatarMoeda(total);
218:             recalcularTotalGeral();
219:         }
220: 
221:         function recalcularTotalGeral() {
222:             let trs = document.querySelectorAll('#tabela-itens tr');
223:             let soma = 0;
224:             trs.forEach(tr => {
225:                 let idx = tr.getAttribute('data-index');
226:                 let qtd = parseFloat(document.getElementById(`qtd_${idx}`).value) || 0;
227:                 let unitario = parseFloat(document.getElementById(`unitario_${idx}`).value) || 0;
228:                 soma += (qtd * unitario);
229:             });
230:             document.getElementById('valor-total-geral').innerText = formatarMoeda(soma);
231:         }
232: 
233:         function removerItem(index) {
234:             let tr = document.getElementById(`linha_${index}`);
235:             if(tr) {
236:                 tr.remove();
237:                 recalcularTotalGeral();
238:             }
239:         }
240: 
241:         function inserirLinhaHTML(produtoId, nome, preco, qtd = 1) {
242:             let tbody = document.getElementById('tabela-itens');
243:             let totalItem = preco * qtd;
244: 
245:             let tr = document.createElement('tr');
246:             tr.id = `linha_${itemIndex}`;
247:             tr.setAttribute('data-index', itemIndex);
248: 
249:             let html = `
250:                 <td class="px-4 py-2">
251:                     <input type="hidden" name="itens[${itemIndex}][produto_servico_id]" value="${produtoId}">
252:                     <input type="text" name="itens[${itemIndex}][nome_item]" value="${nome}" required class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm sm:text-sm">
253:                 </td>
254:                 <td class="px-4 py-2 w-24">
255:                     <input type="number" step="0.01" min="0.01" name="itens[${itemIndex}][quantidade]" id="qtd_${itemIndex}" value="${qtd}" oninput="recalcularTotalItem(${itemIndex})" required class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm sm:text-sm">
256:                 </td>
257:                 <td class="px-4 py-2 w-32">
258:                     <input type="number" step="0.01" min="0" name="itens[${itemIndex}][valor_unitario]" id="unitario_${itemIndex}" value="${preco.toFixed(2)}" oninput="recalcularTotalItem(${itemIndex})" required class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm sm:text-sm">
259:                 </td>
260:                 <td class="px-4 py-2 text-right font-medium text-gray-700" id="total_label_${itemIndex}">
261:                     ${formatarMoeda(totalItem)}
262:                 </td>
263:                 <td class="px-4 py-2 text-right">
264:                     <button type="button" onclick="removerItem(${itemIndex})" class="text-red-500 hover:text-red-700 p-1">
265:                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
266:                     </button>
267:                 </td>
268:             `;
269: 
270:             tr.innerHTML = html;
271:             tbody.appendChild(tr);
272: 
273:             itemIndex++;
274:         }
275: 
276:         function adicionarDoCatalogo() {
277:             let select = document.getElementById('select_catalogo');
278:             let option = select.options[select.selectedIndex];
279:             
280:             if(!option.value) {
281:                 alert('Selecione um produto do catálogo primeiro.');
282:                 return;
283:             }
284: 
285:             let produtoId = option.value;
286:             let nome = option.getAttribute('data-nome');
287:             let preco = parseFloat(option.getAttribute('data-preco')) || 0;
288: 
289:             inserirLinhaHTML(produtoId, nome, preco, 1);
290:             recalcularTotalGeral();
291:             select.value = ''; // reseta
292:         }
293: 
294:         function adicionarItemVazio() {
295:             inserirLinhaHTML('', '', 0, 1);
296:             recalcularTotalGeral();
297:         }
298: 
299:         // Preencher itens existentes
300:         @if(old('itens'))
301:             @foreach(old('itens') as $item)
302:                 inserirLinhaHTML('{{ $item['produto_servico_id'] ?? '' }}', '{{ addslashes($item['nome_item']) }}', {{ $item['valor_unitario'] }}, {{ $item['quantidade'] }});
303:             @endforeach
304:         @else
305:             @foreach($pedido->itens as $item)
306:                 inserirLinhaHTML('{{ $item->produto_servico_id ?? '' }}', '{{ addslashes($item->nome_item) }}', {{ $item->valor_unitario }}, {{ $item->quantidade }});
307:             @endforeach
308:         @endif
309:         
310:         // Atualizar total ao iniciar
311:         setTimeout(() => {
312:             recalcularTotalGeral();
313:         }, 100);
314:     </script>
315: </x-app-layout>
