<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titleText }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #005B9C 0%, #002B49 100%);
            padding: 24px 32px;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 4px 0 0 0;
            font-size: 13px;
            opacity: 0.85;
        }
        .content {
            padding: 32px;
        }
        .greeting {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #0f172a;
        }
        .message-box {
            background-color: #f1f5f9;
            border-left: 4px solid #005B9C;
            padding: 16px 20px;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 14px;
            line-height: 1.6;
            color: #334155;
        }
        .button {
            display: inline-block;
            background-color: #005B9C;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            margin-top: 12px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px 32px;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #64748b;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>PELINDO INTERNSHIP PORTAL</h1>
            <p>PT Pelabuhan Indonesia (Persero) - Solusi Digital</p>
        </div>
        <div class="content">
            <div class="greeting">Halo, {{ $userName }} 👋</div>
            <p style="font-size: 14px; color: #475569; margin: 0 0 16px 0;">
                Anda menerima notifikasi pengingat dari portal resmi magang Pelindo.
            </p>

            <div class="message-box">
                <strong style="color: #005B9C; display: block; margin-bottom: 4px;">{{ $titleText }}</strong>
                {{ $messageText }}
            </div>

            <a href="{{ config('app.url') }}" class="button">Buka Portal Magang</a>
        </div>
        <div class="footer">
            Email ini dikirimkan secara otomatis oleh Sistem Pelindo Internship Portal (PRO).<br>
            Harap tidak membalas email ini secara langsung.
        </div>
    </div>
</body>
</html>
