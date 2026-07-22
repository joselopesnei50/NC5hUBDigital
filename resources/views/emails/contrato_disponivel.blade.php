<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Novo contrato para assinatura</title>
</head>
<body style="margin:0;padding:0;background-color:#F4F5F7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#0A1128;">

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#F4F5F7;padding:40px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" width="560" cellspacing="0" cellpadding="0" border="0" style="max-width:560px;width:100%;background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(10,17,40,0.06);">

                <tr>
                    <td style="background-color:#0A1128;padding:32px 40px;">
                        <p style="margin:0;font-size:11px;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:#E63888;">NC5 Hub</p>
                        <h1 style="margin:8px 0 0 0;font-size:26px;font-weight:800;color:#ffffff;line-height:1.2;">Contrato disponível para assinatura.</h1>
                    </td>
                </tr>

                <tr>
                    <td style="padding:36px 40px 24px 40px;">
                        <p style="margin:0 0 16px 0;font-size:16px;line-height:1.6;color:#0A1128;">
                            Olá, <strong>{{ $contrato->cliente->razao_social }}</strong>.
                        </p>
                        <p style="margin:0 0 24px 0;font-size:15px;line-height:1.65;color:#3A3A3C;">
                            A NC5 Hub emitiu um novo contrato para você. Acesse seu portal para ler e assinar eletronicamente.
                        </p>

                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#F4F5F7;border-radius:12px;margin:8px 0 28px 0;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <p style="margin:0 0 4px 0;font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#8A8F9C;">Serviço</p>
                                    <p style="margin:0 0 18px 0;font-size:15px;font-weight:600;color:#0A1128;">{{ $contrato->servico->nome ?? 'Contrato Avulso' }}</p>

                                    <p style="margin:0 0 4px 0;font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#8A8F9C;">Data de Início</p>
                                    <p style="margin:0;font-size:15px;font-weight:600;color:#0A1128;">{{ \Carbon\Carbon::parse($contrato->data_inicio)->format('d/m/Y') }}</p>
                                </td>
                            </tr>
                        </table>

                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td align="center" style="padding:8px 0 12px 0;">
                                    <a href="{{ $painelUrl }}"
                                       style="display:inline-block;background-color:#E63888;color:#ffffff;text-decoration:none;font-weight:700;font-size:15px;padding:14px 32px;border-radius:999px;">
                                        Ver e Assinar Contrato
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:24px 0 0 0;font-size:13px;line-height:1.6;color:#8A8F9C;">
                            Se você tiver dúvidas, entre em contato com nossa equipe. Sua assinatura eletrônica tem validade jurídica.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:24px 40px 32px 40px;border-top:1px solid #F0F1F3;">
                        <p style="margin:0;font-size:12px;color:#8A8F9C;line-height:1.6;">
                            NC5 Hub — Marketing, Design & Performance<br>
                            Este e-mail foi enviado para <strong>{{ $contrato->cliente->user->email ?? '' }}</strong>.
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
