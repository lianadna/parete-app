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
        'minta_tanda_tangan',
        'path_berkas_diisi',
        'path_dokumen_pendukung',
        'path_surat_balasan',
        'path_surat_ttd',
        'status_permohonan',
        'catatan_rt',
        'alasan_ditolak',
        'tanggal_dijawab',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_dijawab' => 'datetime',
        ];
    }

    public function warga()
    {
        return $this->belongsTo(DataWarga::class, 'referensi_warga_id', '_id');
    }

    public function getJenisDokumenDisplayAttribute(): string
    {
        if ($this->jenis_dokumen === 'Lainnya' && $this->jenis_dokumen_lainnya) {
            return $this->jenis_dokumen_lainnya;
        }

        return (string) $this->jenis_dokumen;
    }

    public function mintaTandaTanganLabel(): string
    {
        return $this->minta_tanda_tangan === 'dengan_ttd' ? 'Dengan tanda tangan RT' : 'Tanpa tanda tangan';
    }
}
