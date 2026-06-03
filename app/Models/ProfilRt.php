<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ProfilRt extends Model
{
    protected $connection = 'mongodb';

    protected $table = 'profil_rt';

    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        'kunci',
        'nama_ketua_rt',
        'nomor_rt',
        'nomor_rw',
        'kelurahan',
        'kecamatan',
        'kota',
        'provinsi',
        'kode_pos',
    ];

    /** @return array<string, string> */
    public static function defaultValues(): array
    {
        return [
            'nama_ketua_rt' => 'Pak Budi Santoso',
            'nomor_rt' => '05',
            'nomor_rw' => '03',
            'kelurahan' => 'Malabar Ujung',
            'kecamatan' => 'Cibeunying Kaler',
            'kota' => 'Bandung',
            'provinsi' => 'Jawa Barat',
            'kode_pos' => '40124',
        ];
    }

    public static function current(): self
    {
        $profil = self::query()->where('kunci', 'utama')->first();

        if ($profil instanceof self) {
            return $profil;
        }

        return self::query()->create(array_merge(['kunci' => 'utama'], self::defaultValues()));
    }

    public function labelRt(): string
    {
        $nomor = trim((string) ($this->nomor_rt ?? ''));

        return $nomor !== '' ? 'RT '.str_pad($nomor, 2, '0', STR_PAD_LEFT) : 'RT —';
    }

    public function labelRw(): string
    {
        $nomor = trim((string) ($this->nomor_rw ?? ''));

        return $nomor !== '' ? 'RW '.str_pad($nomor, 2, '0', STR_PAD_LEFT) : 'RW —';
    }

    public function ringkasanWilayah(): string
    {
        return collect([
            $this->labelRt(),
            $this->labelRw(),
            $this->kelurahan,
            $this->kecamatan,
            $this->kota,
        ])->filter(fn ($bagian) => filled($bagian))->implode(', ');
    }

    /** @return array<string, mixed> */
    public function toApiArray(): array
    {
        return [
            'nama_ketua_rt' => $this->nama_ketua_rt,
            'nomor_rt' => $this->nomor_rt,
            'nomor_rw' => $this->nomor_rw,
            'kelurahan' => $this->kelurahan,
            'kecamatan' => $this->kecamatan,
            'kota' => $this->kota,
            'provinsi' => $this->provinsi,
            'kode_pos' => $this->kode_pos,
            'label_rt' => $this->labelRt(),
            'label_rw' => $this->labelRw(),
        ];
    }
}
