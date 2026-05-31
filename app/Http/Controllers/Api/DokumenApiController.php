<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataWarga;
use App\Models\DokumenRt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DokumenApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DokumenRt::query()
            ->where('akses', 'semua_warga')
            ->orderByDesc('tanggal_dibuat');

        if ($request->filled('kategori')) {
            $kategori = strtolower($request->string('kategori')->toString());
            if (in_array($kategori, ['formulir', 'surat', 'peraturan', 'data'], true)) {
                $query->where('kategori', $kategori);
            }
        }

        $items = $query->get()->map(fn (DokumenRt $d) => $this->formatDokumen($d));

        $stats = [
            'total' => DokumenRt::query()->where('akses', 'semua_warga')->count(),
            'baru_bulan_ini' => DokumenRt::query()
                ->where('akses', 'semua_warga')
                ->where('tanggal_dibuat', '>=', now()->startOfMonth())
                ->count(),
            'total_unduhan' => (int) DokumenRt::query()->where('akses', 'semua_warga')->sum('jumlah_unduhan'),
        ];

        return response()->json([
            'data' => $items,
            'stats' => $stats,
        ]);
    }

    public function download(Request $request, string $dokumen): StreamedResponse|JsonResponse
    {
        if (! $this->resolveWarga($request)) {
            return response()->json(['message' => 'Token autentikasi diperlukan.'], 401);
        }

        $model = DokumenRt::query()
            ->where('_id', $dokumen)
            ->where('akses', 'semua_warga')
            ->firstOrFail();

        if (! $model->path_berkas || ! Storage::disk('public')->exists($model->path_berkas)) {
            return response()->json(['message' => 'Berkas tidak ditemukan.'], 404);
        }

        $model->jumlah_unduhan = (int) ($model->jumlah_unduhan ?? 0) + 1;
        $model->save();

        return Storage::disk('public')->download(
            $model->path_berkas,
            $model->nama_dokumen.'.'.$model->tipe_berkas
        );
    }

    /** @return array<string, mixed> */
    private function formatDokumen(DokumenRt $d): array
    {
        $baseUrl = rtrim(config('app.url'), '/');

        return [
            'id' => (string) $d->getKey(),
            'nama_dokumen' => $d->nama_dokumen,
            'tipe_berkas' => $d->tipe_berkas,
            'kategori' => $d->kategori,
            'ukuran_byte' => (int) ($d->ukuran_byte ?? 0),
            'jumlah_unduhan' => (int) ($d->jumlah_unduhan ?? 0),
            'download_url' => $baseUrl.'/api/dokumen/'.$d->getKey().'/unduh',
        ];
    }

    private function resolveWarga(Request $request): ?DataWarga
    {
        if (config('parete.auth_disabled')) {
            return DataWarga::query()->where('status_akun', 'Aktif')->first();
        }

        /** @var DataWarga|null $warga */
        $warga = $request->attributes->get('warga');
        if ($warga) {
            return $warga;
        }

        $token = $request->bearerToken() ?? $request->query('token');
        if (! $token) {
            return null;
        }

        return DataWarga::query()
            ->where('api_token', hash('sha256', $token))
            ->where('status_akun', 'Aktif')
            ->first();
    }
}
