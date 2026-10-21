@extends('layouts.public')

@section('title', 'Política de Privacidade · NC5 Hub')

@section('content')
<main class="min-h-screen bg-[#050505] text-white py-20 lg:py-28">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12 pb-8 border-b border-white/10">
            <p class="text-xs font-extrabold uppercase tracking-widest text-[#FF7A1A] mb-3">Documento Legal</p>
            <h1 class="font-display font-black text-4xl md:text-5xl text-white leading-tight mb-4">Política de Privacidade</h1>
            <p class="text-sm text-white/50">Última atualização: 18/09/2026</p>
        </div>

        <div class="bg-[#0B0F19] border border-white/10 rounded-3xl p-8 sm:p-12 space-y-8 leading-relaxed text-white/75 text-sm sm:text-base">

            <div>
                <h2 class="font-display font-bold text-xl sm:text-2xl text-white mb-3">1. Quem somos</h2>
                <p>Esta política se aplica ao ecossistema <strong>NC5 Hub Digital</strong> e ao painel operacional <strong>BruceOS</strong> ("plataforma", "nós"), operados sob o domínio <strong>nc5hubdigital.com.br</strong>. Para dúvidas sobre esta política ou seus dados, escreva para <a href="mailto:contato@nc5hubdigital.com.br" class="text-[#FF7A1A] font-bold hover:underline">contato@nc5hubdigital.com.br</a>.</p>
            </div>

            <div>
                <h2 class="font-display font-bold text-xl sm:text-2xl text-white mb-3">2. Dados que coletamos</h2>
                <p class="mb-3">Coletamos apenas os dados estritamente necessários para a prestação do serviço:</p>
                <ul class="list-disc list-inside space-y-2 text-white/70">
                    <li><strong class="text-white">Cadastro do cliente:</strong> nome, e-mail, telefone, CPF/CNPJ e razão social.</li>
                    <li><strong class="text-white">Uso da plataforma:</strong> logs de acesso, endereço IP e ações realizadas no BruceOS, para fins de segurança e auditoria.</li>
                    <li><strong class="text-white">Documentos e materiais enviados</strong> pelo cliente ao cofre da plataforma.</li>
                    <li><strong class="text-white">Dados de integrações opcionais</strong> quando autorizadas pelo cliente — detalhados na seção 4.</li>
                </ul>
            </div>

            <div>
                <h2 class="font-display font-bold text-xl sm:text-2xl text-white mb-3">3. Como usamos seus dados</h2>
                <ul class="list-disc list-inside space-y-2 text-white/70 mb-3">
                    <li>Prestar os serviços contratados (gestão comercial, envio de materiais, faturamento e atendimento).</li>
                    <li>Comunicações operacionais (alertas do painel, faturas, respostas de suporte).</li>
                    <li>Cumprimento de obrigações legais e fiscais.</li>
                </ul>
                <p>Não vendemos dados a terceiros. Não usamos dados de clientes para treinar modelos públicos de IA de terceiros.</p>
            </div>

            <div>
                <h2 class="font-display font-bold text-xl sm:text-2xl text-white mb-3">4. Integração com Google Business Profile</h2>
                <p class="mb-3">Quando o cliente conecta sua conta Google ao BruceOS pela funcionalidade "Google Meu Negócio", solicitamos o escopo OAuth <code class="bg-white/10 text-white px-2 py-0.5 rounded text-xs">https://www.googleapis.com/auth/business.manage</code>.</p>
                <p class="font-bold text-white mb-2">O que fazemos com esse acesso:</p>
                <ul class="list-disc list-inside space-y-2 text-white/70 mb-4">
                    <li>Listamos as localizações (perfis do Google Meu Negócio) associadas à conta autorizada, para que o cliente escolha qual gerenciar dentro do painel.</li>
                    <li>Publicamos posts, respondemos avaliações e consultamos métricas de desempenho <strong class="text-white">somente quando o cliente aciona explicitamente a ação no painel</strong>.</li>
                    <li>Armazenamos os tokens de acesso e de atualização (refresh token) <strong class="text-white">criptografados em repouso</strong> (AES-256), vinculados exclusivamente ao cadastro daquele cliente.</li>
                </ul>
                <p class="font-bold text-white mb-2">O que NÃO fazemos:</p>
                <ul class="list-disc list-inside space-y-2 text-white/70 mb-4">
                    <li>Não compartilhamos dados do Google Meu Negócio do cliente com terceiros.</li>
                    <li>Não usamos os dados coletados via Google APIs para publicidade, venda de dados ou treinamento de modelos de IA generativa.</li>
                    <li>Não acessamos dados de outros produtos Google (Gmail, Drive, Calendar etc.) — apenas o escopo de Business Profile.</li>
                </ul>
                <p class="font-bold text-white mb-2">Como revogar o acesso a qualquer momento:</p>
                <ul class="list-disc list-inside space-y-2 text-white/70 mb-4">
                    <li>Dentro do painel: <em>BruceOS → Google Meu Negócio → Desconectar conta</em>. Ao desconectar, apagamos os tokens do nosso banco imediatamente.</li>
                    <li>Diretamente no Google: <a href="https://myaccount.google.com/permissions" target="_blank" rel="noopener" class="text-[#FF7A1A] font-bold hover:underline">https://myaccount.google.com/permissions</a>.</li>
                </ul>
                <p>O uso dos dados do Google Meu Negócio pela nossa aplicação está em conformidade com a <a href="https://developers.google.com/terms/api-services-user-data-policy" target="_blank" rel="noopener" class="text-[#FF7A1A] font-bold hover:underline">Google API Services User Data Policy</a>, incluindo os requisitos de "Limited Use".</p>
            </div>

            <div>
                <h2 class="font-display font-bold text-xl sm:text-2xl text-white mb-3">5. Compartilhamento com terceiros</h2>
                <p class="mb-3">Compartilhamos dados apenas quando estritamente necessário e sempre com prestadores que atuam como operadores (art. 5º, VII, LGPD):</p>
                <ul class="list-disc list-inside space-y-2 text-white/70">
                    <li><strong class="text-white">Infraestrutura:</strong> Hospedagem segura em nuvem do banco de dados e arquivos.</li>
                    <li><strong class="text-white">Envio de e-mails transacionais:</strong> Brevo.</li>
                    <li><strong class="text-white">Provedores de IA:</strong> DeepSeek, para geração de conteúdo de rascunhos internos e atendimento assistido.</li>
                </ul>
            </div>

            <div>
                <h2 class="font-display font-bold text-xl sm:text-2xl text-white mb-3">6. Retenção de dados</h2>
                <p>Mantemos os dados de cadastro pelo período do contrato + 5 anos (obrigação fiscal). Logs de acesso ficam retidos por 6 meses. Ao final, os dados são anonimizados ou excluídos.</p>
            </div>

            <div>
                <h2 class="font-display font-bold text-xl sm:text-2xl text-white mb-3">7. Seus direitos (LGPD)</h2>
                <p>Você pode a qualquer momento solicitar: confirmação de tratamento, acesso, correção, portabilidade, anonimização, eliminação e revogação do consentimento. Basta escrever para <a href="mailto:contato@nc5hubdigital.com.br" class="text-[#FF7A1A] font-bold hover:underline">contato@nc5hubdigital.com.br</a> — respondemos em até 15 dias.</p>
            </div>

            <div>
                <h2 class="font-display font-bold text-xl sm:text-2xl text-white mb-3">8. Segurança</h2>
                <p>Empregamos HTTPS/TLS em todo o tráfego, criptografia em repouso para credenciais de integrações, autenticação com senha hasheada (bcrypt) e controle de acesso por nível de permissão.</p>
            </div>

            <div>
                <h2 class="font-display font-bold text-xl sm:text-2xl text-white mb-3">9. Alterações nesta política</h2>
                <p>Podemos atualizar esta política. Quando houver alterações relevantes, avisaremos por e-mail e no painel. A data de "Última atualização" no topo indica a versão vigente.</p>
            </div>

        </div>
    </div>
</main>
@endsection
