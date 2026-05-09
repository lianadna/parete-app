<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class PengaduanWarga extends Model
{
    /** @use HasFactory<\Database\Factories\PengaduanWargaFactory> */
    use HasFactory;

    protected $connection = 'mongodb';

    protected $table = 'pengaduan_warga';

    const CREATED_AT = 'tanggal_dibuat';

    const UPDATED_AT = 'tanggal_diubah';

    protected $fillable = [
        'nomor_pengaduan',
        'referensi_warga_id',
        'nama_pelapor',
        'judul_pengaduan',
        'topik',
        'lokasi_kejadian',
        'deskripsi',
        'status_pengaduan',
        'lampiran_gambar',
        'catatan_selesai',
        'bukti_penyelesaian',
        'alasan_ditolak',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_dibuat' => 'datetime',
            'tanggal_diubah' => 'datetime',
            'lampiran_gambar' => 'array',
        ];
    }

    public function warga()
    {
        return $this->belongsTo(DataWarga::class, 'referensi_warga_id', '_id');
    }
}
