<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class InformasiRt extends Model
{
    /** @use HasFactory<\Database\Factories\InformasiRtFactory> */
    use HasFactory;

    protected $connection = 'mongodb';

    protected $table = 'informasi_rt';

    const CREATED_AT = 'tanggal_dibuat';

    const UPDATED_AT = 'tanggal_diubah';

    protected $fillable = [
        'jenis_informasi',
        'judul_informasi',
        'isi_informasi',
        'tanggal_publikasi',
        'tanggal_kegiatan',
        'penulis',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_publikasi' => 'datetime',
            'tanggal_kegiatan' => 'datetime',
        ];
    }
}
