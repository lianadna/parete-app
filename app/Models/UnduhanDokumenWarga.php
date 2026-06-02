<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

/**
 * Log unduhan dokumen per warga (MongoDB).
 */
class UnduhanDokumenWarga extends Model
{
    protected $connection = 'mongodb';

    protected $table = 'unduhan_dokumen_warga';

    const CREATED_AT = 'tanggal_unduh';

    const UPDATED_AT = null;

    protected $fillable = [
        'warga_id',
        'dokumen_id',
    ];

    public static function catat(string $wargaId, string $dokumenId): void
    {
        static::query()->create([
            'warga_id' => $wargaId,
            'dokumen_id' => $dokumenId,
            'tanggal_unduh' => now(),
        ]);
    }

    public static function hitungUntukWarga(string $wargaId): int
    {
        return static::query()->where('warga_id', $wargaId)->count();
    }

    /** @return list<string> */
    public static function dokumenIdsUntukWarga(string $wargaId): array
    {
        return static::query()
            ->where('warga_id', $wargaId)
            ->pluck('dokumen_id')
            ->map(fn ($id) => (string) $id)
            ->all();
    }
}
