<?php

namespace App\Services;

use App\Models\Master\MasterKelas;
use App\Models\Master\MasterSiswa;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Pengelolaan anggota rombel. Riwayat absensi & jurnal tidak terpengaruh karena
 * setiap catatan menyimpan kelas_id-nya sendiri saat dicatat.
 */
class RombelService
{
    /**
     * Pindahkan siswa aktif ke rombel tujuan.
     *
     * @param  array<int|string>  $siswaIds
     * @return int jumlah siswa yang benar-benar pindah
     */
    public function pindahkan(array $siswaIds, int $tujuanId): int
    {
        $tujuan = MasterKelas::with('tahunAjaran')->find($tujuanId);
        if (! $tujuan || ! $tujuan->tahunAjaran?->is_active) {
            throw ValidationException::withMessages(['tujuan' => 'Rombel tujuan harus rombel pada tahun ajaran aktif.']);
        }

        $ids = collect($siswaIds)->map(fn ($id) => (int) $id)->filter()->unique();
        if ($ids->isEmpty()) {
            throw ValidationException::withMessages(['dipilih' => 'Pilih minimal satu siswa.']);
        }

        return DB::transaction(fn () => MasterSiswa::query()
            ->whereIn('id', $ids)
            ->where('status', 'aktif')
            ->where(fn ($q) => $q->whereNull('kelas_id')->orWhere('kelas_id', '!=', $tujuan->id))
            ->update(['kelas_id' => $tujuan->id, 'updated_at' => now()]));
    }
}
