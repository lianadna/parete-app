<?php

namespace App\Models;

use Carbon\Carbon;
use MongoDB\Laravel\Eloquent\Model;

class LogAktivitasAdmin extends Model
{
    protected $connection = 'mongodb';

    protected $table = 'log_aktivitas_admin';

    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        'peran',
        'aksi',
        'objek',
        'waktu',
    ];

    protected function casts(): array
    {
        return [
            'waktu' => 'datetime',
        ];
    }

    public static function catat(string $aksi, string $objek, ?string $peran = null, ?Carbon $saat = null): void
    {
        $namaPeran = $peran ?? (request()->query('role') === 'super_admin'
            ? 'Super Admin'
            : 'Admin');

        self::query()->create([
            'peran' => $namaPeran,
            'aksi' => $aksi,
            'objek' => $objek,
            'waktu' => $saat ?? now(),
        ]);
    }

    public function rangkaianAktivitas(): string
    {
        return trim(implode(' ', array_filter([
            (string) $this->peran,
            (string) $this->aksi,
            (string) $this->objek,
        ])));
    }
}
