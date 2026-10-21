@extends('layouts.public')

@section('title', 'Política de Cookies · NC5 Hub')

@section('content')
<main class="min-h-screen bg-[#050505] text-white py-20 lg:py-28">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12 pb-8 border-b border-white/10">
            <p class="text-xs font-extrabold uppercase tracking-widest text-[#FF7A1A] mb-3">Documento Legal</p>
            <h1 class="font-display font-black text-4xl md:text-5xl text-white leading-tight mb-4">Política de Cookies</h1>
            <p class="text-sm text-white/50">Última atualização: 18/09/2026</p>
        </div>

        <div class="bg-[#0B0F19] border border-white/10 rounded-3xl p-8 sm:p-12 space-y-8 leading-relaxed text-white/75 text-sm sm:text-base">

            <div>
                <h2 class="font-display font-bold text-xl sm:text-2xl text-white mb-3">1. O que são cookies</h2>
                <p>Cookies são pequenos arquivos de texto armazenados no seu navegador quando você visita um site. Eles ajudam a lembrar informações sobre sua visita, como preferências e sessão de acesso ao BruceOS.</p>
            </div>

            <div>
                <h2 class="font-display font-bold text-xl sm:text-2xl text-white mb-3">2. Cookies que usamos</h2>

                <h3 class="font-bold text-lg text-white mt-5 mb-2">Necessários (sempre ativos)</h3>
                <ul class="list-disc list-inside space-y-2 text-white/70">
                    <li><strong class="text-white">Sessão</strong> (<code class="bg-white/10 text-white px-2 py-0.5 rounded text-xs">nc5_hub_digital_session</code>): mantém o usuário autenticado no painel. Expira ao fim da sessão ou em até 2 horas de inatividade.</li>
                    <li><strong class="text-white">XSRF-TOKEN</strong>: proteção contra ataques CSRF em formulários.</li>
                </ul>

                <h3 class="font-bold text-lg text-white mt-5 mb-2">Analíticos (opcional)</h3>
                <ul class="list-disc list-inside space-y-2 text-white/70">
                    <li>Podemos usar ferramentas de análise de tráfego (ex.: Google Analytics) para entender o uso agregado do portal. Dados são coletados de forma anonimizada.</li>
                </ul>

                <h3 class="font-bold text-lg text-white mt-5 mb-2">Marketing (opcional)</h3>
                <ul class="list-disc list-inside space-y-2 text-white/70">
                    <li>Podemos usar pixels de conversão (ex.: Meta Pixel) em campanhas ativas. Nenhum dado sensível é compartilhado.</li>
                </ul>
            </div>

            <div>
                <h2 class="font-display font-bold text-xl sm:text-2xl text-white mb-3">3. Como controlar cookies</h2>
                <p class="mb-3">Você pode aceitar ou recusar cookies opcionais no banner exibido na primeira visita. Cookies necessários não podem ser desativados sem inviabilizar o funcionamento do painel.</p>
                <p>Você também pode limpar cookies a qualquer momento pelas configurações do seu navegador. Instruções: <a href="https://support.google.com/chrome/answer/95647" target="_blank" rel="noopener" class="text-[#FF7A1A] font-bold hover:underline">Chrome</a>, <a href="https://support.mozilla.org/pt-BR/kb/limpe-cookies-e-dados-de-sites-no-firefox" target="_blank" rel="noopener" class="text-[#FF7A1A] font-bold hover:underline">Firefox</a>, <a href="https://support.apple.com/pt-br/guide/safari/sfri11471/mac" target="_blank" rel="noopener" class="text-[#FF7A1A] font-bold hover:underline">Safari</a>.</p>
            </div>

            <div>
                <h2 class="font-display font-bold text-xl sm:text-2xl text-white mb-3">4. Dúvidas</h2>
                <p>Fale conosco em <a href="mailto:contato@nc5hubdigital.com.br" class="text-[#FF7A1A] font-bold hover:underline">contato@nc5hubdigital.com.br</a>. Consulte também nossa <a href="{{ route('privacidade') }}" class="text-[#FF7A1A] font-bold hover:underline">Política de Privacidade</a>.</p>
            </div>

        </div>
    </div>
</main>
@endsection
