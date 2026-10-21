@extends('layouts.public')

@section('title', 'Termos de Uso · NC5 Hub')

@section('content')
<main class="min-h-screen bg-[#050505] text-white py-20 lg:py-28">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12 pb-8 border-b border-white/10">
            <p class="text-xs font-extrabold uppercase tracking-widest text-[#FF7A1A] mb-3">Documento Legal</p>
            <h1 class="font-display font-black text-4xl md:text-5xl text-white leading-tight mb-4">Termos de Uso</h1>
            <p class="text-sm text-white/50">Última atualização: 18/09/2026</p>
        </div>

        <div class="bg-[#0B0F19] border border-white/10 rounded-3xl p-8 sm:p-12 space-y-8 leading-relaxed text-white/75 text-sm sm:text-base">

            <div>
                <h2 class="font-display font-bold text-xl sm:text-2xl text-white mb-3">1. Aceite</h2>
                <p>Ao acessar ou utilizar o <strong>NC5 Hub Digital</strong> e o painel <strong>BruceOS</strong> ("plataforma"), você concorda com estes Termos de Uso e com a nossa <a href="{{ route('privacidade') }}" class="text-[#FF7A1A] font-bold hover:underline">Política de Privacidade</a>. Se não concordar, não utilize o serviço.</p>
            </div>

            <div>
                <h2 class="font-display font-bold text-xl sm:text-2xl text-white mb-3">2. O que a plataforma faz</h2>
                <p>O BruceOS é o sistema operacional privado para clientes contratantes de serviços de estratégia, marketing e tecnologia da NC5. Oferece ambiente unificado para gestão de contratos, faturas, materiais em aprovação, briefings, base de clientes, pedidos, agenda, WhatsApp e integrações (como Google Meu Negócio).</p>
            </div>

            <div>
                <h2 class="font-display font-bold text-xl sm:text-2xl text-white mb-3">3. Conta e credenciais</h2>
                <ul class="list-disc list-inside space-y-2 text-white/70">
                    <li>O cliente é responsável por manter suas credenciais de acesso em sigilo.</li>
                    <li>Cada conta corporativa no BruceOS admite até 5 usuários ativos. A gestão desses usuários é responsabilidade do titular do cadastro.</li>
                    <li>Notifique-nos imediatamente em caso de uso não autorizado da conta.</li>
                </ul>
            </div>

            <div>
                <h2 class="font-display font-bold text-xl sm:text-2xl text-white mb-3">4. Uso permitido</h2>
                <p class="mb-3">O cliente concorda em não:</p>
                <ul class="list-disc list-inside space-y-2 text-white/70">
                    <li>Utilizar a plataforma para atividades ilícitas, spam ou que violem direitos de terceiros.</li>
                    <li>Realizar engenharia reversa, tentativas de acesso não autorizado ou ataques à infraestrutura.</li>
                    <li>Enviar conteúdo com malware, discurso de ódio ou material protegido por direitos autorais alheios sem autorização.</li>
                </ul>
            </div>

            <div>
                <h2 class="font-display font-bold text-xl sm:text-2xl text-white mb-3">5. Integrações de terceiros</h2>
                <p>Integrações opcionais (como Google Meu Negócio e WhatsApp) são ativadas pelo cliente e regidas também pelos termos dos respectivos provedores. Ao conectar contas de terceiros, o cliente autoriza a plataforma a executar as ações descritas na <a href="{{ route('privacidade') }}" class="text-[#FF7A1A] font-bold hover:underline">Política de Privacidade</a>.</p>
            </div>

            <div>
                <h2 class="font-display font-bold text-xl sm:text-2xl text-white mb-3">6. Disponibilidade e limitação de responsabilidade</h2>
                <p>Nos esforçamos para manter alta disponibilidade, mas não garantimos serviço ininterrupto. Não somos responsáveis por perdas indiretas decorrentes de falhas de terceiros (provedores de API, serviços de hospedagem, operadoras de internet).</p>
            </div>

            <div>
                <h2 class="font-display font-bold text-xl sm:text-2xl text-white mb-3">7. Encerramento</h2>
                <p>O acesso ao BruceOS está vinculado à vigência do contrato ativo com a NC5. O cliente pode solicitar o encerramento contatando <a href="mailto:contato@nc5hubdigital.com.br" class="text-[#FF7A1A] font-bold hover:underline">contato@nc5hubdigital.com.br</a>. Podemos suspender contas em caso de violação destes termos.</p>
            </div>

            <div>
                <h2 class="font-display font-bold text-xl sm:text-2xl text-white mb-3">8. Foro</h2>
                <p>Estes termos são regidos pelas leis do Brasil. Fica eleito o foro da comarca da sede da NC5, com renúncia a qualquer outro por mais privilegiado que seja.</p>
            </div>

        </div>
    </div>
</main>
@endsection
