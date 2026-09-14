<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerta do BruceIA</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #F4F4F5;
            margin: 0;
            padding: 0;
            color: #0A0A0B;
        }
        .container {
            max-width: 620px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px -10px rgba(10,10,11,0.15);
        }
        .header {
            background-color: #0A0A0B;
            padding: 30px 32px;
            color: #ffffff;
        }
        .header-title {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin: 0;
        }
        .header-title .accent { color: #FF7A1A; }
        .header-sub {
            color: rgba(255,255,255,0.6);
            font-size: 13px;
            margin-top: 6px;
        }
        .content {
            padding: 32px;
            line-height: 1.6;
        }
        .greeting {
            font-size: 16px;
            color: #0A0A0B;
            margin: 0 0 24px;
        }
        .alert-card {
            border: 1px solid #fecaca;
            background-color: #fef2f2;
            border-radius: 14px;
            padding: 18px 20px;
            margin-bottom: 14px;
        }
        .alert-badge {
            display: inline-block;
            background-color: #fee2e2;
            color: #991b1b;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 999px;
        }
        .alert-title {
            font-size: 16px;
            font-weight: 800;
            color: #0A0A0B;
            margin: 10px 0 6px;
        }
        .alert-msg {
            color: #3A3A3C;
            font-size: 14px;
            margin: 0;
        }
        .cta-box { text-align: center; margin: 32px 0 16px; }
        .cta {
            display: inline-block;
            background-color: #0A0A0B;
            color: #ffffff;
            text-decoration: none;
            padding: 14px 30px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 0.3px;
        }
        .cta:hover { background-color: #FF7A1A; }
        .disclaimer {
            font-size: 12px;
            color: #6b7280;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            line-height: 1.6;
        }
        .footer {
            background-color: #0A0A0B;
            color: rgba(255,255,255,0.5);
            padding: 20px 32px;
            text-align: center;
            font-size: 11px;
        }
        .footer a { color: rgba(255,255,255,0.7); text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <p class="header-title">Bruce<span class="accent">IA</span></p>
            <p class="header-sub">Análise proativa do seu negócio · {{ now()->translatedFormat('d \d\e F, Y') }}</p>
        </div>

        <div class="content">
            <p class="greeting">
                Olá <strong>{{ $cliente->razao_social }}</strong>,
            </p>
            <p style="color:#3A3A3C; font-size:14px; margin:0 0 24px;">
                Rodei a análise diária do seu negócio e encontrei
                <strong style="color:#0A0A0B;">{{ $alertas->count() }} {{ $alertas->count() === 1 ? 'ponto crítico' : 'pontos críticos' }}</strong>
                que precisam da sua decisão nas próximas horas:
            </p>

            @foreach($alertas as $a)
                <div class="alert-card">
                    <span class="alert-badge">Crítico</span>
                    <p class="alert-title">{{ $a->titulo }}</p>
                    <p class="alert-msg">{{ $a->mensagem }}</p>
                </div>
            @endforeach

            <div class="cta-box">
                <a href="{{ $linkPainel }}" class="cta">Abrir alertas no painel →</a>
            </div>

            <p class="disclaimer">
                Você recebeu este e-mail porque o Bruce identificou pelo menos um alerta de severidade crítica na sua operação.
                Alertas de atenção e informativos ficam apenas no painel.
                Se você já resolveu ou não pretende agir, dispense o alerta no painel — assim eu paro de reemiti-lo até que a situação apareça de novo.
            </p>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} BruceIA — inteligência artificial do <a href="{{ config('app.url') }}">NC5 Hub Digital</a>.
        </div>
    </div>
</body>
</html>
