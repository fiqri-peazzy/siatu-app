<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Jurnal Mengajar</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .logo { width: 70px; position: absolute; left: 0; top: 0; }
        .school-name { font-size: 16px; font-weight: bold; margin: 0; }
        .school-sub { font-size: 14px; font-weight: bold; margin: 5px 0; }
        .school-address { font-size: 10px; margin: 0; }
        .title { text-align: center; font-size: 14px; font-weight: bold; margin-bottom: 20px; text-transform: uppercase; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 3px; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 5px; vertical-align: top; }
        .data-table th { background-color: #f2f2f2; text-align: center; }
        .footer { width: 100%; margin-top: 50px; }
        .signature { float: right; width: 300px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        @if(file_exists(public_path('images/logo-removebg-preview.png')))
            <img src="{{ public_path('images/logo-removebg-preview.png') }}" class="logo">
        @endif
        <h1 class="school-name">SMA MUHAMMADIYAH GORONTALO</h1>
        <h2 class="school-sub">ISLAMIC GLOBAL SCHOOL</h2>
        <p class="school-address">Jln. Taman Bunga, Kota Timur, Kota Gorontalo, Gorontalo 96135</p>
    </div>

    <div class="title">JURNAL MENGAJAR GURU</div>

    <table class="info-table">
        <tr>
            <td width="15%">Nama Guru</td>
            <td width="2%">:</td>
            <td width="40%">{{ $guru->nama ?? '-' }}</td>
            <td width="15%">Bulan/Tahun</td>
            <td width="2%">:</td>
            <td width="26%">{{ \Carbon\Carbon::create()->month((int)$bulan)->locale('id')->isoFormat('MMMM') }} {{ $tahun }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="8%">Jam Ke</th>
                <th width="15%">Kelas / Mapel</th>
                <th width="30%">Materi</th>
                <th width="30%">Kegiatan / Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jurnals as $index => $jurnal)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d/m/Y') }}</td>
                <td style="text-align: center;">{{ $jurnal->jam_ke }}</td>
                <td>
                    {{ $jurnal->kelas->nama ?? '-' }} <br>
                    <small>{{ $jurnal->mapel->nama ?? '-' }}</small>
                </td>
                <td>{{ $jurnal->materi }}</td>
                <td>{{ $jurnal->kegiatan }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">Tidak ada jurnal pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <p>Gorontalo, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY') }}</p>
            <p>Guru Mata Pelajaran,</p>
            <br><br><br>
            <p style="font-weight: bold; text-decoration: underline;">{{ $guru->nama ?? '_______________________' }}</p>
            <p>NBM. {{ $guru->nbm ?? '-' }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>
</body>
</html>
