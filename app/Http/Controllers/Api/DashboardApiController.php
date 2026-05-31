<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataWarga;
use App\Models\InformasiRt;
use App\Models\PengaduanWarga;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        /** @var DataWarga $warga */
        $warga = $request->attributes->get('warga');
        $wargaId = (string) $warga->getKey();

        $informasiTerbaru = InformasiRt::query()
            ->orderByDesc('tanggal_publikasi')
            ->limit(3)
            ->get()
            ->map(fn (InformasiRt $i) => [
                'id' => (string) $i->getKey(),
                'jenis_informasi' => $i->jenis_informasi,
                'judul_informasi' => $i->judul_informasi,
                'isi_informasi' => $i->isi_informasi,
                'tanggal_publikasi' => optional($i->tanggal_publikasi)->toIso8601String(),
            ]);

        $pengaduanTerbaru = PengaduanWarga::query()
            ->where('referensi_warga_id', $wargaId)
            ->orderByDesc('tanggal_dibuat')
            ->limit(3)
            ->get()
            ->map(fn (PengaduanWarga $p) => [
                'id' => (string) $p->getKey(),
                'nomor_pengaduan' => $p->nomor_pengaduan,
                'judul_pengaduan' => $p->judul_pengaduan,
                'status_pengaduan' => $p->status_pengaduan,
                'tanggal_dibuat' => optional($p->tanggal_dibuat)->toIso8601String(),
            ]);

        return response()->json([
            'warga' => [
                'nama_kepala_keluarga' => $warga->nama_kepala_keluarga,
                'id_keluarga' => $warga->id_keluarga,
            ],
            'informasi_terbaru' => $informasiTerbaru,
            'pengaduan_terbaru' => $pengaduanTerbaru,
        ]);
    }
}
