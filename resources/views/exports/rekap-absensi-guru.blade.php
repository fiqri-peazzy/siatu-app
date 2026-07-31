<table>
    <thead>
        <!-- KOP SURAT -->
        <tr>
            <th colspan="9" style="text-align: center; font-size: 16px; font-weight: bold;">SMA MUHAMMADIYAH GORONTALO</th>
        </tr>
        <tr>
            <th colspan="9" style="text-align: center; font-size: 14px; font-weight: bold;">ISLAMIC GLOBAL SCHOOL</th>
        </tr>
        <tr>
            <th colspan="9" style="text-align: center; font-size: 10px;">Jln. Taman Bunga, Kota Timur, Kota Gorontalo | Email: smasmuhammadiyahkotagorontalo@gmail.com</th>
        </tr>
        <tr>
            <th colspan="9"></th>
        </tr>
        <tr>
            <th colspan="9" style="text-align: center; font-size: 12px; font-weight: bold;">REKAP ABSENSI GURU</th>
        </tr>
        <tr>
            <th colspan="2">Tanggal Cetak</th>
            <th colspan="7">: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</th>
        </tr>
        <tr>
            <th colspan="2">Rentang Tanggal</th>
            <th colspan="7">: {{ $start_date ? \Carbon\Carbon::parse($start_date)->format('d/m/Y') : '-' }} s/d {{ $end_date ? \Carbon\Carbon::parse($end_date)->format('d/m/Y') : '-' }}</th>
        </tr>
        <tr>
            <th colspan="9"></th>
        </tr>

        <!-- Table Headers -->
        <tr>
            <th style="text-align: center; font-weight: bold; width: 50px;">No</th>
            <th style="text-align: center; font-weight: bold; width: 250px;">Nama Guru</th>
            <th style="text-align: center; font-weight: bold; width: 150px;">NIP</th>
            <th style="text-align: center; font-weight: bold; width: 150px;">Jabatan</th>
            <th style="text-align: center; font-weight: bold; width: 100px;">Total Hadir</th>
            <th style="text-align: center; font-weight: bold; width: 100px;">Total Terlambat</th>
            <th style="text-align: center; font-weight: bold; width: 100px;">Total Izin</th>
            <th style="text-align: center; font-weight: bold; width: 100px;">Total Sakit</th>
            <th style="text-align: center; font-weight: bold; width: 100px;">Total Alfa</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $index => $guru)
        @php
            $absensisMasuk = $guru->absensis->where('jenis', 'masuk');
            $hadir = $absensisMasuk->where('status', 'hadir')->count();
            $terlambat = $absensisMasuk->where('status', 'terlambat')->count();
            $izin = $absensisMasuk->where('status', 'izin')->count();
            $sakit = $absensisMasuk->where('status', 'sakit')->count();
            $alfa = $absensisMasuk->where('status', 'alfa')->count();
        @endphp
        <tr>
            <td style="text-align: center;">{{ $index + 1 }}</td>
            <td>{{ $guru->nama }}</td>
            <td style="text-align: center;">{{ $guru->nip ? "'" . $guru->nip : '-' }}</td>
            <td>{{ $guru->jabatan ?? '-' }}</td>
            <td style="text-align: center;">{{ $hadir }}</td>
            <td style="text-align: center;">{{ $terlambat }}</td>
            <td style="text-align: center;">{{ $izin }}</td>
            <td style="text-align: center;">{{ $sakit }}</td>
            <td style="text-align: center;">{{ $alfa }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="9" style="text-align: center;">Tidak ada data pada rentang tanggal ini.</td>
        </tr>
        @endforelse
    </tbody>
</table>
