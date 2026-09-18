@extends('layouts.public')

@section('title', 'Política de Cookies — NC5 Hub Digital')

@section('content')
<section class="bg-white py-16 lg:py-24">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-xs font-extrabold uppercase tracking-widest text-[#FF7A1A] mb-3">Documento legal</p>
        <h1 class="font-display font-extrabold text-4xl md:text-5xl text-[#0A1128] leading-tight mb-4">Política de Cookies</h1>
        <p class="text-sm text-slate-500 mb-12">Última atualização: 18/09/2026</p>

        <div class="max-w-none space-y-8 leading-relaxed" style="color: #334155;">

            <div>
                <h2 class="font-display font-bold text-2xl text-[#0A1128] mb-3">1. O que são cookies</h2>
                <p>Cookies são pequenos arquivos de texto armazenados no seu navegador quando você visita um site. Eles ajudam a lembrar informações sobre sua visita, como preferências e sessão de acesso.</p>
            </div>

            <div>
                <h2 class="font-display font-bold text-2xl text-[#0A1128] mb-3">2. Cookies que usamos</h2>

                <h3 class="font-bold text-lg text-[#0A1128] mt-4 mb-2">Necessários (sempre ativos)</h3>
                <ul class="list-disc list-inside space-y-2">
                    <li><strong>Sessão</strong> (<code>nc5_hub_digital_session</code>): mantém o usuário logado no painel. Expira ao fim da sessão ou em até 2 horas de inatividade.</li>
                    <li><strong>XSRF-TOKEN</strong>: proteção contra ataques CSRF em formulários.</li>
                </ul>

                <h3 class="font-bold text-lg text-[#0A1128] mt-4 mb-2">Analíticos (opcional)</h3>
                <ul class="list-disc list-inside space-y-2">
                    <li>Podemos usar ferramentas de análise de tráfego (ex.: Google Analytics) para entender uso agregado do site. Dados são coletados de forma anonimizada.</li>
                </ul>

                <h3 class="font-bold text-lg text-[#0A1128] mt-4 mb-2">Marketing (opcional)</h3>
                <ul class="list-disc list-inside space-y-2">
                    <li>Podemos usar pixels de conversão (ex.: Meta Pixel) em campanhas ativas. Nenhum dado sensível é compartilhado.</li>
                </ul>
            </div>

            <div>
                <h2 class="font-display font-bold text-2xl text-[#0A1128] mb-3">3. Como controlar cookies</h2>
                <p>Você pode aceitar ou recusar cookies opcionais no banner exibido na primeira visita. Cookies necessários não podem ser desativados sem inviabilizar o funcionamento do painel.</p>
                <p>Você também pode limpar cookies a qualquer momento pelas configurações do seu navegador. Instruções: <a href="https://support.google.com/chrome/answer/95647" target="_blank" rel="noopener" class="text-[#FF7A1A] font-bold">Chrome</a>, <a href="https://support.mozilla.org/pt-BR/kb/limpe-cookies-e-dados-de-sites-no-firefox" target="_blank" rel="noopener" class="text-[#FF7A1A] font-bold">Firefox</a>, <a href="https://support.apple.com/pt-br/guide/safari/sfri11471/mac" target="_blank" rel="noopener" class="text-[#FF7A1A] font-bold">Safari</a>.</p>
            </div>

            <div>
                <h2 class="font-display font-bold text-2xl text-[#0A1128] mb-3">4. Dúvidas</h2>
                <p>Fale conosco em <a href="mailto:contato@nc5hubdigital.com.br" class="text-[#FF7A1A] font-bold">contato@nc5hubdigital.com.br</a>. Consulte também nossa <a href="{{ route('privacidade') }}" class="text-[#FF7A1A] font-bold">Política de Privacidade</a>.</p>
            </div>

        </div>
    </div>
</section>
@endsection
