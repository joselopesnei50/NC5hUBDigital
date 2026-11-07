<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Termo de Autorização de Uso de Imagem e Voz</title>
</head>
<body style="margin:0;padding:0;background-color:#F4F5F7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#0A1128;">

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#F4F5F7;padding:40px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" width="560" cellspacing="0" cellpadding="0" border="0" style="max-width:560px;width:100%;background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(10,17,40,0.06);">

                <tr>
                    <td style="background-color:#0A1128;padding:32px 40px;">
                        <p style="margin:0;font-size:11px;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:#FF7A1A;">NC5 Hub Digital</p>
                        <h1 style="margin:8px 0 0 0;font-size:24px;font-weight:800;color:#ffffff;line-height:1.2;">Autorização de Uso de Imagem e Voz.</h1>
                    </td>
                </tr>

                <tr>
                    <td style="padding:36px 40px 24px 40px;">
                        <p style="margin:0 0 16px 0;font-size:16px;line-height:1.6;color:#0A1128;">
                            Olá, <strong>{{ $termo->nome_assinante_esperado }}</strong>.
                        </p>
                        <p style="margin:0 0 24px 0;font-size:15px;line-height:1.65;color:#3A3A3C;">
                            A NC5 Hub Digital disponibilizou um Termo de Autorização de Uso de Imagem e Voz para leitura e assinatura eletrônica diretamente pelo seu celular ou computador.
                        </p>

                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#F4F5F7;border-radius:12px;margin:8px 0 28px 0;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <p style="margin:0 0 4px 0;font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#8A8F9C;">Termo</p>
                                    <p style="margin:0 0 18px 0;font-size:15px;font-weight:600;color:#0A1128;">{{ $termo->titulo }}</p>

                                    <p style="margin:0 0 4px 0;font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#8A8F9C;">Cedente</p>
                                    <p style="margin:0 0 18px 0;font-size:15px;font-weight:600;color:#0A1128;">{{ $termo->cedente_nome }}</p>

                                    @if($termo->projeto_referencia || $termo->data_captacao)
                                        <p style="margin:0 0 4px 0;font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#8A8F9C;">Projeto / Data</p>
                                        <p style="margin:0;font-size:15px;font-weight:600;color:#0A1128;">
                                            {{ $termo->projeto_referencia ?: 'Produção Audiovisual' }}
                                            @if($termo->data_captacao) · {{ $termo->data_captacao->format('d/m/Y') }} @endif
                                        </p>
                                    @endif
                                </td>
                            </tr>
                        </table>

                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td align="center" style="padding:8px 0 12px 0;">
                                    <a href="{{ $signUrl }}"
                                       style="display:inline-block;background-color:#FF7A1A;color:#ffffff;text-decoration:none;font-weight:700;font-size:15px;padding:14px 32px;border-radius:999px;">
                                        Ler e assinar termo
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:24px 0 0 0;font-size:12px;line-height:1.6;color:#8A8F9C;word-break:break-all;">
                            Ou abra o link direto:<br>
                            <a href="{{ $signUrl }}" style="color:#0A1128;">{{ $signUrl }}</a>
                        </p>

                        <p style="margin:16px 0 0 0;font-size:13px;line-height:1.6;color:#8A8F9C;">
                            A assinatura eletrônica tem validade jurídica (art. 10, §2º da MP 2.200-2/2001 e Lei nº 14.063/2020).
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:24px 40px 32px 40px;border-top:1px solid #F0F1F3;">
                        <p style="margin:0;font-size:12px;color:#8A8F9C;line-height:1.6;">
                            NC5 Hub Digital — Marketing, Design & Performance<br>
                            Este e-mail foi enviado para <strong>{{ $termo->cedente_email ?? '' }}</strong>.
                        </p>
                    </td>
                </tr>
            </table>

            <p style="margin:20px auto 0;font-size:11px;color:#8A8F9C;max-width:560px;text-align:center;">
                Enviado automaticamente por NC5 Hub · nc5hubdigital.com.br
            </p>
        </td>
    </tr>
</table>

</body>
</html>
