<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class MasterJadwalPelajaran extends Model
{
    protected $fillable = [
        'tahun_ajaran_id',
        'kelas_id',
        'mapel_id',
        'guru_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'jam_ke',
        'kategori_kegiatan',
        'keterangan_kegiatan',
    ];

    public function tahunAjaran()
    {
        return $this->belongsTo(MasterTahunAjaran::class, 'tahun_ajaran_id');
    }

    public function kelas()
    {
        return $this->belongsTo(MasterKelas::class, 'kelas_id');
    }

    public function mapel()
    {
        return $this->belongsTo(MasterMapel::class, 'mapel_id');
    }

    public function guru()
    {
        return $this->belongsTo(MasterGuru::class, 'guru_id');
    }

    /**
     * Get default jam mulai dan selesai berdasarkan hari dan jam ke (berdasarkan dokumen JADWAL PEMBELAJARAN.docx)
     */
    public static function getDefaultTime($hari, $jamKe)
    {
        $jamKe = (int) $jamKe;
        if ($jamKe < 1 || $jamKe > 10) return null;

        $hari = strtolower($hari);
        
        if ($hari === 'senin') {
            $times = [
                1 => ['07:45', '08:15'],
                2 => ['08:15', '08:45'], // Fix dari doc: 08.15-09.45 yg typo
                3 => ['08:45', '09:15'],
                4 => ['09:15', '09:45'],
                5 => ['10:15', '10:45'], // Lewati istirahat 09.45-10.15
                6 => ['10:45', '11:15'],
                7 => ['11:15', '11:45'],
                8 => ['13:00', '13:30'], // Lewati ishoma 11.45-13.00
                9 => ['13:30', '14:00'],
            ];
            return $times[$jamKe] ?? null;
        } else {
            // Selasa, Rabu, Kamis, Jumat
            $times = [
                1 => ['07:15', '07:45'],
                2 => ['07:45', '08:15'],
                3 => ['08:15', '08:45'],
                4 => ['08:45', '09:15'],
                5 => ['09:15', '09:45'],
                6 => ['10:15', '10:45'], // Lewati istirahat 09.45-10.15
                7 => ['10:45', '11:15'],
                8 => ['11:15', '11:45'],
                9 => ['13:00', '13:30'], // Lewati ishoma 11.45-13.00
                10 => ['13:30', '14:00'],
            ];
            return $times[$jamKe] ?? null;
        }
    }

    /**
     * Get jam_ke based on hari and jam_mulai
     */
    public static function getJamKeByTime($hari, $jamMulai)
    {
        for ($i = 1; $i <= 10; $i++) {
            $times = self::getDefaultTime($hari, $i);
            if ($times && $times[0] === substr($jamMulai, 0, 5)) {
                return $i;
            }
        }
        return null;
    }


    /**
     * Get all unique time slots across the week in order.
     */
    public static function getAllTimeSlots()
    {
        return [
            ['07:00', '07:45'],
            ['07:15', '07:45'],
            ['07:45', '08:15'],
            ['08:15', '08:45'],
            ['08:45', '09:15'],
            ['09:15', '09:45'],
            ['09:45', '10:15'], // Istirahat
            ['10:15', '10:45'],
            ['10:45', '11:15'],
            ['11:15', '11:45'],
            ['11:45', '13:00'], // Ishoma
            ['13:00', '13:30'],
            ['13:30', '14:00'],
        ];
    }
}
