<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class DokumenRt extends Model
{
    /** @use HasFactory<\Database\Factories\DokumenRtFactory> */
    use HasFactory;

    protected $connection = 'mongodb';

    protected $table = 'dokumen_rt';

    const CREATED_AT = 'tanggal_dibuat';

    const UPDATED_AT = 'tanggal_diubah';

    protected $fillable = [
        'nama_dokumen',
        'tipe_berkas',
        'kategori',
        'ukuran_byte',
        'jumlah_unduhan',
        'path_berkas',
        'akses',
    ];
}
