<?php

namespace App\Models;

use App\Support\MediaUrl;
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
        'gambar_informasi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_publikasi' => 'datetime',
            'tanggal_kegiatan' => 'datetime',
        ];
    }

    public function gambarPublikUrl(): ?string
    {
        return MediaUrl::fromPublicDisk($this->gambar_informasi);
    }

    /** Path relatif file di disk `public` (disimpan di MongoDB). */
    public function gambarStoragePath(): ?string
    {
        if (empty($this->gambar_informasi)) {
            return null;
        }

        return str_replace('\\', '/', ltrim((string) $this->gambar_informasi, '/'));
    }

    /** @return array<string, mixed> */
    public function toApiArray(): array
    {
        return [
            'id' => (string) $this->getKey(),
            'jenis_informasi' => $this->jenis_informasi,
            'judul_informasi' => $this->judul_informasi,
            'isi_informasi' => $this->isi_informasi,
            'tanggal_publikasi' => \App\Support\ApiDate::format($this->tanggal_publikasi),
            'tanggal_kegiatan' => \App\Support\ApiDate::format($this->tanggal_kegiatan),
            'penulis' => $this->penulis,
            // Path di MongoDB; app mobile menyusun URL dari host API-nya sendiri.
            'gambar_informasi' => $this->gambarStoragePath(),
        ];
    }
}
