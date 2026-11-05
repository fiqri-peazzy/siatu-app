<table>
    <thead>
        <!-- KOP SURAT -->
        <tr>
            <th colspan="11" style="text-align: center; font-size: 16px; font-weight: bold;">SMA MUHAMMADIYAH GORONTALO</th>
        </tr>
        <tr>
            <th colspan="11" style="text-align: center; font-size: 14px; font-weight: bold;">ISLAMIC GLOBAL SCHOOL</th>
        </tr>
        <tr>
            <th colspan="11" style="text-align: center; font-size: 10px;">Jln. Taman Bunga, Kota Timur, Kota Gorontalo | Email: smasmuhammadiyahkotagorontalo@gmail.com</th>
        </tr>
        <tr>
            <th colspan="11"></th>
        </tr>
        <tr>
            <th colspan="11" style="text-align: center; font-size: 12px; font-weight: bold;">REKAP JURNAL MENGAJAR GURU</th>
        </tr>
        <tr>
            <th colspan="2">Tanggal Cetak</th>
            <th colspan="9">: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</th>
        </tr>
        <tr>
            <th colspan="2">Rentang Tanggal</th>
            <th colspan="9">: {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}</th>
        </tr>
        <tr>
            <th colspan="11"></th>
        </tr>

        <!-- Table Headers -->
        <tr>
            <th style="text-align: center; font-weight: bold; width: 50px;">No</th>
            <th style="text-align: center; font-weight: bold; width: 250px;">Pertemuan / Hari Tgl</th>
            <th style="text-align: center; font-weight: bold; width: 150px;">Nama Guru</th>
            <th style="text-align: center; font-weight: bold; width: 150px;">Mapel</th>
            <th style="text-align: center; font-weight: bold; width: 100px;">Rombel</th>
            <th style="text-align: center; font-weight: bold; width: 200px;">KD / CP &amp; Materi</th>
            <th style="text-align: center; font-weight: bold; width: 200px;">Kegiatan / Tugas</th>
            <th style="text-align: center; font-weight: bold; width: 40px; color: green;">H</th>
            <th style="text-align: center; font-weight: bold; width: 40px; color: blue;">I</th>
            <th style="text-align: center; font-weight: bold; width: 40px; color: red;">A</th>
            <th style="text-align: center; font-weight: bold; width: 150px;">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($jurnals as $index => $jurnal)
        @php
            $jamString = $jurnal->jam_mulai == $jurnal->jam_selesai ? $jurnal->jam_mulai : $jurnal->jam_mulai . '-' . $jurnal->jam_selesai;
            $hariTgl = "Jam ke-" . $jamString . " / " . \Carbon\Carbon::parse($jurnal->tanggal)->translatedFormat('l, d-m-Y');
            
            $ket = [];
            if ($jurnal->jumlah_hadir > 0) $ket[] = "Hadir " . $jurnal->jumlah_hadir;
            if ($jurnal->jumlah_sakit > 0) $ket[] = "Sakit " . $jurnal->jumlah_sakit;
            if ($jurnal->jumlah_izin > 0) $ket[] = "Izin " . $jurnal->jumlah_izin;
            if ($jurnal->jumlah_alfa > 0) $ket[] = "Alfa " . $jurnal->jumlah_alfa;
            $keteranganString = implode(', ', $ket);
        @endphp
        <tr>
            <td style="text-align: center;">{{ $index + 1 }}</td>
            <td>{{ $hariTgl }}</td>
            <td>{{ $jurnal->guru->nama ?? '-' }}</td>
            <td>{{ $jurnal->mapel->nama ?? '-' }}</td>
            <td style="text-align: center;">{{ $jurnal->kelas->nama ?? '-' }}</td>
            <td>
                @if($jurnal->kd_cp)
                    <b>KD/CP:</b> {{ $jurnal->kd_cp }}<br>
                @endif
                <b>Materi:</b> {{ $jurnal->materi ?: '-' }}
            </td>
            <td>{{ $jurnal->kegiatan ?: '-' }}</td>
            <td style="text-align: center;">{{ $jurnal->jumlah_hadir > 0 ? $jurnal->jumlah_hadir : '' }}</td>
            <td style="text-align: center;">{{ $jurnal->jumlah_izin > 0 ? $jurnal->jumlah_izin : '' }}</td>
            <td style="text-align: center;">{{ $jurnal->jumlah_alfa > 0 ? $jurnal->jumlah_alfa : '' }}</td>
            <td>{{ $keteranganString }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="11" style="text-align: center;">Tidak ada data jurnal pada rentang tanggal ini.</td>
        </tr>
        @endforelse
    </tbody>
</table>
