@extends('layouts.public')

@section('title', 'Política de Privacidade — NC5 Hub Digital')

@section('content')
<section class="bg-white py-16 lg:py-24">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-xs font-extrabold uppercase tracking-widest text-[#FF7A1A] mb-3">Documento legal</p>
        <h1 class="font-display font-extrabold text-4xl md:text-5xl text-[#0A1128] leading-tight mb-4">Política de Privacidade</h1>
        <p class="text-sm text-slate-500 mb-12">Última atualização: 18/09/2026</p>

        <div class="max-w-none space-y-8 text-slate-700 leading-relaxed">

            <div>
                <h2 class="font-display font-bold text-2xl text-[#0A1128] mb-3">1. Quem somos</h2>
                <p>Esta política se aplica ao serviço <strong>NC5 Hub Digital</strong> ("plataforma", "nós"), operado sob o domínio <strong>nc5hubdigital.com.br</strong>. Para dúvidas sobre esta política ou seus dados, escreva para <a href="mailto:contato@nc5hubdigital.com.br" class="text-[#FF7A1A] font-bold">contato@nc5hubdigital.com.br</a>.</p>
            </div>

            <div>
                <h2 class="font-display font-bold text-2xl text-[#0A1128] mb-3">2. Dados que coletamos</h2>
                <p>Coletamos apenas os dados estritamente necessários pra prestação do serviço:</p>
                <ul class="list-disc list-inside space-y-2">
                    <li><strong>Cadastro do cliente:</strong> nome, e-mail, telefone, CPF/CNPJ e razão social.</li>
                    <li><strong>Uso da plataforma:</strong> logs de acesso, endereço IP e ações realizadas no painel, pra fins de segurança e auditoria.</li>
                    <li><strong>Documentos e materiais enviados</strong> pelo cliente ao próprio cofre da plataforma.</li>
                    <li><strong>Dados de integrações opcionais</strong> quando autorizadas pelo cliente — detalhados na seção 4.</li>
                </ul>
            </div>

            <div>
                <h2 class="font-display font-bold text-2xl text-[#0A1128] mb-3">3. Como usamos seus dados</h2>
                <ul class="list-disc list-inside space-y-2">
                    <li>Prestar os serviços contratados (gestão de projetos, envio de materiais, faturamento, atendimento).</li>
                    <li>Comunicações operacionais (alertas do painel, faturas, respostas de suporte).</li>
                    <li>Cumprimento de obrigações legais e fiscais.</li>
                </ul>
                <p>Não vendemos dados a terceiros. Não usamos dados de clientes pra treinar modelos de IA de terceiros.</p>
            </div>

            <div>
                <h2 class="font-display font-bold text-2xl text-[#0A1128] mb-3">4. Integração com Google Business Profile</h2>
                <p>Quando o cliente conecta sua conta Google ao NC5 Hub Digital pela funcionalidade "Google Meu Negócio" do painel, solicitamos o escopo OAuth <code>https://www.googleapis.com/auth/business.manage</code>.</p>
                <p><strong>O que fazemos com esse acesso:</strong></p>
                <ul class="list-disc list-inside space-y-2">
                    <li>Listamos as localizações (perfis do Google Meu Negócio) associadas à conta autorizada, pra que o cliente escolha qual gerenciar dentro do painel.</li>
                    <li>Publicamos posts, respondemos avaliações e consultamos métricas de desempenho <strong>somente quando o cliente aciona explicitamente a ação no painel</strong>.</li>
                    <li>Armazenamos os tokens de acesso e de atualização (refresh token) <strong>criptografados em repouso</strong> (AES-256), vinculados exclusivamente ao cadastro daquele cliente.</li>
                </ul>
                <p><strong>O que NÃO fazemos:</strong></p>
                <ul class="list-disc list-inside space-y-2">
                    <li>Não compartilhamos dados do Google Meu Negócio do cliente com terceiros.</li>
                    <li>Não usamos os dados coletados via Google APIs pra publicidade, venda de dados ou treinamento de modelos de IA generativa.</li>
                    <li>Não acessamos dados de outros produtos Google (Gmail, Drive, Calendar etc.) — apenas o escopo de Business Profile.</li>
                </ul>
                <p><strong>Como revogar o acesso a qualquer momento:</strong></p>
                <ul class="list-disc list-inside space-y-2">
                    <li>Dentro do painel: <em>Área do Cliente → Google Meu Negócio → Desconectar conta</em>. Ao desconectar, apagamos os tokens do nosso banco imediatamente.</li>
                    <li>Diretamente no Google: <a href="https://myaccount.google.com/permissions" target="_blank" rel="noopener" class="text-[#FF7A1A] font-bold">https://myaccount.google.com/permissions</a>.</li>
                </ul>
                <p>O uso dos dados do Google Meu Negócio pela nossa aplicação está em conformidade com a <a href="https://developers.google.com/terms/api-services-user-data-policy" target="_blank" rel="noopener" class="text-[#FF7A1A] font-bold">Google API Services User Data Policy</a>, incluindo os requisitos de "Limited Use".</p>
            </div>

            <div>
                <h2 class="font-display font-bold text-2xl text-[#0A1128] mb-3">5. Compartilhamento com terceiros</h2>
                <p>Compartilhamos dados apenas quando estritamente necessário e sempre com prestadores que atuam como operadores (art. 5º, VII, LGPD):</p>
                <ul class="list-disc list-inside space-y-2">
                    <li><strong>Infraestrutura:</strong> AWS (hospedagem do banco de dados e arquivos).</li>
                    <li><strong>Envio de e-mails transacionais:</strong> Brevo/Mailtrap.</li>
                    <li><strong>Provedores de IA:</strong> DeepSeek, para geração de conteúdo de rascunhos internos — sem incluir PII do cliente.</li>
                </ul>
            </div>

            <div>
                <h2 class="font-display font-bold text-2xl text-[#0A1128] mb-3">6. Retenção de dados</h2>
                <p>Mantemos os dados de cadastro pelo período do contrato + 5 anos (obrigação fiscal). Logs de acesso ficam retidos por 6 meses. Ao final, os dados são anonimizados ou excluídos.</p>
            </div>

            <div>
                <h2 class="font-display font-bold text-2xl text-[#0A1128] mb-3">7. Seus direitos (LGPD)</h2>
                <p>Você pode a qualquer momento solicitar: confirmação de tratamento, acesso, correção, portabilidade, anonimização, eliminação e revogação do consentimento. Basta escrever para <a href="mailto:contato@nc5hubdigital.com.br" class="text-[#FF7A1A] font-bold">contato@nc5hubdigital.com.br</a> — respondemos em até 15 dias.</p>
            </div>

            <div>
                <h2 class="font-display font-bold text-2xl text-[#0A1128] mb-3">8. Segurança</h2>
                <p>Empregamos HTTPS/TLS em todo o tráfego, criptografia em repouso pra credenciais de integrações, autenticação com senha hasheada (bcrypt) e controle de acesso por papel (super admin, cliente).</p>
            </div>

            <div>
                <h2 class="font-display font-bold text-2xl text-[#0A1128] mb-3">9. Alterações nesta política</h2>
                <p>Podemos atualizar esta política. Quando materiais, avisaremos por e-mail e no painel. A data de "Última atualização" no topo indica a versão vigente.</p>
            </div>

        </div>
    </div>
</section>
@endsection
