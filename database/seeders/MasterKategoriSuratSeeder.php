<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Master\MasterKategoriSurat;
use App\Enums\TipeKategoriSurat;

class MasterKategoriSuratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            // Surat Dinas (Keluar)
            [
                'kode' => 'DNS',
                'nama' => 'Surat Dinas',
                'tipe' => TipeKategoriSurat::KELUAR,
                'format_nomor' => 'DNS/{ROMAWI_BULAN}/{TAHUN}',
                'config' => null,
            ],
            // Surat Undangan (Masuk & Keluar)
            [
                'kode' => 'UND',
                'nama' => 'Surat Undangan',
                'tipe' => TipeKategoriSurat::KEDUANYA,
                'format_nomor' => 'UND/{ROMAWI_BULAN}/{TAHUN}',
                'config' => [
                    ['name' => 'tanggal_kegiatan', 'type' => 'date', 'label' => 'Tanggal Kegiatan', 'required' => true],
                    ['name' => 'waktu_kegiatan', 'type' => 'time', 'label' => 'Waktu Kegiatan', 'required' => true],
                    ['name' => 'tempat_kegiatan', 'type' => 'text', 'label' => 'Tempat Kegiatan', 'required' => true],
                ],
            ],
            // Surat Pemberitahuan (Masuk & Keluar)
            [
                'kode' => 'PMB',
                'nama' => 'Surat Pemberitahuan',
                'tipe' => TipeKategoriSurat::KEDUANYA,
                'format_nomor' => 'PMB/{ROMAWI_BULAN}/{TAHUN}',
                'config' => null,
            ],
            // Surat Edaran (Masuk & Keluar)
            [
                'kode' => 'EDR',
                'nama' => 'Surat Edaran',
                'tipe' => TipeKategoriSurat::KEDUANYA,
                'format_nomor' => 'EDR/{ROMAWI_BULAN}/{TAHUN}',
                'config' => null,
            ],
            // Surat Tugas (Masuk & Keluar)
            [
                'kode' => 'TGS',
                'nama' => 'Surat Tugas',
                'tipe' => TipeKategoriSurat::KEDUANYA,
                'format_nomor' => 'TGS/{ROMAWI_BULAN}/{TAHUN}',
                'config' => [
                    ['name' => 'nama_petugas', 'type' => 'text', 'label' => 'Nama Petugas', 'required' => true],
                    ['name' => 'tujuan_tugas', 'type' => 'text', 'label' => 'Tujuan Tugas', 'required' => true],
                    ['name' => 'tanggal_mulai', 'type' => 'date', 'label' => 'Tanggal Mulai', 'required' => true],
                    ['name' => 'tanggal_selesai', 'type' => 'date', 'label' => 'Tanggal Selesai', 'required' => true],
                ],
            ],
            // Surat Keputusan (SK) (Masuk & Keluar)
            [
                'kode' => 'SK',
                'nama' => 'Surat Keputusan (SK)',
                'tipe' => TipeKategoriSurat::KEDUANYA,
                'format_nomor' => 'SK/{ROMAWI_BULAN}/{TAHUN}',
                'config' => [
                    ['name' => 'nomor_sk', 'type' => 'text', 'label' => 'Nomor SK (Jika Ada)', 'required' => false],
                    ['name' => 'tentang', 'type' => 'text', 'label' => 'Tentang', 'required' => true],
                ],
            ],
            // Surat Keterangan (Masuk & Keluar)
            [
                'kode' => 'KET',
                'nama' => 'Surat Keterangan',
                'tipe' => TipeKategoriSurat::KEDUANYA,
                'format_nomor' => 'KET/{ROMAWI_BULAN}/{TAHUN}',
                'config' => [
                    ['name' => 'nama_terkait', 'type' => 'text', 'label' => 'Nama Pihak Terkait', 'required' => true],
                    ['name' => 'keperluan', 'type' => 'text', 'label' => 'Keperluan', 'required' => true],
                ],
            ],
            // Surat Permohonan (Masuk & Keluar)
            [
                'kode' => 'MOHON',
                'nama' => 'Surat Permohonan',
                'tipe' => TipeKategoriSurat::KEDUANYA,
                'format_nomor' => 'MOHON/{ROMAWI_BULAN}/{TAHUN}',
                'config' => [
                    ['name' => 'jenis_permohonan', 'type' => 'text', 'label' => 'Jenis Permohonan', 'required' => true],
                ],
            ],
            // Surat Izin (Keluar)
            [
                'kode' => 'IZN',
                'nama' => 'Surat Izin',
                'tipe' => TipeKategoriSurat::KELUAR,
                'format_nomor' => 'IZN/{ROMAWI_BULAN}/{TAHUN}',
                'config' => [
                    ['name' => 'nama_pemohon', 'type' => 'text', 'label' => 'Nama Pemohon', 'required' => true],
                    ['name' => 'alasan_izin', 'type' => 'text', 'label' => 'Alasan Izin', 'required' => true],
                    ['name' => 'tanggal_izin', 'type' => 'date', 'label' => 'Tanggal Izin', 'required' => true],
                ],
            ],
            // Surat Rekomendasi (Masuk & Keluar)
            [
                'kode' => 'REK',
                'nama' => 'Surat Rekomendasi',
                'tipe' => TipeKategoriSurat::KEDUANYA,
                'format_nomor' => 'REK/{ROMAWI_BULAN}/{TAHUN}',
                'config' => [
                    ['name' => 'direkomendasikan_untuk', 'type' => 'text', 'label' => 'Direkomendasikan Untuk (Nama)', 'required' => true],
                    ['name' => 'tujuan_rekomendasi', 'type' => 'text', 'label' => 'Tujuan Rekomendasi', 'required' => true],
                ],
            ],
            // Surat Dispensasi (Keluar)
            [
                'kode' => 'DIS',
                'nama' => 'Surat Dispensasi',
                'tipe' => TipeKategoriSurat::KELUAR,
                'format_nomor' => 'DIS/{ROMAWI_BULAN}/{TAHUN}',
                'config' => [
                    ['name' => 'nama_siswa', 'type' => 'text', 'label' => 'Nama Siswa/Pegawai', 'required' => true],
                    ['name' => 'alasan_dispensasi', 'type' => 'text', 'label' => 'Alasan Dispensasi', 'required' => true],
                ],
            ],
            // Surat Perintah (Masuk & Keluar) - Note: Instruksi (Masuk) is basically Perintah
            [
                'kode' => 'PRT',
                'nama' => 'Surat Perintah / Instruksi',
                'tipe' => TipeKategoriSurat::KEDUANYA,
                'format_nomor' => 'PRT/{ROMAWI_BULAN}/{TAHUN}',
                'config' => null,
            ],
            // Surat Pengantar (Masuk & Keluar)
            [
                'kode' => 'PNT',
                'nama' => 'Surat Pengantar',
                'tipe' => TipeKategoriSurat::KEDUANYA,
                'format_nomor' => 'PNT/{ROMAWI_BULAN}/{TAHUN}',
                'config' => [
                    ['name' => 'dokumen_terlampir', 'type' => 'text', 'label' => 'Dokumen Terlampir', 'required' => true],
                    ['name' => 'jumlah_lampiran', 'type' => 'number', 'label' => 'Jumlah Lampiran', 'required' => true],
                ],
            ],
            // Surat Perjanjian atau MoU (Masuk & Keluar)
            [
                'kode' => 'MOU',
                'nama' => 'Surat Perjanjian atau MoU / Dokumen Resmi',
                'tipe' => TipeKategoriSurat::KEDUANYA,
                'format_nomor' => 'MOU/{ROMAWI_BULAN}/{TAHUN}',
                'config' => [
                    ['name' => 'pihak_pertama', 'type' => 'text', 'label' => 'Pihak Pertama', 'required' => true],
                    ['name' => 'pihak_kedua', 'type' => 'text', 'label' => 'Pihak Kedua', 'required' => true],
                    ['name' => 'masa_berlaku', 'type' => 'text', 'label' => 'Masa Berlaku', 'required' => false],
                ],
            ],
            // Surat Pernyataan (Keluar)
            [
                'kode' => 'NYT',
                'nama' => 'Surat Pernyataan',
                'tipe' => TipeKategoriSurat::KELUAR,
                'format_nomor' => 'NYT/{ROMAWI_BULAN}/{TAHUN}',
                'config' => null,
            ],
            // Surat Teguran atau Peringatan (SP) (Keluar)
            [
                'kode' => 'SP',
                'nama' => 'Surat Teguran / Peringatan (SP)',
                'tipe' => TipeKategoriSurat::KELUAR,
                'format_nomor' => 'SP/{ROMAWI_BULAN}/{TAHUN}',
                'config' => [
                    ['name' => 'nama_pelanggar', 'type' => 'text', 'label' => 'Nama Penerima SP', 'required' => true],
                    ['name' => 'tingkat_sp', 'type' => 'text', 'label' => 'Tingkat SP (Misal: SP 1)', 'required' => true],
                ],
            ],
            // Surat Pengumuman (Masuk & Keluar)
            [
                'kode' => 'PGM',
                'nama' => 'Surat Pengumuman',
                'tipe' => TipeKategoriSurat::KEDUANYA,
                'format_nomor' => 'PGM/{ROMAWI_BULAN}/{TAHUN}',
                'config' => null,
            ],
            // Surat Mutasi (Keluar)
            [
                'kode' => 'MTS',
                'nama' => 'Surat Mutasi',
                'tipe' => TipeKategoriSurat::KELUAR,
                'format_nomor' => 'MTS/{ROMAWI_BULAN}/{TAHUN}',
                'config' => [
                    ['name' => 'nama_mutasi', 'type' => 'text', 'label' => 'Nama Yang Dimutasi', 'required' => true],
                    ['name' => 'instansi_tujuan', 'type' => 'text', 'label' => 'Instansi/Sekolah Tujuan', 'required' => true],
                ],
            ],
            // Surat Permintaan Data (Masuk)
            [
                'kode' => 'REQ',
                'nama' => 'Surat Permintaan Data',
                'tipe' => TipeKategoriSurat::MASUK,
                'format_nomor' => 'REQ/{ROMAWI_BULAN}/{TAHUN}',
                'config' => [
                    ['name' => 'data_diminta', 'type' => 'text', 'label' => 'Data Yang Diminta', 'required' => true],
                    ['name' => 'batas_waktu', 'type' => 'date', 'label' => 'Batas Waktu Penyerahan', 'required' => true],
                ],
            ],
            // Surat Kerja Sama (Masuk)
            [
                'kode' => 'KSM',
                'nama' => 'Surat Kerja Sama / Penawaran',
                'tipe' => TipeKategoriSurat::MASUK,
                'format_nomor' => 'KSM/{ROMAWI_BULAN}/{TAHUN}',
                'config' => [
                    ['name' => 'bidang_kerjasama', 'type' => 'text', 'label' => 'Bidang Kerjasama/Penawaran', 'required' => true],
                ],
            ],
            // Surat Panggilan (Masuk)
            [
                'kode' => 'PGL',
                'nama' => 'Surat Panggilan',
                'tipe' => TipeKategoriSurat::MASUK,
                'format_nomor' => 'PGL/{ROMAWI_BULAN}/{TAHUN}',
                'config' => [
                    ['name' => 'pihak_dipanggil', 'type' => 'text', 'label' => 'Pihak Yang Dipanggil', 'required' => true],
                    ['name' => 'waktu_panggilan', 'type' => 'date', 'label' => 'Waktu Panggilan', 'required' => true],
                ],
            ],
            // Surat Himbauan (Masuk)
            [
                'kode' => 'HIM',
                'nama' => 'Surat Himbauan',
                'tipe' => TipeKategoriSurat::MASUK,
                'format_nomor' => 'HIM/{ROMAWI_BULAN}/{TAHUN}',
                'config' => null,
            ],
            // Surat Pengaduan (Masuk)
            [
                'kode' => 'ADU',
                'nama' => 'Surat Pengaduan',
                'tipe' => TipeKategoriSurat::MASUK,
                'format_nomor' => 'ADU/{ROMAWI_BULAN}/{TAHUN}',
                'config' => [
                    ['name' => 'nama_pengadu', 'type' => 'text', 'label' => 'Nama Pengadu', 'required' => true],
                    ['name' => 'hal_diadukan', 'type' => 'text', 'label' => 'Hal Yang Diadukan', 'required' => true],
                ],
            ],
            // Surat Laporan (Masuk)
            [
                'kode' => 'LAP',
                'nama' => 'Surat Laporan',
                'tipe' => TipeKategoriSurat::MASUK,
                'format_nomor' => 'LAP/{ROMAWI_BULAN}/{TAHUN}',
                'config' => null,
            ],
            // Surat Balasan (Masuk)
            [
                'kode' => 'BLS',
                'nama' => 'Surat Balasan',
                'tipe' => TipeKategoriSurat::MASUK,
                'format_nomor' => 'BLS/{ROMAWI_BULAN}/{TAHUN}',
                'config' => null,
            ],
        ];

        foreach ($kategoris as $kategoriData) {
            MasterKategoriSurat::updateOrCreate(
                ['kode' => $kategoriData['kode']],
                [
                    'nama' => $kategoriData['nama'],
                    'tipe' => $kategoriData['tipe'],
                    'format_nomor' => $kategoriData['format_nomor'],
                    'config' => $kategoriData['config'],
                ]
            );
        }
    }
}
