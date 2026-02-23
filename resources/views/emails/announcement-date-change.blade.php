<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambio de Fechas en Convocatoria</title>
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
            max-width: 800px;
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
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .content {
            padding: 50px 60px;
            text-align: center;
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
            margin-bottom: 35px;
        }

        .announcement-box {
            background: linear-gradient(120deg, #f8fafc 60%, #e0e7ef 100%);
            border-radius: 14px;
            padding: 28px 24px;
            margin: 28px 0 36px 0;
            border: 1.5px solid #dbeafe;
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

        .changes-box {
            background: #f3f6fb;
            border-radius: 14px;
            padding: 28px 20px;
            margin: 0 0 36px 0;
            border: 1.5px solid #dbeafe;
        }

        .changes-title {
            color: #1B396A;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 22px;
            letter-spacing: 0.5px;
            text-align: left;
        }

        .change-item {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            background: #fff;
            border-radius: 10px;
            padding: 18px 20px;
            margin-bottom: 16px;
            border: 1px solid #e0e7ef;
            box-shadow: 0 2px 8px rgba(27,57,106,0.04);
        }

        .change-item:last-child {
            margin-bottom: 0;
        }

        .change-label {
            color: #1B396A;
            font-size: 15px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0;
            flex: 1 1 180px;
            text-align: left;
        }

        .change-dates {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1 1 220px;
            justify-content: flex-end;
        }

        .old-value {
            color: #64748b;
            background: #e0e7ef;
            border-radius: 6px;
            padding: 6px 14px;
            font-size: 15px;
            font-weight: 500;
            text-decoration: line-through;
            margin-right: 0;
        }

        .arrow {
            color: #1B396A;
            font-size: 18px;
            font-weight: 700;
            margin: 0 4px;
        }

        .new-value {
            color: #fff;
            background: #1B396A;
            border-radius: 6px;
            padding: 6px 14px;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .footer {
            padding: 30px 40px;
            background: linear-gradient(to right, #F9FAFB, #F3F4F6);
            text-align: center;
            border-top: 2px solid #E5E7EB;
        }

        .footer-text {
            color: #6B7280;
            font-size: 12px;
            line-height: 1.8;
            margin: 0;
        }

        /* Responsive Design para Móviles */
        @media only screen and (max-width: 600px) {
            body {
                padding: 15px 8px;
            }

            .email-container {
                max-width: 100%;
                border-radius: 10px;
            }

            .header {
                padding: 20px 12px;
            }

            .header-title {
                font-size: 15px;
                line-height: 1.3;
            }

            .content {
                padding: 25px 15px;
            }

            .greeting {
                font-size: 16px;
            }

            .message {
                font-size: 13px;
                line-height: 1.6;
            }

            .announcement-box {
                padding: 15px;
                margin: 15px 0;
            }

            .changes-box {
                padding: 15px 8px;
            }

            .change-item {
                flex-direction: column;
                align-items: flex-start;
                padding: 15px;
                gap: 10px;
            }

            .change-label,
            .change-dates {
                flex: unset;
                width: 100%;
                text-align: left;
                box-sizing: border-box;
            }

            .change-dates {
                justify-content: flex-start;
                flex-wrap: wrap;
            }

            .old-value,
            .new-value {
                font-size: 13px;
                padding: 6px 10px;
            }

            .footer {
                padding: 18px 12px;
            }

            .footer-text {
                font-size: 10px;
                line-height: 1.6;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1 class="header-title">Cambio de Fechas en Convocatoria</h1>
        </div>

        <div class="content">
            <p class="greeting">Hola {{ $userName }},</p>

            <p class="message">
                Te informamos que se han actualizado las fechas de la siguiente convocatoria:
            </p>

            <div class="announcement-box">
                <p class="announcement-label">Convocatoria</p>
                <p class="announcement-title">{{ $announcementTitle }}</p>
            </div>

            <div class="changes-box">
                <p class="changes-title">Cambios realizados:</p>
                @foreach($changes as $change)
                    <div class="change-item">
                        <span class="change-label">{{ $change['label'] }}</span>
                        <span class="change-dates">
                            <span class="old-value">{{ \Carbon\Carbon::parse($change['old'])->translatedFormat('d \d\e F \d\e\l Y') }}</span>
                            <span class="arrow">→</span>
                            <span class="new-value">{{ \Carbon\Carbon::parse($change['new'])->translatedFormat('d \d\e F \d\e\l Y') }}</span>
                        </span>
                    </div>
                @endforeach
            </div>

            <p class="message">
                Por favor, toma nota de estos cambios y ajusta tus plazos en consecuencia.
                Puedes consultar el calendario completo ingresando al sistema.
            </p>
        </div>

        <div class="footer">
            <p class="footer-text">
                <strong>Programa de Estímulos al Desempeño del Personal Docente - TecNM</strong><br>
                Este es un mensaje automático del Sistema de Becas TecNM.<br>
                Por favor, no respondas a este correo.<br>
                © {{ date('Y') }} Tecnológico Nacional de México. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html>
