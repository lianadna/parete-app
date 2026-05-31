<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class PermohonanDokumen extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';

    protected $table = 'permohonan_dokumen';

    const CREATED_AT = 'tanggal_dibuat';

    const UPDATED_AT = 'tanggal_diubah';

    protected $fillable = [
        'referensi_warga_id',
        'nama_pemohon',
        'jenis_dokumen',
        'jenis_dokumen_lainnya',
        'keperluan',
        'catatan_tambahan',
        'path_berkas_diisi',
        'path_dokumen_pendukung',
        'status_permohonan',
    ];

    public function warga()
    {
        return $this->belongsTo(DataWarga::class, 'referensi_warga_id', '_id');
    }
}
