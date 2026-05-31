<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataWarga;
use App\Models\PengaduanWarga;
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
                ->where('status_pengaduan', 'Selesai')
                ->count(),
        ];

        return response()->json([
            'warga' => [
                'id' => $wargaId,
                'id_keluarga' => $warga->id_keluarga,
                'nama_kepala_keluarga' => $warga->nama_kepala_keluarga,
                'nomor_rumah' => $warga->nomor_rumah,
                'alamat_lengkap' => $warga->alamat_lengkap,
                'nomor_hp' => $warga->nomor_hp,
                'status_akun' => $warga->status_akun,
            ],
            'stats' => $stats,
        ]);
    }
}
