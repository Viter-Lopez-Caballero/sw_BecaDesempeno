<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambio de Etapa en Convocatoria</title>
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

        .announcement-box {
            background: #F3F4F6;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
        }

        .announcement-label {
            color: #6B7280;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .announcement-title {
            color: #1B396A;
            font-size: 16px;
            font-weight: 600;
        }

        .stage-box {
            background: linear-gradient(to bottom right, #DBEAFE, #BFDBFE);
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            text-align: center;
            border-left: 4px solid #3B82F6;
        }

        .stage-label {
            color: #1E40AF;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }

        .stage-name {
            color: #1E3A8A;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .stage-date {
            color: #1E40AF;
            font-size: 14px;
            font-weight: 500;
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
            <h1 class="header-title">Cambio de Etapa en Convocatoria</h1>
        </div>

        <div class="content">
            <p class="greeting">Hola {{ $userName }},</p>

            <p class="message">
                Te informamos que ha ocurrido un cambio de etapa en la siguiente convocatoria:
            </p>

            <div class="announcement-box">
                <p class="announcement-label">Convocatoria</p>
                <p class="announcement-title">{{ $announcementTitle }}</p>
            </div>

            <div class="stage-box">
                <p class="stage-label">Nueva Etapa</p>
                <p class="stage-name">{{ $newStage }}</p>
                @if($stageDate)
                    <p class="stage-date">Fecha: {{ $stageDate }}</p>
                @endif
            </div>

            <p class="message">
                Por favor, ingresa al sistema para conocer más detalles sobre esta etapa y las acciones que debes realizar.
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
