<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataWarga;
use App\Models\PengaduanWarga;
use App\Support\PhoneNumber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileApiController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        /** @var DataWarga $warga */
        $warga = $request->attributes->get('warga');

        $wargaId = (string) $warga->getKey();

        $stats = [
            'total_pengaduan' => PengaduanWarga::query()->where('referensi_warga_id', $wargaId)->count(),
            'pengaduan_aktif' => PengaduanWarga::query()
                ->where('referensi_warga_id', $wargaId)
                ->whereIn('status_pengaduan', ['Terkirim', 'Diterima', 'Diproses'])
                ->count(),
            'pengaduan_selesai' => PengaduanWarga::query()
                ->where('referensi_warga_id', $wargaId)
                ->whereIn('status_pengaduan', ['Selesai', 'Ditolak', 'Dibatalkan'])
                ->count(),
        ];

        return response()->json([
            'warga' => $this->formatWarga($warga),
            'stats' => $stats,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        /** @var DataWarga $warga */
        $warga = $request->attributes->get('warga');

        $validated = $request->validate([
            'nomor_hp' => ['required', 'string', 'max:30'],
        ]);

        $warga->nomor_hp = PhoneNumber::normalize($validated['nomor_hp']);
        $warga->save();

        return response()->json([
            'message' => 'Nomor HP berhasil diperbarui.',
            'warga' => $this->formatWarga($warga),
        ]);
    }

    /** @return array<string, mixed> */
    private function formatWarga(DataWarga $warga): array
    {
        return [
            'id' => (string) $warga->getKey(),
            'id_keluarga' => $warga->id_keluarga,
            'nama_kepala_keluarga' => $warga->nama_kepala_keluarga,
            'nomor_rumah' => $warga->nomor_rumah,
            'alamat_lengkap' => $warga->alamat_lengkap,
            'nomor_hp' => PhoneNumber::normalize($warga->nomor_hp) ?? '',
            'status_akun' => $warga->status_akun,
            'harus_ganti_password' => (bool) ($warga->harus_ganti_password ?? false),
        ];
    }
}
