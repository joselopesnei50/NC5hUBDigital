<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Material Avaliado pelo Cliente — NC5 Hub</title>
</head>
<body style="margin:0;padding:0;background-color:#F4F5F7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#0A1128;">

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#F4F5F7;padding:40px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" width="560" cellspacing="0" cellpadding="0" border="0" style="max-width:560px;width:100%;background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(10,17,40,0.06);">

                <tr>
                    <td style="background-color:#0A1128;padding:32px 40px;">
                        <p style="margin:0;font-size:11px;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:#FF7A1A;">NC5 Hub — Alerta de Produção</p>
                        <h1 style="margin:8px 0 0 0;font-size:24px;font-weight:800;color:#ffffff;line-height:1.2;">Cliente avaliou o material!</h1>
                    </td>
                </tr>

                <tr>
                    <td style="padding:36px 40px 24px 40px;">
                        <p style="margin:0 0 16px 0;font-size:16px;line-height:1.6;color:#0A1128;">
                            O cliente <strong>{{ $material->cliente->razao_social ?? 'Cliente' }}</strong> respondeu sobre o material enviado.
                        </p>

                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#F4F5F7;border-radius:12px;margin:8px 0 28px 0;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <p style="margin:0 0 4px 0;font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#8A8F9C;">Material</p>
                                    <p style="margin:0 0 16px 0;font-size:16px;font-weight:700;color:#0A1128;">{{ $material->titulo }}</p>

                                    <p style="margin:0 0 4px 0;font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#8A8F9C;">Decisão do Cliente</p>
                                    <p style="margin:0 0 16px 0;font-size:15px;font-weight:800;
                                        @if($material->status_aprovacao === 'aprovado') color:#059669;
                                        @elseif($material->status_aprovacao === 'ajustes_solicitados') color:#D97706;
                                        @else color:#DC2626; @endif">
                                        {{ match($material->status_aprovacao) {
                                            'aprovado' => '✓ APROVADO',
                                            'ajustes_solicitados' => '⚠️ AJUSTES SOLICITADOS',
                                            'reprovado' => '❌ REPROVADO',
                                            default => strtoupper($material->status_aprovacao)
                                        } }}
                                    </p>

                                    @if($material->comentario_cliente)
                                    <p style="margin:0 0 4px 0;font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#8A8F9C;">Observações / Comentários do Cliente</p>
                                    <div style="background:#ffffff;border:1px solid #E5E7EB;border-radius:8px;padding:14px;margin-top:4px;font-size:14px;line-height:1.6;color:#1F2937;">
                                        "{{ $material->comentario_cliente }}"
                                    </div>
                                    @endif
                                </td>
                            </tr>
                        </table>

                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td align="center" style="padding:8px 0 12px 0;">
                                    <a href="{{ $adminUrl }}"
                                       style="display:inline-block;background-color:#0A1128;color:#ffffff;text-decoration:none;font-weight:700;font-size:15px;padding:14px 32px;border-radius:999px;">
                                        Abrir no Painel Admin
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding:24px 40px 32px 40px;border-top:1px solid #F0F1F3;">
                        <p style="margin:0;font-size:12px;color:#8A8F9C;line-height:1.6;">
                            NC5 Hub Digital — Sistema Interno de Notificações
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>
