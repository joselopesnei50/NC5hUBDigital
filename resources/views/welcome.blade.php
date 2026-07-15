@extends('layouts.public')

@section('content')
    <!-- Hero Section -->
    <section class="hero">
        <!-- Animação de Fundo -->
        <div class="hero-animation-bg">
            <div class="flow-orb orb-1"></div>
            <div class="flow-orb orb-2"></div>
            <div class="orbit-axis">
                <div class="orbit-circle-1"></div>
                <div class="orbit-circle-2"></div>
            </div>
        </div>

        <div class="container">
            <div style="margin-bottom: 2rem;">
                <!-- Símbolo Isolado com animação sutil -->
                <img src="{{ asset('assets/images/nc5-final-simbolo-claro.svg') }}" alt="Símbolo NC5" style="width: 80px; height: auto;">
            </div>
        <h1 style="overflow: hidden;">
            <span class="reveal-text" style="--delay: 0.1s">Automação inteligente e</span><br>
            <span class="reveal-text" style="--delay: 0.3s">gestão de marca</span> <span class="reveal-text" style="--delay: 0.4s">para empresas.</span>
        </h1>
        <p class="reveal-text" style="--delay: 0.6s">
            Da nutrição de leads à identidade visual de alta performance, a NC5 HUB DIGITAL acelera o seu crescimento com tecnologia e design estratégico.
        </p>
        <div class="hero-actions reveal-text" style="--delay: 0.8s">
            <a href="#servicos" class="btn btn-primary">Conhecer Soluções</a>
            <a href="#contato" class="btn btn-outline">Fale com Especialista</a>
        </div>
        </div>
    </section>

    <!-- Seção 1: Processo 01 -->
    <section id="processo-01" class="section-premium">
        <div class="container">
            <div class="grid-50-50">
                <div class="grid-content-left">
                    <span class="step-number">01</span>
                    <h2>Aceleração <br><span style="color: var(--nc5-tinta);">Imparável</span></h2>
                    <p style="margin-top: 1.5rem; color: var(--nc5-cinza); margin-bottom: 2.5rem; font-size: 1.1rem; line-height: 1.6;">Transforme sua operação em uma máquina de conversão. Desenhamos arquiteturas invisíveis que nutrem leads, eliminam atritos e vendem no piloto automático.</p>
                    <ul style="list-style: none; color: var(--nc5-tinta); font-weight: 500;">
                        <li style="margin-bottom: 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 1rem;">✓ Funis de conversão de alta previsibilidade</li>
                        <li style="margin-bottom: 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 1rem;">✓ Mapeamento e integração total de CRM</li>
                        <li style="margin-bottom: 1rem; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 1rem;">✓ Engenharia de recuperação de receita</li>
                    </ul>
                </div>
                <div>
                    <img src="/images/dashboard.png" alt="Visão Estratégica do Funil" style="width: 100%; border-radius: var(--radius-lg); box-shadow: 0 20px 40px rgba(0,0,0,0.08); transition: transform 0.5s ease;" class="hover-lift">
                </div>
            </div>
        </div>
    </section>

    <!-- Seção 2: Processo 02 -->
    <section id="processo-02" class="section-premium">
        <div class="container">
            <div class="grid-50-50" style="direction: rtl;">
                <div class="grid-content-left" style="direction: ltr;">
                    <span class="step-number">02</span>
                    <h2>Posicionamento <br><span style="color: var(--nc5-tinta);">Dominante</span></h2>
                    <p style="margin-top: 1.5rem; color: var(--nc5-cinza); margin-bottom: 2.5rem; font-size: 1.1rem; line-height: 1.6;">Não seja apenas mais uma opção; torne-se a escolha óbvia. Esculpimos a presença digital da sua marca com estética visceral e engenharia de tráfego.</p>
                    <ul style="list-style: none; color: var(--nc5-tinta); font-weight: 500;">
                        <li style="margin-bottom: 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 1rem;">✓ Direção de Arte e Branding Premium</li>
                        <li style="margin-bottom: 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 1rem;">✓ Campanhas de tração e performance extrema</li>
                        <li style="margin-bottom: 1rem; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 1rem;">✓ Posicionamento magnético em redes sociais</li>
                    </ul>
                </div>
                <div style="direction: ltr;">
                    <img src="/images/branding.png" alt="Experiência de Marca Integrada" style="width: 100%; border-radius: var(--radius-lg); box-shadow: 0 20px 40px rgba(0,0,0,0.08); transition: transform 0.5s ease;" class="hover-lift">
                </div>
            </div>
        </div>
    </section>

    <!-- Seção 3: Processo 03 -->
    <section id="processo-03" class="section-premium dark">
        <div class="container">
            <div class="grid-50-50">
                <div class="grid-content-left">
                    <span class="step-number" style="color: rgba(255,255,255,0.1);">03</span>
                    <h2 style="color: var(--nc5-neutro);">Controle <br>Absoluto</h2>
                    <p style="margin-top: 1.5rem; color: rgba(255,255,255,0.7); margin-bottom: 3rem; font-size: 1.1rem; line-height: 1.6;">O seu negócio operando sob a sua ótica. Um portal centralizado com inteligência de dados, gestão contratual e suporte tático à distância de um clique.</p>
                    <a href="/area-cliente" class="btn btn-primary" style="background-color: var(--nc5-neutro); color: var(--nc5-tinta);">Explore o seu Portal</a>
                </div>
                <div>
                    <img src="/images/portal.png" alt="Visão do Portal Dinâmico" style="width: 100%; border-radius: var(--radius-lg); box-shadow: 0 20px 40px rgba(0,0,0,0.4); transition: transform 0.5s ease;" class="hover-lift">
                </div>
            </div>
        </div>
    </section>

    <!-- Seção: Números / Impacto -->
    <section class="section-premium light-gray">
        <div class="container text-center">
            <h2 style="font-size: 2.5rem; margin-bottom: 1.5rem;">Força motriz para empresas ambiciosas</h2>
            <p style="color: var(--nc5-cinza); font-size: 1.1rem; max-width: 600px; margin: 0 auto;">Substitua o esforço bruto por sistemas hiper-eficientes de alto impacto.</p>
            
            <div class="stats-grid" style="margin-top: 5rem;">
                <div class="stat-item">
                    <h4>100%</h4>
                    <p>Foco Estratégico</p>
                </div>
                <div class="stat-item">
                    <h4>2.5x</h4>
                    <p>Métrica de Escala</p>
                </div>
                <div class="stat-item">
                    <h4>12h</h4>
                    <p>Tempo Recuperado</p>
                </div>
                <div class="stat-item">
                    <h4>+50</h4>
                    <p>Sistemas Ativos</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção: Prova Social (Logos) -->
    <section class="section-premium" style="padding: 4rem 0;">
        <div class="container">
            <p style="text-align: center; color: var(--nc5-cinza); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 2rem;">Tecnologia de Ponta Integrada</p>
            <div class="logo-carousel">
                <div class="logo-track">
                    <!-- Primeiro Set -->
                    <div>ActiveCampaign</div>
                    <div>HubSpot</div>
                    <div>Meta Ads</div>
                    <div>Google AI</div>
                    <div>Pipedrive</div>
                    <!-- Segundo Set (para o loop infinito não quebrar) -->
                    <div>ActiveCampaign</div>
                    <div>HubSpot</div>
                    <div>Meta Ads</div>
                    <div>Google AI</div>
                    <div>Pipedrive</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção: Formulário / Captura -->
    <section id="contato" class="section-premium">
        <div class="container">
            <div class="grid-50-50">
                <div class="grid-content-left">
                    <h2 style="font-size: 2.8rem; margin-bottom: 2rem; line-height: 1.1;">O próximo nível exige ação rápida.</h2>
                    <div style="display: flex; margin-bottom: 2.5rem; align-items: flex-start;">
                        <div style="background: var(--nc5-tinta); color: #fff; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; margin-right: 1.5rem; flex-shrink: 0; font-weight: 600; font-size: 1.2rem;">1</div>
                        <div>
                            <h4 style="margin-bottom: 0.5rem; font-size: 1.2rem;">Faça o manifesto</h4>
                            <p style="color: var(--nc5-cinza); font-size: 1rem; line-height: 1.6;">Nos envie os dados da sua empresa. Sem burocracia, apenas o essencial para iniciarmos a análise.</p>
                        </div>
                    </div>
                    <div style="display: flex; align-items: flex-start;">
                        <div style="background: var(--nc5-tinta); color: #fff; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; margin-right: 1.5rem; flex-shrink: 0; font-weight: 600; font-size: 1.2rem;">2</div>
                        <div>
                            <h4 style="margin-bottom: 0.5rem; font-size: 1.2rem;">Reunião Tática</h4>
                            <p style="color: var(--nc5-cinza); font-size: 1rem; line-height: 1.6;">Desenharemos um mapa claro de execução. Entenderemos seu cenário e proporemos um plano de ataque agressivo.</p>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="capture-form" style="background: #FAFAFA; padding: 3rem; border-radius: var(--radius-lg); border: 1px solid rgba(10, 17, 40, 0.05); margin: 0 auto;">
                        <h3 style="margin-bottom: 2rem; font-size: 1.5rem;">Inicie seu Movimento</h3>
                        <form action="#" method="POST">
                            <div class="form-group">
                                <label>Qual é o seu nome?</label>
                                <input type="text" placeholder="Seu nome completo" required style="background: #fff;">
                            </div>
                            <div class="form-group">
                                <label>Nome da sua empresa</label>
                                <input type="text" placeholder="Razão social ou Fantasia" required style="background: #fff;">
                            </div>
                            <div class="form-group">
                                <label>E-mail estratégico</label>
                                <input type="email" placeholder="seuemail@empresa.com" required style="background: #fff;">
                            </div>
                            <div class="form-group">
                                <label>Linha direta (WhatsApp)</label>
                                <input type="text" placeholder="(00) 00000-0000" required style="background: #fff;">
                            </div>
                            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1.2rem; margin-top: 1.5rem; font-size: 1.1rem;">Solicitar Análise</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="section-pad" style="background: #FFFFFF;">
        <div class="container" style="max-width: 800px;">
            <h2 style="text-align: center; margin-bottom: 3rem;">Perguntas Frequentes</h2>
            
            <div class="faq-item" x-data="{ open: false }">
                <div class="faq-question" @click="open = !open">
                    A NC5 atende quais tipos de empresas?
                    <span style="font-size: 1.5rem;" x-text="open ? '−' : '+'">+</span>
                </div>
                <div class="faq-answer" x-show="open" x-collapse style="display: none;">Focamos em pequenas e médias empresas que buscam estruturação e previsibilidade, seja através de automações de marketing ou consolidação de sua identidade visual.</div>
            </div>

            <div class="faq-item" x-data="{ open: false }">
                <div class="faq-question" @click="open = !open">
                    Como funciona a Área do Cliente?
                    <span style="font-size: 1.5rem;" x-text="open ? '−' : '+'">+</span>
                </div>
                <div class="faq-answer" x-show="open" x-collapse style="display: none;">Todos os clientes da NC5 possuem acesso a um portal exclusivo (acessível pelo botão no menu superior). Nele, você pode acompanhar chamados de suporte, faturas e links dos seus relatórios de forma centralizada e transparente.</div>
            </div>

            <div class="faq-item" x-data="{ open: false }">
                <div class="faq-question" @click="open = !open">
                    Vocês gerenciam verba de anúncios?
                    <span style="font-size: 1.5rem;" x-text="open ? '−' : '+'">+</span>
                </div>
                <div class="faq-answer" x-show="open" x-collapse style="display: none;">Sim! Nas frentes de desenvolvimento de marca e tráfego pago, nossa equipe planeja, cria os anúncios (criativos) e faz a gestão do orçamento nas plataformas Meta Ads, Google Ads, entre outras.</div>
            </div>
        </div>
    </section>
@endsection
