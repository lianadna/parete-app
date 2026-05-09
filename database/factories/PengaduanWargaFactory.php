<?php

namespace Database\Factories;

use App\Models\DataWarga;
use App\Models\PengaduanWarga;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PengaduanWarga>
 */
class PengaduanWargaFactory extends Factory
{
    protected $model = PengaduanWarga::class;

    public function definition(): array
    {
        $daftar = DataWarga::all();
        $warga = $daftar->isNotEmpty()
            ? $daftar->random()
            : DataWarga::factory()->create();

        $topik = fake()->randomElement(['Infrastruktur', 'Kebersihan', 'Keamanan', 'Sosial', 'Lainnya']);
        $status = fake()->randomElement(['Terkirim', 'Diterima', 'Diproses', 'Selesai', 'Ditolak']);

        return [
            'nomor_pengaduan' => 'ADU-'.fake()->unique()->numerify('###'),
            'referensi_warga_id' => (string) $warga->getKey(),
            'nama_pelapor' => $warga->nama_kepala_keluarga,
            'judul_pengaduan' => fake()->randomElement([
                'Lampu jalan gang mati',
                'Sampah menumpuk di ujung gang',
                'Jalan berlubang depan rumah',
                'Kebisingan di malam hari',
                'Genangan air saat hujan',
                'Pohon menutupi saluran',
                'Fasilitas pos kamling rusak',
                'Kucing liar mengganggu',
                'Drainase tersumbat',
                'Parkir liar menghalangi jalan',
            ]),
            'topik' => $topik,
            'lokasi_kejadian' => fake()->randomElement(['Gang Mawar', 'Gang Melati', 'Blok A', 'Pos RT', 'Lapangan RT']),
            'deskripsi' => 'Mohon ditindaklanjuti sesuai prosedur RT. Kondisi sudah berlangsung beberapa hari dan mengganggu warga sekitar.',
            'status_pengaduan' => $status,
            'lampiran_gambar' => [],
            'catatan_selesai' => $status === 'Selesai' ? fake()->sentence() : null,
            'bukti_penyelesaian' => null,
            'alasan_ditolak' => $status === 'Ditolak' ? fake()->sentence() : null,
        ];
    }

    public function forWarga(DataWarga $warga): static
    {
        return $this->state(fn (array $attributes) => [
            'referensi_warga_id' => (string) $warga->getKey(),
            'nama_pelapor' => $warga->nama_kepala_keluarga,
        ]);
    }
}
