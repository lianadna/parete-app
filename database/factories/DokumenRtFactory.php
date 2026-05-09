<?php

namespace Database\Factories;

use App\Models\DokumenRt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DokumenRt>
 */
class DokumenRtFactory extends Factory
{
    protected $model = DokumenRt::class;

    public function definition(): array
    {
        $tipe = fake()->randomElement(['pdf', 'doc', 'docx', 'xls', 'xlsx']);
        $kategori = fake()->randomElement(['formulir', 'surat', 'peraturan', 'data']);

        $nama = fake()->randomElement([
            'Formulir surat keterangan domisili',
            'Template pengajuan surat pengantar RT',
            'Peraturan RT 05 tahun berjalan',
            'Pengumuman rapat warga',
            'Data warga RT (rekapitulasi)',
            'Surat undangan kerja bakti',
            'Formulir iuran kas RT',
            'Berita acara rapat koordinasi',
            'Daftar calon ketua RT',
            'Laporan keuangan kas RT',
            'Surat pernyataan warga',
            'Jadwal ronda malam',
            'Panduan penggunaan fasilitas RT',
            'Rekap pengaduan triwulan',
            'Dokumen MOU dengan pihak ketiga',
        ]);

        $ukuran = fake()->numberBetween(12_000, 900_000);

        return [
            'nama_dokumen' => $nama,
            'tipe_berkas' => $tipe,
            'kategori' => $kategori,
            'ukuran_byte' => $ukuran,
            'jumlah_unduhan' => fake()->numberBetween(0, 40),
            'path_berkas' => 'dokumen_rt/contoh-'.fake()->uuid().'.'.$tipe,
            'akses' => fake()->randomElement(['semua_warga', 'admin_rt']),
        ];
    }
}
