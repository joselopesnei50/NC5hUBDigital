<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f5f7;
            margin: 0;
            padding: 0;
            color: #0a1128;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #0a1128;
            padding: 30px 20px;
            text-align: center;
        }
        .header img {
            max-height: 40px;
        }
        .content {
            padding: 40px 30px;
            line-height: 1.6;
        }
        .content p {
            margin-bottom: 20px;
            color: #4a5568;
            font-size: 16px;
        }
        .button-container {
            text-align: center;
            margin: 35px 0;
        }
        .button {
            display: inline-block;
            background-color: #ff7a1a;
            color: #ffffff;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            color: #a0aec0;
            font-size: 13px;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Caso tenha uma imagem pública da logo, você pode colocar a URL absoluta aqui -->
            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">NC5 Hub Digital</h1>
        </div>
        
        <div class="content">
            {!! nl2br(e($mensagem)) !!}
            
            @if($linkAcao)
            <div class="button-container">
                <a href="{{ $linkAcao }}" class="button">{{ $textoBotao }}</a>
            </div>
            @endif

            <p style="margin-top: 40px; font-size: 14px; color: #718096;">
                Atenciosamente,<br>
                <strong>Equipe NC5</strong>
            </p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} NC5 Hub Digital. Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>
