<?php

namespace Database\Factories;

use App\Models\InformasiRt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InformasiRt>
 */
class InformasiRtFactory extends Factory
{
    protected $model = InformasiRt::class;

    public function definition(): array
    {
        $jenis = fake()->randomElement(['pengumuman', 'kegiatan']);

        return [
            'jenis_informasi' => $jenis,
            'judul_informasi' => fake()->randomElement([
                'Jadwal rapat bulanan RT 05',
                'Kerja bakti massal lingkungan',
                'Pengumuman pemutihan PBB',
                'Waspada demam berdarah di musim hujan',
                'Pemilihan ketua RT periode baru',
                'Pengajian rutinan minggu kedua',
                'Distribusi sembako untuk lansia',
                'Lomba 17 Agustus tingkat RT',
                'Sosialisasi program kelurahan',
                'Pemeliharaan jalan dan drainase',
                'Pendaftaran kartu keluarga sehat',
                'Gotong royong perbaikan pos ronda',
                'Rapat koordinasi keamanan',
                'Penggalangan dana bencana',
                'Kunjungan tim kesehatan puskesmas',
            ]),
            'isi_informasi' => 'Diumumkan kepada seluruh warga RT 05 Malabar Ujung bahwa kegiatan ini wajib diperhatikan. '
                .'Detail teknis dan jadwal akan disampaikan melalui pengurus RT. '
                .'Kehadiran atau partisipasi sangat diharapkan demi kelancaran program kemasyarakatan.',
            'tanggal_publikasi' => fake()->dateTimeBetween('-3 months', 'now'),
            'tanggal_kegiatan' => $jenis === 'kegiatan' ? fake()->dateTimeBetween('now', '+2 months') : null,
            'penulis' => fake()->randomElement(['Admin RT', 'Sekretaris RT', 'Ketua RT']),
        ];
    }
}
