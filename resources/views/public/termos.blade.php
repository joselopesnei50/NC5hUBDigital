@extends('layouts.public')

@section('title', 'Termos de Uso — NC5 Hub Digital')

@section('content')
<section class="bg-white py-16 lg:py-24">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-xs font-extrabold uppercase tracking-widest text-[#FF7A1A] mb-3">Documento legal</p>
        <h1 class="font-display font-extrabold text-4xl md:text-5xl text-[#0A1128] leading-tight mb-4">Termos de Uso</h1>
        <p class="text-sm text-slate-500 mb-12">Última atualização: 18/09/2026</p>

        <div class="max-w-none space-y-8 text-slate-700 leading-relaxed">

            <div>
                <h2 class="font-display font-bold text-2xl text-[#0A1128] mb-3">1. Aceite</h2>
                <p>Ao acessar ou utilizar o NC5 Hub Digital ("plataforma"), você concorda com estes Termos de Uso e com a nossa <a href="{{ route('privacidade') }}" class="text-[#FF7A1A] font-bold">Política de Privacidade</a>. Se não concordar, não utilize o serviço.</p>
            </div>

            <div>
                <h2 class="font-display font-bold text-2xl text-[#0A1128] mb-3">2. O que a plataforma faz</h2>
                <p>O NC5 Hub Digital é um portal privado para clientes contratantes de serviços de marketing digital e tecnologia da NC5. Oferece área de trabalho para gestão de contratos, faturas, materiais em aprovação, briefings, projetos, documentos e integrações opcionais (por exemplo, Google Meu Negócio).</p>
            </div>

            <div>
                <h2 class="font-display font-bold text-2xl text-[#0A1128] mb-3">3. Conta e credenciais</h2>
                <ul class="list-disc list-inside space-y-2">
                    <li>O cliente é responsável por manter suas credenciais de acesso em sigilo.</li>
                    <li>Cada painel de cliente admite até 5 usuários. A gestão desses usuários é responsabilidade do titular do cadastro.</li>
                    <li>Notifique-nos imediatamente em caso de uso não autorizado da conta.</li>
                </ul>
            </div>

            <div>
                <h2 class="font-display font-bold text-2xl text-[#0A1128] mb-3">4. Uso permitido</h2>
                <p>O cliente concorda em não:</p>
                <ul class="list-disc list-inside space-y-2">
                    <li>Utilizar a plataforma para atividades ilícitas ou que violem direitos de terceiros.</li>
                    <li>Realizar engenharia reversa, tentativas de acesso não autorizado ou ataques à infraestrutura.</li>
                    <li>Enviar conteúdo com malware, discurso de ódio ou material protegido por direitos autorais alheios sem autorização.</li>
                </ul>
            </div>

            <div>
                <h2 class="font-display font-bold text-2xl text-[#0A1128] mb-3">5. Integrações de terceiros</h2>
                <p>Integrações opcionais (como Google Meu Negócio) são ativadas pelo cliente e regidas também pelos termos dos respectivos provedores. Ao conectar contas de terceiros, o cliente autoriza a plataforma a executar as ações descritas na <a href="{{ route('privacidade') }}" class="text-[#FF7A1A] font-bold">Política de Privacidade</a>.</p>
            </div>

            <div>
                <h2 class="font-display font-bold text-2xl text-[#0A1128] mb-3">6. Disponibilidade e limitação de responsabilidade</h2>
                <p>Nos esforçamos pra manter alta disponibilidade, mas não garantimos serviço ininterrupto. Não somos responsáveis por perdas indiretas decorrentes de falhas de terceiros (provedores de API, serviços de hospedagem, operadoras de internet).</p>
            </div>

            <div>
                <h2 class="font-display font-bold text-2xl text-[#0A1128] mb-3">7. Encerramento</h2>
                <p>O cliente pode encerrar sua conta a qualquer momento contatando <a href="mailto:contato@nc5hubdigital.com.br" class="text-[#FF7A1A] font-bold">contato@nc5hubdigital.com.br</a>. Podemos suspender ou encerrar contas em caso de violação destes termos.</p>
            </div>

            <div>
                <h2 class="font-display font-bold text-2xl text-[#0A1128] mb-3">8. Foro</h2>
                <p>Estes termos são regidos pelas leis do Brasil. Fica eleito o foro da comarca da sede da NC5, com renúncia a qualquer outro por mais privilegiado que seja.</p>
            </div>

        </div>
    </div>
</section>
@endsection
