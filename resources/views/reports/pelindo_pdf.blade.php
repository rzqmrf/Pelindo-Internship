<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi Magang - {{ $internUser->name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #000000;
            margin: 10px 15px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .header-table td {
            vertical-align: middle;
            padding: 0;
        }

        .logo-img {
            height: 48px;
            width: auto;
        }

        .report-title {
            font-size: 15px;
            font-weight: bold;
            color: #000000;
            text-align: right;
            margin: 0;
            letter-spacing: 0.2px;
        }

        .metadata-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 15px;
        }

        .metadata-table td {
            padding: 3px 0;
            vertical-align: top;
            font-size: 11px;
            color: #000000;
        }

        .metadata-label {
            font-weight: bold;
            width: 70px;
        }

        .metadata-colon {
            width: 15px;
            text-align: left;
        }

        .metadata-value {
            font-weight: bold;
        }

        .activity-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .activity-table thead {
            display: table-header-group;
        }

        .activity-table th {
            background-color: #0c6795; /* Pelindo teal-blue header color */
            color: #ffffff;
            font-size: 11px;
            font-weight: bold;
            padding: 8px 6px;
            border: 1.5px solid #000000;
            text-align: center;
        }

        .activity-table tr {
            page-break-inside: avoid;
        }

        .activity-table td {
            border: 1.5px solid #000000;
            padding: 8px 10px;
            font-size: 10.5px;
            vertical-align: middle;
            color: #000000;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .bullet-item {
            margin-bottom: 4px;
            padding-left: 12px;
            text-indent: -12px;
            line-height: 1.3;
        }

        .bullet-item:last-child {
            margin-bottom: 0;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td style="text-align: left;">
                <img src="{{ public_path('images/pelindo-logo-transparent.png') }}" class="logo-img" alt="PELINDO SOLUSI DIGITAL">
            </td>
            <td style="text-align: right;">
                <h1 class="report-title">Laporan Absensi Magang</h1>
            </td>
        </tr>
    </table>

    <!-- Metadata Section -->
    <table class="metadata-table">
        <tr>
            <td class="metadata-label">Nama</td>
            <td class="metadata-colon">:</td>
            <td class="metadata-value">{{ $internUser->name }}</td>
        </tr>
        <tr>
            <td class="metadata-label">Periode</td>
            <td class="metadata-colon">:</td>
            <td class="metadata-value">{{ $periodLabel }}</td>
        </tr>
        <tr>
            <td class="metadata-label">Divisi</td>
            <td class="metadata-colon">:</td>
            <td class="metadata-value">{{ $internProfile?->division?->name ?? 'PT ILCS Area Surabaya' }}</td>
        </tr>
    </table>

    <!-- Activity / Attendance Table -->
    <table class="activity-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Hari/Tanggal</th>
                <th style="width: 52%;">Kegiatan</th>
                <th style="width: 18%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportRows as $index => $row)
                <tr>
                    <td class="text-center" style="font-weight: bold;">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $row['date'] ? $row['date']->isoFormat('dddd, D MMMM YYYY') : '-' }}</td>
                    <td class="text-left">
                        @php
                            $lines = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $row['activity_description']))));
                        @endphp
                        @if(count($lines) > 0)
                            @foreach($lines as $line)
                                @if(!empty($line))
                                    @php
                                        $cleanLine = ltrim($line, "-*• \t\n\r\0\x0B");
                                    @endphp
                                    <div class="bullet-item">- {{ $cleanLine }}</div>
                                @endif
                            @endforeach
                        @else
                            &nbsp;
                        @endif
                    </td>
                    <td class="text-center">
                        @if(in_array(strtolower($row['work_location']), ['leave', 'izin', 'sakit']))
                            Izin
                        @else
                            Hadir ({{ strtoupper($row['work_location']) }})
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Signature Section -->
    <table style="width: 100%; border-collapse: collapse; margin-top: 40px; font-family: Arial, sans-serif; font-size: 11px; page-break-inside: avoid;">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%; text-align: center; padding-bottom: 15px;">
                Surabaya, {{ $generatedDate }}
            </td>
        </tr>
        <tr>
            <td style="text-align: center; vertical-align: top; width: 50%;">
                <div style="font-weight: bold; margin-bottom: 75px;">Peserta Magang</div>
                <div style="font-weight: bold;">{{ $internUser->name }}</div>
            </td>
            <td style="text-align: center; vertical-align: top; width: 50%;">
                <div style="font-weight: bold; margin-bottom: 75px;">Manajer Divisi</div>
                <div style="font-weight: bold;">{{ $internProfile?->division?->head_name ?? 'Mohammad Nasrul Rohman' }}</div>
            </td>
        </tr>
    </table>

</body>
</html>
