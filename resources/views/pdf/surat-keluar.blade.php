<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keluar - {{ $surat->no_surat }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 2cm 2cm 2cm 2cm;
        }
        .kop-surat {
            text-align: center;
            border-bottom: 3px solid black;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .kop-surat h1, .kop-surat h2, .kop-surat h3, .kop-surat p {
            margin: 0;
            padding: 0;
        }
        .kop-surat h1 {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-surat h2 {
            font-size: 14pt;
            font-weight: bold;
        }
        .kop-surat p {
            font-size: 11pt;
        }
        table.meta-surat {
            width: 100%;
            margin-bottom: 20px;
        }
        table.meta-surat td {
            vertical-align: top;
        }
        .tujuan {
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .isi-surat {
            text-align: justify;
            margin-bottom: 40px;
        }
        .ttd-container {
            float: right;
            text-align: center;
            width: 300px;
        }
        .ttd-container p {
            margin: 0;
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
    <div class="kop-surat">
        <h2>PEMERINTAH PROVINSI JAWA BARAT</h2>
        <h2>DINAS PENDIDIKAN</h2>
        <h1>SMA NEGERI 1 CONTOH</h1>
        <p>Jl. Pendidikan No. 123, Kota Contoh, Jawa Barat 40000</p>
        <p>Telp: (022) 1234567 | Email: info@sman1contoh.sch.id | Website: sman1contoh.sch.id</p>
    </div>

    <table class="meta-surat">
        <tr>
            <td width="10%">Nomor</td>
            <td width="2%">:</td>
            <td width="50%">{{ $surat->no_surat ?? '(Belum Ada Nomor)' }}</td>
            <td width="38%" style="text-align: right;">{{ $surat->tanggal->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td>Sifat</td>
            <td>:</td>
            <td>Biasa</td>
            <td></td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>:</td>
            <td>-</td>
            <td></td>
        </tr>
        <tr>
            <td>Hal</td>
            <td>:</td>
            <td><strong>{{ $surat->perihal }}</strong></td>
            <td></td>
        </tr>
    </table>

    <div class="tujuan">
        <p>
            Yth. <strong>{{ $surat->tujuan }}</strong><br>
            di<br>
            Tempat
        </p>
    </div>

    <div class="isi-surat">
        <p>Dengan hormat,</p>
        <p>{!! nl2br(e($surat->isi_ringkas)) !!}</p>
        <p>Demikian surat ini kami sampaikan. Atas perhatian dan kerjasamanya kami ucapkan terima kasih.</p>
    </div>

    <div class="clearfix">
        <div class="ttd-container">
            <p>Kepala SMA Negeri 1 Contoh</p>
            <div class="ttd-space">
                <!-- Space for actual signature and stamp -->
            </div>
            <p><strong>{{ $surat->penyetuju->name ?? 'NAMA KEPALA SEKOLAH' }}</strong></p>
            <p>NIP. 19800101 200501 1 001</p>
        </div>
    </div>
</body>
</html>
