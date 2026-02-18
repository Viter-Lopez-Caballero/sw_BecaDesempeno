<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevas Evaluaciones Asignadas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            min-height: 100vh;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #1B396A 0%, #0f2347 100%);
            padding: 40px 30px;
            text-align: center;
        }

        .header-title {
            color: #ffffff;
            font-size: 26px;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .content {
            padding: 40px 50px;
        }

        .greeting {
            color: #1B396A;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .message {
            color: #4B5563;
            font-size: 15px;
            line-height: 1.8;
            margin-bottom: 25px;
        }

        .info-box {
            background: linear-gradient(to bottom right, #EEF2FF, #E0E7FF);
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            border-left: 4px solid #1B396A;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-label {
            color: #374151;
            font-size: 14px;
            font-weight: 600;
        }

        .info-value {
            color: #1B396A;
            font-size: 24px;
            font-weight: 700;
        }

        .warning-box {
            background: linear-gradient(to bottom right, #FEF3C7, #FDE68A);
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            border-left: 4px solid #F59E0B;
        }

        .warning-text {
            color: #92400E;
            font-size: 14px;
            font-weight: 600;
            margin: 0;
        }

        .footer {
            background-color: #F9FAFB;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #E5E7EB;
        }

        .footer-text {
            color: #6B7280;
            font-size: 12px;
            line-height: 1.5;
        }

        @media only screen and (max-width: 600px) {
            .content {
                padding: 30px 25px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1 class="header-title">Nuevas Evaluaciones Asignadas</h1>
        </div>

        <div class="content">
            <p class="greeting">Hola {{ $evaluatorName }},</p>

            <p class="message">
                Se te han asignado nuevas evaluaciones en el Sistema de Becas TecNM. 
                Por favor, revisa tu panel de evaluaciones para comenzar con la revisión.
            </p>

            <div class="info-box">
                <div class="info-item">
                    <span class="info-label">Evaluaciones asignadas:</span>
                    <span class="info-value">{{ $evaluationsCount }}</span>
                </div>
            </div>

            <div class="warning-box">
                <p class="warning-text">
                    ⏰ Tienes {{ $daysLimit }} días calendario para completar las evaluaciones a partir de hoy. 
                    Pasado este tiempo, las asignaciones serán removidas automáticamente.
                </p>
            </div>

            <p class="message">
                Te recomendamos iniciar sesión lo antes posible para revisar las solicitudes asignadas.
            </p>
        </div>

        <div class="footer">
            <p class="footer-text">
                Este es un mensaje automático del Sistema de Becas TecNM.<br>
                Por favor, no respondas a este correo.
            </p>
        </div>
    </div>
</body>
</html>
