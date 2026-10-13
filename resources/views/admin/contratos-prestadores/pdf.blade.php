<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Contrato Prestador #{{ $contrato->id }}</title>
<style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10.5px; color: #1a1a2e; line-height: 1.6; margin: 0; padding: 0; }
    .header { background-color: #0A1128; color: #fff; padding: 24px 36px; }
    .header-label { font-size: 9px; font-weight: bold; letter-spacing: 3px; text-transform: uppercase; color: #FF7A1A; margin-bottom: 6px; }
    .header-title { font-size: 20px; font-weight: bold; color: #fff; }
    .header-sub { font-size: 11px; color: #cbd5e1; margin-top: 6px; }
    .body { padding: 28px 36px; }
    .section-title { font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; color: #0A1128; margin-top: 20px; margin-bottom: 10px; border-bottom: 1.5px solid #FF7A1A; padding-bottom: 4px; }
    .parties { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    .parties td { padding: 8px; vertical-align: top; width: 50%; background: #fafafa; border: 1px solid #e5e7eb; border-radius: 4px; }
    .party-label { font-size: 8px; font-weight: bold; text-transform: uppercase; color: #8A8F9C; letter-spacing: 1px; margin-bottom: 4px; }
    .party-name { font-size: 11px; font-weight: bold; color: #0A1128; margin-bottom: 2px; }
    .party-info { font-size: 10px; color: #475569; }
    .info-grid { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    .info-grid td { padding: 4px 0; vertical-align: top; width: 33%; }
    .info-label { font-size: 8px; font-weight: bold; text-transform: uppercase; color: #8A8F9C; letter-spacing: 1px; }
    .info-value { font-size: 11px; font-weight: bold; color: #0A1128; }
    .clausula { margin-bottom: 10px; text-align: justify; }
    .clausula strong { color: #0A1128; }
    .cl-title { font-weight: bold; color: #0A1128; display: block; margin-bottom: 2px; }
    .sig-grid { width: 100%; border-collapse: collapse; margin-top: 40px; }
    .sig-grid td { width: 50%; padding: 0 20px; vertical-align: top; }
    .sig-line { border-top: 1px solid #0A1128; padding-top: 6px; font-size: 9px; color: #475569; text-align: center; }
    .sig-badge { display: inline-block; background: #dcfce7; color: #166534; font-size: 9px; font-weight: bold; padding: 3px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: 1px; }
    .pending-badge { display: inline-block; background: #fff7ed; color: #9a3412; font-size: 9px; font-weight: bold; padding: 3px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: 1px; }
    .footer { margin-top: 32px; padding-top: 12px; border-top: 1px solid #e5e7eb; font-size: 8.5px; color: #8A8F9C; text-align: center; }
    .extras-box { border: 1px solid #e5e7eb; border-radius: 4px; padding: 12px 16px; background: #fafafa; white-space: pre-line; }
</style>
</head>
<body>

<div class="header">
    <div class="header-label">NC5 Hub Digital · Contrato de Prestação de Serviços</div>
    <div class="header-title">{{ $contrato->titulo }}</div>
    <div class="header-sub">Contrato nº {{ str_pad($contrato->id, 5, '0', STR_PAD_LEFT) }} · Emitido em {{ $contrato->created_at->format('d/m/Y') }}</div>
</div>

<div class="body">

    @php $p = $contrato->prestador; @endphp

    <div class="section-title">Partes</div>
    <table class="parties">
        <tr>
            <td>
                <div class="party-label">Contratante</div>
                <div class="party-name">NC5 Hub Digital</div>
                <div class="party-info">
                    CNPJ: {{ config('services.nc5.cnpj', '—') }}<br>
                    Endereço: {{ config('services.nc5.endereco', '—') }}<br>
                    nc5hubdigital.com.br
                </div>
            </td>
            <td>
                <div class="party-label">Contratado(a) — Prestador(a) de Serviços</div>
                <div class="party-name">{{ $p->nome ?? '—' }}</div>
                <div class="party-info">
                    {{ $p->tipo_pessoa === 'PJ' ? 'CNPJ' : 'CPF' }}: {{ $p->cpf_cnpj ?? '—' }}<br>
                    @if($p->rg_ie) {{ $p->tipo_pessoa === 'PJ' ? 'IE' : 'RG' }}: {{ $p->rg_ie }}<br> @endif
                    @if($p->endereco) Endereço: {{ $p->endereco }}{{ $p->cidade ? ', ' . $p->cidade : '' }}{{ $p->uf ? '/' . $p->uf : '' }}{{ $p->cep ? ' — CEP ' . $p->cep : '' }}<br> @endif
                    @if($p->email) E-mail: {{ $p->email }}<br> @endif
                    @if($p->telefone) Telefone: {{ $p->telefone }} @endif
                </div>
            </td>
        </tr>
    </table>

    <p style="font-size:10px;color:#475569;margin:14px 0 4px 0;text-align:justify;">
        As partes acima qualificadas, doravante denominadas <strong>CONTRATANTE</strong> e <strong>CONTRATADO(A)</strong>,
        têm entre si justo e contratado o presente <strong>Contrato de Prestação de Serviços</strong>, que se regerá pelas cláusulas e condições a seguir.
    </p>

    <div class="section-title">Cláusula 1 — Objeto</div>
    <p class="clausula">
        O(a) <strong>CONTRATADO(A)</strong> prestará à <strong>CONTRATANTE</strong>, de forma autônoma e sem subordinação,
        os seguintes serviços: {{ $contrato->objeto }}
    </p>

    <div class="section-title">Cláusula 2 — Prazo e Vigência</div>
    <p class="clausula">
        O presente contrato terá início em <strong>{{ $contrato->data_inicio?->format('d/m/Y') }}</strong>
        @if($contrato->data_fim)
            e término em <strong>{{ $contrato->data_fim->format('d/m/Y') }}</strong>, podendo ser prorrogado mediante acordo escrito entre as partes.
        @else
            e vigorará por prazo indeterminado, podendo ser rescindido por qualquer das partes mediante aviso prévio por escrito de 30 (trinta) dias.
        @endif
        @if($contrato->prazo_entrega)
            <br>Prazo específico de entrega: <strong>{{ $contrato->prazo_entrega }}</strong>.
        @endif
    </p>

    <div class="section-title">Cláusula 3 — Remuneração e Forma de Pagamento</div>
    <p class="clausula">
        Pela prestação dos serviços, a <strong>CONTRATANTE</strong> pagará ao(à) <strong>CONTRATADO(A)</strong> o valor de
        <strong>R$ {{ number_format($contrato->valor, 2, ',', '.') }}</strong> ({{ ucfirst($contrato->forma_pagamento_label) }}).
        @if($contrato->periodicidade)
            Pagamento conforme periodicidade: <strong>{{ $contrato->periodicidade }}</strong>.
        @endif
        <br>
        O pagamento será efetuado mediante apresentação de nota fiscal / recibo correspondente, por meio dos dados bancários informados pelo(a) <strong>CONTRATADO(A)</strong>:
        @if($p->chave_pix) PIX <strong>{{ $p->chave_pix }}</strong>. @endif
        @if($p->banco) Banco {{ $p->banco }}, Agência {{ $p->agencia ?: '—' }}, Conta {{ $p->conta ?: '—' }}. @endif
    </p>

    @php $num = 4; @endphp

    @if($contrato->clausula_nao_vinculo_empregaticio)
        <div class="section-title">Cláusula {{ $num++ }} — Natureza Civil / Ausência de Vínculo Empregatício</div>
        <p class="clausula">
            O(a) <strong>CONTRATADO(A)</strong> presta os serviços de forma autônoma, sem pessoalidade obrigatória, subordinação ou habitualidade típicas da relação empregatícia.
            Não há geração de vínculo trabalhista entre as partes, nos termos do art. 442-B da CLT, respondendo o(a) <strong>CONTRATADO(A)</strong> por seus próprios encargos tributários, previdenciários e trabalhistas.
        </p>
    @endif

    @if($contrato->clausula_confidencialidade)
        <div class="section-title">Cláusula {{ $num++ }} — Confidencialidade (NDA)</div>
        <p class="clausula">
            O(a) <strong>CONTRATADO(A)</strong> compromete-se a manter sigilo absoluto sobre todas as informações, dados, estratégias, materiais e identidade de clientes da <strong>CONTRATANTE</strong> aos quais tiver acesso em razão deste contrato,
            inclusive após o seu término, pelo prazo de 5 (cinco) anos. O descumprimento desta cláusula sujeitará o(a) <strong>CONTRATADO(A)</strong> à multa correspondente ao valor total do contrato,
            sem prejuízo das demais sanções civis, criminais e das obrigações impostas pela Lei nº 13.709/2018 (LGPD).
        </p>
    @endif

    @if($contrato->clausula_propriedade_intelectual)
        <div class="section-title">Cláusula {{ $num++ }} — Propriedade Intelectual</div>
        <p class="clausula">
            Todos os direitos patrimoniais sobre materiais, obras, códigos, peças e entregáveis produzidos pelo(a) <strong>CONTRATADO(A)</strong> em decorrência deste contrato serão transferidos integralmente,
            de forma total, definitiva e irrevogável, à <strong>CONTRATANTE</strong>, mediante o pagamento previsto na Cláusula 3, nos termos dos arts. 49 e seguintes da Lei nº 9.610/98,
            inclusive para fins de utilização comercial, modificação, distribuição e sublicenciamento a terceiros, por prazo indeterminado e em qualquer território.
        </p>
    @endif

    @if($contrato->clausula_exclusividade)
        <div class="section-title">Cláusula {{ $num++ }} — Exclusividade</div>
        <p class="clausula">
            Durante a vigência deste contrato, o(a) <strong>CONTRATADO(A)</strong> compromete-se a <strong>não prestar serviços similares a empresas concorrentes diretas</strong> da <strong>CONTRATANTE</strong> no mesmo nicho e território de atuação,
            sob pena de rescisão imediata e multa equivalente a 2 (duas) vezes o valor mensal do contrato.
        </p>
    @endif

    @if($contrato->clausulas_extras)
        <div class="section-title">Cláusula {{ $num++ }} — Disposições Adicionais</div>
        <div class="extras-box">{{ $contrato->clausulas_extras }}</div>
    @endif

    <div class="section-title">Cláusula {{ $num++ }} — Rescisão</div>
    <p class="clausula">
        O presente contrato poderá ser rescindido: <strong>(a)</strong> por mútuo acordo entre as partes;
        <strong>(b)</strong> por iniciativa de qualquer das partes, mediante comunicação escrita com antecedência mínima de 30 (trinta) dias;
        <strong>(c)</strong> por descumprimento de qualquer das cláusulas aqui previstas, independentemente de aviso prévio;
        <strong>(d)</strong> por caso fortuito ou força maior devidamente comprovado.
    </p>

    <div class="section-title">Cláusula {{ $num++ }} — Foro</div>
    <p class="clausula">
        Fica eleito o foro da comarca da sede da <strong>CONTRATANTE</strong> para dirimir quaisquer dúvidas ou controvérsias oriundas do presente contrato,
        com renúncia expressa a qualquer outro, por mais privilegiado que seja.
    </p>

    @if($contrato->conteudo)
        <div class="section-title">Observações / Termos Livres</div>
        <div class="extras-box">{!! $contrato->conteudo !!}</div>
    @endif

    <p style="font-size:10px;color:#475569;margin:20px 0 10px 0;text-align:justify;">
        E, por estarem assim justos e contratados, as partes firmam o presente instrumento em formato eletrônico, com força de título executivo extrajudicial, nos termos do art. 784 do Código de Processo Civil.
    </p>

    <div class="sig-section">
        @if($contrato->status_assinatura === 'assinado' && $contrato->assinado_em)
            <div class="sig-badge">✓ Assinado eletronicamente em {{ $contrato->assinado_em->format('d/m/Y H:i') }}</div>
        @else
            <div class="pending-badge">Aguardando assinatura</div>
        @endif
    </div>

    <table class="sig-grid">
        <tr>
            <td>
                <div style="height:40px;"></div>
                <div class="sig-line">
                    <strong>NC5 Hub Digital</strong><br>
                    CONTRATANTE
                </div>
            </td>
            <td>
                <div style="height:40px;"></div>
                <div class="sig-line">
                    <strong>{{ $p->nome ?? '—' }}</strong><br>
                    CONTRATADO(A)
                </div>
            </td>
        </tr>
    </table>

</div>

<div class="footer">
    NC5 Hub Digital · Gerado em {{ now()->format('d/m/Y H:i') }} · nc5hubdigital.com.br
</div>

</body>
</html>
