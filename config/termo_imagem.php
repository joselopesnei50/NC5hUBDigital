<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Modelos padrão — Termo de Autorização de Uso de Imagem e Voz
    |--------------------------------------------------------------------------
    |
    | Placeholders disponíveis:
    |   {{cedente_nome}}, {{cedente_cpf}}, {{cedente_rg}}, {{cedente_endereco}},
    |   {{responsavel_nome}}, {{responsavel_cpf}}, {{responsavel_parentesco}},
    |   {{projeto}}, {{data_captacao}}, {{local}}, {{cliente}}
    |
    */

    'placeholders' => [
        '{{cedente_nome}}'           => 'Nome completo do cedente (ou do menor)',
        '{{cedente_cpf}}'            => 'CPF do cedente',
        '{{cedente_rg}}'             => 'RG do cedente',
        '{{cedente_endereco}}'       => 'Endereço do cedente',
        '{{responsavel_nome}}'       => 'Nome do responsável legal (quando menor)',
        '{{responsavel_cpf}}'        => 'CPF do responsável legal (quando menor)',
        '{{responsavel_parentesco}}' => 'Vínculo/parentesco do responsável',
        '{{projeto}}'                => 'Nome do projeto ou gravação de referência',
        '{{data_captacao}}'          => 'Data da captação',
        '{{local}}'                  => 'Local da captação',
        '{{cliente}}'                => 'Razão social do cliente vinculado (ou NC5 Hub Digital)',
    ],

    'template_adulto' => <<<'TEXT'
TERMO DE AUTORIZAÇÃO GRATUITA DE USO DE IMAGEM E VOZ

Pelo presente instrumento particular, eu, {{cedente_nome}}, inscrito(a) no CPF sob o nº {{cedente_cpf}}, portador(a) do RG nº {{cedente_rg}}, residente e domiciliado(a) em {{cedente_endereco}}, doravante denominado(a) simplesmente CEDENTE, AUTORIZO, de forma livre, informada, expressa e inteiramente gratuita, o uso de minha imagem, som de voz, nome e depoimento captados no contexto do projeto/gravação "{{projeto}}", realizada em {{data_captacao}}, no local {{local}}, em favor de NC5 Hub Digital e de {{cliente}}, doravante denominadas AUTORIZADAS.

1. FINALIDADE DO USO
A presente autorização abrange a utilização da imagem e da voz captadas para fins institucionais, publicitários, promocionais, comerciais, artísticos e informativos, podendo o material ser veiculado em redes sociais (Instagram, Facebook, TikTok, YouTube, LinkedIn e similares), websites, portfólios, apresentações, campanhas de tráfego pago, peças audiovisuais, materiais impressos e quaisquer outros meios de comunicação física ou digital existentes ou que venham a ser criados.

2. EDIÇÃO E TRATAMENTO DO MATERIAL
Ficam as AUTORIZADAS expressamente autorizadas a realizar cortes, edições, montagens, legendagens, tratamentos de cor e áudio, adaptações de formato e reduções de tempo necessários à adequação técnica e estética das peças, desde que preservado o contexto original e que não haja alteração pejorativa, difamatória ou que desvirtue o sentido da manifestação do(a) CEDENTE.

3. GRATUIDADE, TERRITÓRIO E PRAZO
A presente autorização é concedida a título gratuito, sem que nada haja a ser reclamado a título de direitos conexos à imagem, cachê, remuneração ou indenização, abrangendo todo o território nacional e internacional, pelo prazo de 10 (dez) anos a contar da data da assinatura deste termo, permanecendo válido para as publicações realizadas durante sua vigência.

