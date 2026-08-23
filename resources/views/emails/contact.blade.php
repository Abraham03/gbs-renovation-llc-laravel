<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Inquiry - GBS Renovations</title>
    <style>
        /* Reseteo básico para clientes de correo */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { border-collapse: collapse; }
        img { -ms-interpolation-mode: bicubic; }
        
        /* Estilos generales */
        body { 
            margin: 0; 
            padding: 0; 
            background-color: #121212; 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            color: #E0E0E0;
            width: 100% !important;
        }

        /* Contenedor principal */
        .wrapper {
            width: 100%;
            background-color: #121212;
            padding: 40px 20px;
        }

        .main-card {
            max-width: 600px;
            margin: 0 auto;
            background-color: #030303;
            border: 1px solid #222222;
            border-top: 4px solid #D4AF37;
            border-radius: 8px;
            overflow: hidden;
        }

        /* Encabezado */
        .header {
            padding: 30px;
            text-align: center;
            border-bottom: 1px solid #1A1A1A;
            background-color: #0A0A0A;
        }

        .header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 24px;
            font-weight: normal;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .header span {
            color: #D4AF37;
            font-style: italic;
        }

        .subtitle {
            margin-top: 10px;
            color: #888888;
            font-size: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Cuerpo del contenido */
        .content {
            padding: 30px;
        }

        .intro-text {
            font-size: 16px;
            color: #ffffff;
            margin-bottom: 25px;
            font-weight: 500;
        }

        /* Tabla de datos */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table td {
            padding: 15px 0;
            border-bottom: 1px solid #1A1A1A;
            vertical-align: top;
        }

        .data-table td:last-child {
            border-bottom: none;
        }

        .label {
            color: #AA8529;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: bold;
            width: 35%;
            padding-right: 15px;
        }

        .value {
            color: #E0E0E0;
            font-size: 14px;
            line-height: 1.5;
        }

        /* Forzar color de los enlaces (evitar el azul de Gmail) */
        .value a {
            color: #E0E0E0 !important;
            text-decoration: none;
        }

        .message-box {
            background-color: #0A0A0A;
            border: 1px solid #1A1A1A;
            border-radius: 6px;
            padding: 20px;
            margin-top: 10px;
            color: #cccccc;
            font-size: 14px;
            line-height: 1.6;
            white-space: pre-wrap;
        }

        /* Pie de página */
        .footer {
            text-align: center;
            padding: 20px;
            color: #555555;
            font-size: 11px;
            background-color: #080808;
            border-top: 1px solid #1A1A1A;
        }
    </style>
</head>
<body>
    <table class="wrapper" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center">
                <table class="main-card" cellpadding="0" cellspacing="0" border="0" width="100%">
                    
                    <tr>
                        <td class="header">
                            <h1>GBS <span>Renovations</span></h1>
                            <div class="subtitle">Website Contact Form</div>
                        </td>
                    </tr>

                    <tr>
                        <td class="content">
                            <div class="intro-text">New Inquiry Details</div>
                            
                            <table class="data-table" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td class="label">Name:</td>
                                    <td class="value">{{ $contactData['name'] }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Email:</td>
                                    <td class="value"><a href="mailto:{{ $contactData['email'] }}">{{ $contactData['email'] }}</a></td>
                                </tr>
                                <tr>
                                    <td class="label">Phone:</td>
                                    <td class="value"><a href="tel:{{ $contactData['phone'] }}">{{ $contactData['phone'] }}</a></td>
                                </tr>
                                <tr>
                                    <td class="label">Service:</td>
                                    <td class="value" style="color: #D4AF37; font-weight: bold;">{{ $contactData['service'] }}</td>
                                </tr>
                            </table>

                            <div style="margin-top: 25px;">
                                <div class="label" style="margin-bottom: 10px;">Message:</div>
                                <div class="message-box">
                                    {{ $contactData['message'] }}
                                </div>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="footer">
                            This message was sent automatically from the GBS Renovations website. <br>
                            Reply directly to this email to contact the prospective client.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>