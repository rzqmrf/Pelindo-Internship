<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Magang Pelindo - {{ $intern->name }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            color: #1e293b;
            -webkit-print-color-adjust: exact;
        }
        .cert-container {
            position: relative;
            width: 297mm;
            height: 210mm;
            box-sizing: border-box;
            padding: 12mm 15mm;
            background: #ffffff;
        }
        /* Outer Decorative Border */
        .border-outer {
            width: 100%;
            height: 100%;
            border: 4px solid #00529C; /* Pelindo Blue */
            box-sizing: border-box;
            position: relative;
            padding: 6px;
        }
        .border-inner {
            width: 100%;
            height: 100%;
            border: 1.5px solid #D97706; /* Elegant Gold */
            box-sizing: border-box;
            padding: 20px 35px;
            text-align: center;
            position: relative;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        }
        
        /* Corner Accents */
        .corner-tl { position: absolute; top: -4px; left: -4px; width: 30px; height: 30px; border-top: 5px solid #D97706; border-left: 5px solid #D97706; }
        .corner-tr { position: absolute; top: -4px; right: -4px; width: 30px; height: 30px; border-top: 5px solid #D97706; border-right: 5px solid #D97706; }
        .corner-bl { position: absolute; bottom: -4px; left: -4px; width: 30px; height: 30px; border-bottom: 5px solid #D97706; border-left: 5px solid #D97706; }
        .corner-br { position: absolute; bottom: -4px; right: -4px; width: 30px; height: 30px; border-bottom: 5px solid #D97706; border-right: 5px solid #D97706; }

        .header-logo {
            margin-bottom: 8px;
        }
        .company-title {
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 2px;
            color: #00529C;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .sub-company {
            font-size: 10px;
            color: #64748b;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }
        .cert-title {
            font-size: 30px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin: 5px 0 2px 0;
            font-family: Georgia, 'Times New Roman', serif;
        }
        .cert-number {
            font-size: 11px;
            color: #b45309;
            font-weight: 600;
            letter-spacing: 1.5px;
            margin-bottom: 15px;
        }
        .given-to {
            font-size: 12px;
            color: #475569;
            font-style: italic;
            margin-bottom: 8px;
        }
        .recipient-name {
            font-size: 26px;
            font-weight: bold;
            color: #00529C;
            text-decoration: none;
            border-bottom: 2px solid #D97706;
            display: inline-block;
            padding-bottom: 4px;
            margin-bottom: 8px;
            font-family: Georgia, 'Times New Roman', serif;
        }
        .recipient-meta {
            font-size: 12px;
            color: #334155;
            margin-bottom: 15px;
            font-weight: 500;
        }
        .cert-desc {
            font-size: 11.5px;
            color: #334155;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto 15px auto;
        }
        .predicate-badge {
            display: inline-block;
            background-color: #f0fdf4;
            border: 1px solid #86efac;
            color: #166534;
            padding: 4px 16px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 20px;
            margin-bottom: 20px;
        }

        /* Signatures & QR */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .sign-box {
            text-align: center;
            vertical-align: top;
            width: 38%;
            font-size: 11px;
        }
        .qr-box {
            text-align: center;
            vertical-align: middle;
            width: 24%;
        }
        .sign-title {
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 45px;
        }
        .sign-name {
            font-weight: bold;
            font-size: 12px;
            color: #0f172a;
            text-decoration: underline;
        }
        .sign-nip {
            font-size: 9.5px;
            color: #64748b;
            margin-top: 2px;
        }
        .qr-text {
            font-size: 8px;
            color: #64748b;
            margin-top: 4px;
            line-height: 1.2;
        }
    </style>
</head>
<body>
    <div class="cert-container">
        <div class="border-outer">
            <div class="corner-tl"></div>
            <div class="corner-tr"></div>
            <div class="corner-bl"></div>
            <div class="corner-br"></div>
            
            <div class="border-inner">
                <!-- Header -->
                <div class="company-title">{{ $companyName }}</div>
                <div class="sub-company">REGIONAL 3 SURABAYA — PROGRAM PRAKTIK KERJA LAPANGAN & MAGANG</div>

                <!-- Title -->
                <div class="cert-title">SERTIFIKAT MAGANG</div>
                <div class="cert-number">NO: {{ $certificate->certificate_number }}</div>

                <!-- Given to -->
                <div class="given-to">Diberikan sebagai penghargaan dan bukti kelulusan kepada:</div>
                <div class="recipient-name">{{ $intern->name }}</div>
                <div class="recipient-meta">
                    NIM/NIS: <strong>{{ $profile?->student_id_number ?? '-' }}</strong> &bull; 
                    {{ $profile?->major ?? 'Program Studi' }} &bull; 
                    <strong>{{ $profile?->institution ?? '-' }}</strong>
                </div>

                <!-- Description -->
                <div class="cert-desc">
                    Telah menyelesaikan program Praktik Kerja Lapangan (Internship) di <strong>PT Pelabuhan Indonesia (Persero)</strong> pada divisi <strong>{{ $profile?->division?->name ?? 'Divisi Umum' }}</strong> terhitung mulai tanggal <strong>{{ $startDateFormatted }}</strong> sampai dengan <strong>{{ $endDateFormatted }}</strong> dengan capaian predikat:
                </div>

                <div class="predicate-badge">
                    PREDIKAT: {{ strtoupper($assessment?->predicate ?? 'SANGAT BAIK (A)') }} (SKOR AKHIR: {{ number_format($assessment?->final_score ?? 90, 2) }}/100)
                </div>

                <!-- Signatures and QR Code -->
                <table class="footer-table">
                    <tr>
                        <td class="sign-box">
                            <div class="sign-title">Pembimbing Lapangan,</div>
                            <div class="sign-name">{{ $supervisor?->name ?? '-' }}</div>
                            <div class="sign-nip">NIP. {{ $supervisor?->supervisorProfile?->employee_id_number ?? '-' }}</div>
                        </td>

                        <td class="qr-box">
                            <!-- Public Verification Link and Indicator -->
                            <div style="border: 1px dashed #cbd5e1; padding: 6px; border-radius: 8px; display: inline-block; background: #ffffff;">
                                <div style="font-size: 9px; font-weight: bold; color: #00529C; margin-bottom: 2px;">VERIFIKASI DIGITAL</div>
                                <div style="font-size: 8px; color: #475569; word-break: break-all; max-width: 130px;">
                                    Token: {{ substr($certificate->verification_token, 0, 16) }}...
                                </div>
                            </div>
                            <div class="qr-text">
                                Terverifikasi Resmi Sistem Pelindo<br>
                                Surabaya, {{ $issueDateFormatted }}
                            </div>
                        </td>

                        <td class="sign-box">
                            <div class="sign-title">Manajer Divisi / HR,</div>
                            <div class="sign-name">{{ $profile?->division?->head_name ?? 'Mohammad Nasrul Rohman' }}</div>
                            <div class="sign-nip">PT PELABUHAN INDONESIA (PERSERO)</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
