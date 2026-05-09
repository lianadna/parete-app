<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class DataWarga extends Model
{
    /** @use HasFactory<\Database\Factories\DataWargaFactory> */
    use HasFactory;

    protected $connection = 'mongodb';

    protected $table = 'data_warga';

    const CREATED_AT = 'tanggal_dibuat';

    const UPDATED_AT = 'tanggal_diubah';

    protected $fillable = [
        'id_keluarga',
        'nama_kepala_keluarga',
        'nomor_rumah',
        'alamat_lengkap',
        'nomor_hp',
        'nama_pengguna',
        'status_akun',
    ];

    public function pengaduan()
    {
        return $this->hasMany(PengaduanWarga::class, 'referensi_warga_id', '_id');
    }
}