4. PROTEÇÃO DE DADOS PESSOAIS (LGPD — LEI Nº 13.709/2018)
Em conformidade com a Lei Geral de Proteção de Dados Pessoais (Lei nº 13.709/2018), o(a) CEDENTE declara estar ciente e de acordo com o tratamento de seus dados pessoais (nome, documentos de identificação, imagem, voz e metadados de assinatura eletrônica) pelas AUTORIZADAS para a execução das finalidades descritas neste instrumento e para o arquivamento comprobatório desta autorização. O(a) titular poderá exercer os direitos previstos no art. 18 da LGPD mediante solicitação formal aos canais de atendimento da NC5 Hub Digital.

Por ser esta a expressão da minha vontade, firmo o presente termo eletronicamente, reconhecendo sua plena validade jurídica nos termos do art. 10, §2º da Medida Provisória nº 2.200-2/2001 e da Lei nº 14.063/2020.
TEXT,

    'template_menor' => <<<'TEXT'
TERMO DE AUTORIZAÇÃO GRATUITA DE USO DE IMAGEM E VOZ DE MENOR DE IDADE

Pelo presente instrumento particular, eu, {{responsavel_nome}}, inscrito(a) no CPF sob o nº {{responsavel_cpf}}, na qualidade de responsável legal ({{responsavel_parentesco}}) do(a) menor {{cedente_nome}}, inscrito(a) no CPF sob o nº {{cedente_cpf}} e RG nº {{cedente_rg}}, residente e domiciliado(a) em {{cedente_endereco}}, nos termos do Código Civil e do Estatuto da Criança e do Adolescente (Lei nº 8.069/1990 — ECA), AUTORIZO, de forma livre, informada, expressa e inteiramente gratuita, o uso da imagem, som de voz e nome do(a) referido(a) menor, captados no contexto do projeto/gravação "{{projeto}}", realizada em {{data_captacao}}, no local {{local}}, em favor de NC5 Hub Digital e de {{cliente}}, doravante denominadas AUTORIZADAS.

1. FINALIDADE DO USO E RESGUARDO DO MENOR (ECA)
A presente autorização abrange a utilização da imagem e da voz captadas para fins institucionais, publicitários, promocionais, comerciais e informativos, em redes sociais (Instagram, Facebook, TikTok, YouTube, LinkedIn e similares), websites, portfólios, campanhas digitais, peças audiovisuais e materiais impressos, comprometendo-se as AUTORIZADAS a preservar a dignidade, o respeito e a integridade física, psíquica e moral da criança ou adolescente, nos termos dos arts. 17 e 18 da Lei nº 8.069/1990 (ECA).

2. EDIÇÃO E TRATAMENTO DO MATERIAL
Ficam as AUTORIZADAS autorizadas a realizar cortes, edições, montagens, legendagens e tratamentos técnicos de imagem e som necessários à produção das peças, desde que mantido o respeito à imagem do(a) menor e sem qualquer alteração de sentido ou exposição inadequada.

3. GRATUIDADE, TERRITÓRIO E PRAZO
A presente autorização é concedida a título gratuito, sem que nada haja a ser reclamado a título de direitos de imagem, cachê ou remuneração, abrangendo o território nacional e internacional, pelo prazo de 5 (cinco) anos a contar da data da assinatura deste instrumento.

4. PROTEÇÃO DE DADOS PESSOAIS (LGPD — ART. 14 DA LEI Nº 13.709/2018)
Em estrita observância ao art. 14 da Lei Geral de Proteção de Dados Pessoais (Lei nº 13.709/2018), o(a) RESPONSÁVEL LEGAL concede consentimento específico e em destaque para o tratamento dos dados pessoais do(a) menor (nome, imagem, voz e registro de participação) e de seus próprios dados de identificação, exclusivamente para as finalidades previstas neste termo e no melhor interesse do(a) menor.

Por ser esta a expressão da minha vontade e no exercício do poder familiar/responsabilidade legal, firmo o presente termo eletronicamente, reconhecendo sua plena validade jurídica nos termos do art. 10, §2º da Medida Provisória nº 2.200-2/2001 e da Lei nº 14.063/2020.
TEXT,

];
