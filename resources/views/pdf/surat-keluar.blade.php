<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keluar - {{ $surat->no_surat }}</title>
    <style>
        @page {
            margin: 0cm 0cm 2cm 0cm; /* Top margin 0 to touch the top */
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
        }
        .content-padding {
            padding: 0cm 2.5cm 0cm 2.5cm;
        }
        .header-wrapper {
            position: relative;
            width: 100%;
            padding-top: 1cm;
        }
        .header-text-area {
            margin-left: 4.5cm;
            padding-right: 2.5cm;
        }
        .title-1 {
            font-family: Arial, sans-serif;
            font-size: 24pt;
            font-weight: bold;
            color: #002060;
            margin: 0;
            line-height: 1;
            letter-spacing: 0.5px;
        }
        .title-2 {
            font-family: Arial, sans-serif;
            font-size: 13pt;
            font-weight: bold;
            color: #b8860b;
            margin: 5px 0 5px 0;
            letter-spacing: 4px;
        }
        .title-divider {
            border-bottom: 1px solid #d3d3d3;
            width: 100%;
            margin-bottom: 5px;
            position: relative;
        }
        .title-divider-star {
            position: absolute;
            top: -6px;
            left: 50%;
            background: white;
            padding: 0 5px;
            color: #b8860b;
            font-size: 10px;
        }
        .contact-info {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            color: #333;
            width: 100%;
            border-collapse: collapse;
        }
        .contact-info td {
            vertical-align: top;
        }
        .icon-col {
            width: 20px;
            color: #002060;
            font-size: 12pt;
            text-align: left;
        }
        .lines-container {
            margin-top: 15px;
            width: 100%;
        }
        .line-blue {
            border-top: 5px solid #002060;
        }
        .line-gold {
            border-top: 2px solid #b8860b;
            margin-top: 2px;
        }
        .logo-img {
            position: absolute;
            top: 0.5cm;
            left: 1.5cm;
            width: 3.5cm;
            z-index: 10;
        }
        table.meta-surat {
            width: 100%;
            margin-top: 25px;
            margin-bottom: 25px;
            border-collapse: collapse;
        }
        table.meta-surat td {
            vertical-align: top;
            padding: 2px 0;
        }
        .tujuan {
            margin-bottom: 25px;
            line-height: 1.3;
        }
        .isi-surat {
            text-align: justify;
            margin-bottom: 40px;
        }
        .isi-surat p {
            margin-top: 0;
            margin-bottom: 10px;
        }
        .ttd-container {
            width: 300px;
            float: right;
            text-align: center;
        }
        .ttd-container p {
            margin: 0;
            line-height: 1.3;
        }
        .ttd-space {
            height: 80px;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="header-wrapper">
        @php
            $logoPath = public_path('images/logo-removebg-preview.png');
            if (file_exists($logoPath)) {
                $logoData = base64_encode(file_get_contents($logoPath));
                echo '<img src="data:image/png;base64,' . $logoData . '" class="logo-img">';
            }
        @endphp

        <div class="header-text-area">
            <div class="title-1">SMA MUHAMMADIYAH GORONTALO</div>
            <div class="title-2">
                ISLAMIC GLOBAL SCHOOL
            </div>
            <div class="title-divider">
                <span class="title-divider-star">✶</span>
            </div>
            <table class="contact-info">
                <tr>
                    <td class="icon-col">📍</td>
                    <td width="55%">
                        Jln. Taman Bunga, Kota Timur,<br>
                        Kota Gorontalo, Gorontalo 96135
                    </td>
                    <td class="icon-col">✉</td>
                    <td width="40%">
                        Email.<br>
                        smasmuhammadiyahkotagorontalo@gmail.com
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="lines-container">
            <div class="line-blue"></div>
            <div class="line-gold"></div>
        </div>
    </div>

    <!-- BODY -->
    <div class="content-padding">
        <table class="meta-surat">
            <tr>
                <td width="12%">Nomor</td>
                <td width="3%">:</td>
                <td width="45%">{{ $surat->no_surat ?? '(Belum Ada Nomor)' }}</td>
                <td width="40%" style="text-align: right;">
                    Gorontalo, {{ $surat->tanggal->translatedFormat('d F Y') }}
                </td>
            </tr>
            <tr>
                <td>Lamp</td>
                <td>:</td>
                <td>-</td>
                <td></td>
            </tr>
            <tr>
                <td>Hal</td>
                <td>:</td>
                <td>{{ $surat->perihal }}</td>
                <td></td>
            </tr>
        </table>

        <div class="tujuan">
            Kepada Yth.<br>
            {{ $surat->tujuan }}<br>
            di<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tempat
        </div>

        <div class="isi-surat">
            {!! $surat->isi_ringkas !!}
        </div>

        <div class="clearfix">
            <div class="ttd-container">
                <p>Mengetahui,<br>Kepala SMAMGo</p>
                <div class="ttd-space">
                    <!-- Space for actual signature and stamp -->
                </div>
                <p><u><strong>{{ $surat->disetujuiOleh->name ?? 'NAMA KEPALA SEKOLAH' }}</strong></u></p>
                <p>NBM. 1478021</p>
            </div>
        </div>
    </div>
</body>
</html>
