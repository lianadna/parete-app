<?php

namespace Database\Seeders;

use App\Models\DataWarga;
use App\Models\DokumenRt;
use App\Models\InformasiRt;
use App\Models\LogAktivitasAdmin;
use App\Models\PengaduanWarga;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PareteDataSeeder extends Seeder
{
    public function run(): void
    {
        Config::set('faker.locale', 'id_ID');

        DataWarga::query()->delete();
        PengaduanWarga::query()->delete();
        InformasiRt::query()->delete();
        DokumenRt::query()->delete();

        LogAktivitasAdmin::query()->delete();

        DataWarga::factory(15)->create();

        PengaduanWarga::factory(15)->create();

        InformasiRt::factory(15)->create();

        Storage::disk('public')->makeDirectory('dokumen_rt');
        foreach (DokumenRt::factory()->count(15)->make() as $dokumen) {
            $isi = "Contoh isi berkas untuk: {$dokumen->nama_dokumen}\nRT 05 Malabar Ujung.";
            $slug = Str::slug(Str::limit($dokumen->nama_dokumen, 40, ''));
            $path = 'dokumen_rt/'.$slug.'-'.Str::lower(Str::random(5)).'.'.$dokumen->tipe_berkas;
            Storage::disk('public')->put($path, $isi);
            $dokumen->path_berkas = $path;
            $dokumen->ukuran_byte = (int) Storage::disk('public')->size($path);
            $dokumen->save();
        }

        $t = Carbon::now()->startOfDay();
        foreach ([
            ['menambah', 'data warga', $t->copy()->addHours(8)->addMinutes(12)->addSeconds(5)],
            ['memperbarui', 'informasi RT', $t->copy()->addHours(9)->addMinutes(41)->addSeconds(18)],
            ['menambah', 'dokumen', $t->copy()->addHours(11)->addSeconds(33)],
            ['menghapus', 'informasi RT', $t->copy()->addHours(13)->addMinutes(5)->addSeconds(2)],
            ['memperbarui', 'data warga', $t->copy()->addHours(14)->addMinutes(22)->addSeconds(44)],
            ['mengubah status pengaduan', 'ADU-001 menjadi Diproses', $t->copy()->addHours(15)->addMinutes(3)],
            ['mengubah status pengaduan', 'ADU-002 menjadi Selesai', $t->copy()->addHours(16)->addMinutes(58)->addSeconds(7)],
        ] as [$aksi, $objek, $waktu]) {
            LogAktivitasAdmin::catat($aksi, $objek, 'Super Admin', $waktu);
        }
    }
}
