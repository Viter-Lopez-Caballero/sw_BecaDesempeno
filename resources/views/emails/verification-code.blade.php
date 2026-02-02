<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirma tu Correo Electrónico</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background-color: #ffffff;
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
        }

        .logos {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
            margin-bottom: 20px;
        }

        .logo-sep {
            width: 100px;
            height: auto;
        }

        .logo-cenidet {
            width: 120px;
            height: auto;
        }

        .content {
            padding: 40px 30px;
            text-align: center;
        }

        h1 {
            color: #1f2937;
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .message {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .code-box {
            background-color: #f9fafb;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 30px;
            margin: 30px 0;
        }

        .code {
            font-size: 36px;
            font-weight: 700;
            letter-spacing: 8px;
            color: #1f2937;
            font-family: 'Courier New', monospace;
        }

        .code-label {
            color: #9ca3af;
            font-size: 12px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .button {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            margin: 20px 0;
            transition: background-color 0.2s;
        }

        .button:hover {
            background-color: #1d4ed8;
        }

        .footer {
            padding: 20px 30px;
            background-color: #f9fafb;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }

        .warning {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
            border-radius: 4px;
        }

        .warning p {
            color: #92400e;
            font-size: 13px;
            margin: 0;
        }

        .expiry {
            color: #ef4444;
            font-size: 13px;
            margin-top: 15px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logos">
                <img src="{{ asset('img/logo-sep.png') }}" alt="SEP" class="logo-sep">
                <img src="{{ asset('img/logo-cenidet.png') }}" alt="CENIDET" class="logo-cenidet">
            </div>
        </div>

        <div class="content">
            <h1>Confirma tu Correo Electrónico</h1>
            
            <p class="message">
                Hola {{ $userName }},<br><br>
                Hemos enviado un código de confirmación a tu dirección de email.
                Por favor revisa tu bandeja de entrada y haz clic en el enlace para activar tu cuenta.
            </p>

            <div class="code-box">
                <p class="code-label">Tu código de verificación es:</p>
                <div class="code">{{ $code }}</div>
                <p class="expiry">
                    Este código expira en 24 horas
                </p>
            </div>

            <a href="{{ route('verification.notice') }}" class="button">
                ← Volver e Iniciar Sesión
            </a>

            <div class="warning">
                <p>
                    Si no encontrarás el correo, revisa la carpeta de spam o correo no deseado.
                </p>
            </div>
        </div>

        <div class="footer">
            <p>
                Este es un correo automático, por favor no respondas a este mensaje.<br>
                © {{ date('Y') }} Sistema Nacional de Educación - CENIDET
            </p>
        </div>
    </div>
</body>
</html>
