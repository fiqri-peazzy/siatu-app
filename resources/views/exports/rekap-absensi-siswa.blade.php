<table>
    <thead>
        {{-- Kop resmi (4 baris) --}}
        @include('exports.partials.kop', ['kolom' => $isSemuaMapel ? 6 : (count($kolomTanggal) + 6)])
        <tr>
            <th colspan="{{ $isSemuaMapel ? 6 : (count($kolomTanggal) + 6) }}" style="text-align: center; font-size: 12px; font-weight: bold;">REKAPITULASI KEHADIRAN SISWA</th>
        </tr>
        <tr>
            <th colspan="2">Tahun Ajaran</th>
            <th colspan="{{ $isSemuaMapel ? 4 : (count($kolomTanggal) + 4) }}">: {{ $ta->nama }} ({{ $ta->semester }})</th>
        </tr>
        <tr>
            <th colspan="2">Kelas</th>
            <th colspan="{{ $isSemuaMapel ? 4 : (count($kolomTanggal) + 4) }}">: {{ $kelas->nama }}</th>
        </tr>
        <tr>
            <th colspan="2">Mata Pelajaran</th>
            <th colspan="{{ $isSemuaMapel ? 4 : (count($kolomTanggal) + 4) }}">: {{ $mapelName }}</th>
        </tr>
        <tr>
            <th colspan="2">Rentang Waktu</th>
            <th colspan="{{ $isSemuaMapel ? 4 : (count($kolomTanggal) + 4) }}">: {{ $waktuLabel }}</th>
        </tr>
        <tr>
            <th colspan="{{ $isSemuaMapel ? 6 : (count($kolomTanggal) + 6) }}"></th>
        </tr>

        <!-- Table Headers -->
        <tr>
            <th rowspan="2" style="text-align: center; vertical-align: middle; border: 1px solid #000; font-weight: bold; background-color: #f3f4f6;">No</th>
            <th rowspan="2" style="text-align: center; vertical-align: middle; border: 1px solid #000; font-weight: bold; background-color: #f3f4f6; width: 30px;">Nama Siswa</th>
            
            @if(!$isSemuaMapel && count($kolomTanggal) > 0)
                <th colspan="{{ count($kolomTanggal) }}" style="text-align: center; border: 1px solid #000; font-weight: bold; background-color: #f3f4f6;">Detail Pertemuan (Tanggal)</th>
            @endif

            <th colspan="4" style="text-align: center; border: 1px solid #000; font-weight: bold; background-color: #f3f4f6;">Total Rekap</th>
        </tr>
        <tr>
            @if(!$isSemuaMapel && count($kolomTanggal) > 0)
                @foreach($kolomTanggal as $tgl)
                    <th style="text-align: center; border: 1px solid #000; font-weight: bold; background-color: #f3f4f6;">{{ \Carbon\Carbon::parse($tgl)->format('d/m') }}</th>
                @endforeach
            @endif
            <th style="text-align: center; border: 1px solid #000; font-weight: bold; background-color: #f3f4f6; color: green;">H</th>
            <th style="text-align: center; border: 1px solid #000; font-weight: bold; background-color: #f3f4f6; color: orange;">S</th>
            <th style="text-align: center; border: 1px solid #000; font-weight: bold; background-color: #f3f4f6; color: blue;">I</th>
            <th style="text-align: center; border: 1px solid #000; font-weight: bold; background-color: #f3f4f6; color: red;">A</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach($rekapData as $id => $data)
        <tr>
            <td style="text-align: center; border: 1px solid #000;">{{ $no++ }}</td>
            <td style="border: 1px solid #000;">{{ $data['siswa']->nama }}</td>
            
            @if(!$isSemuaMapel && count($kolomTanggal) > 0)
                @foreach($kolomTanggal as $tgl)
                    @php
                        $status = $data['per_tanggal'][$tgl] ?? '-';
                        $color = '#000000';
                        if($status == 'hadir') $color = 'green';
                        if($status == 'sakit') $color = 'orange';
                        if($status == 'izin') $color = 'blue';
                        if($status == 'alfa') $color = 'red';
                    @endphp
                    <td style="text-align: center; border: 1px solid #000; color: {{ $color }};">
                        {{ $status == '-' ? '-' : strtoupper(substr($status, 0, 1)) }}
                    </td>
                @endforeach
            @endif

            <td style="text-align: center; border: 1px solid #000;">{{ $data['stats']['hadir'] }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ $data['stats']['sakit'] }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ $data['stats']['izin'] }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ $data['stats']['alfa'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
