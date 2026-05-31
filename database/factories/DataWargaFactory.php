<?php

namespace Database\Factories;

use App\Models\DataWarga;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<DataWarga>
 */
class DataWargaFactory extends Factory
{
    protected $model = DataWarga::class;

    public function definition(): array
    {
        $nama = fake()->randomElement([
            'Budi Santoso', 'Siti Rahayuni', 'Ahmad Fauzan', 'Dewi Kusumawati',
            'Yulian Adiprana', 'Rina Wulandari', 'Hendra Gunawan', 'Maya Putri',
            'Agus Salim', 'Fitri Handayani', 'Eko Prasetyo', 'Lestari Wijaya',
            'Joko Widodo', 'Ratna Sari', 'Bayu Permana',
        ]);

        $noRumah = (string) fake()->unique()->numberBetween(1, 120);

        return [
            'id_keluarga' => 'RT05-'.now()->format('Y').'-'.fake()->unique()->numerify('###'),
            'nama_kepala_keluarga' => $nama,
            'nomor_rumah' => $noRumah,
            'alamat_lengkap' => 'Gang '.fake()->randomElement(['Mawar', 'Melati', 'Kenanga', 'Anggrek']).' No. '.$noRumah.', RT 05 Malabar Ujung',
            'nomor_hp' => '+62 8'.fake()->numerify('## ### #### ###'),
            'nama_pengguna' => 'warga'.str_pad($noRumah, 3, '0', STR_PAD_LEFT),
            'status_akun' => fake()->randomElement(['Aktif', 'Aktif', 'Aktif', 'Nonaktif']),
            'password' => Hash::make('warga123'),
            'harus_ganti_password' => true,
            'api_token' => null,
        ];
    }
}
