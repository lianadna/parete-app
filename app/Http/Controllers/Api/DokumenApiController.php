<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataWarga;
use App\Models\DokumenRt;
use App\Models\UnduhanDokumenWarga;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DokumenApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $warga = $this->resolveWarga($request);

        $downloadedIds = $warga
            ? UnduhanDokumenWarga::dokumenIdsUntukWarga((string) $warga->getKey())
            : [];

        $items = $this->baseQuery($request)
            ->orderByDesc('tanggal_dibuat')
            ->get()
            ->map(fn (DokumenRt $d) => $this->formatDokumen($d, $request, $downloadedIds));

        $unduhanSaya = $warga
            ? UnduhanDokumenWarga::hitungUntukWarga((string) $warga->getKey())
            : 0;

        $stats = [
            'total' => $this->baseQuery($request)->count(),
            'baru_bulan_ini' => $this->baseQuery($request)
                ->where('tanggal_dibuat', '>=', now()->startOfMonth())
                ->count(),
            // Jumlah unduhan oleh warga yang sedang login (0 jika belum pernah unduh).
            'total_unduhan' => $unduhanSaya,
        ];

        return response()->json([
            'data' => $items,
            'stats' => $stats,
        ]);
    }

    /** @return \Illuminate\Database\Eloquent\Builder<DokumenRt> */
    private function baseQuery(Request $request)
    {
        $query = DokumenRt::query()->where('akses', 'semua_warga');

        if ($request->filled('kategori')) {
            $kategori = strtolower($request->string('kategori')->toString());
            if (in_array($kategori, ['formulir', 'surat', 'peraturan', 'data'], true)) {
                $query->where('kategori', $kategori);
            }
        }

        return $query;
    }

    public function download(Request $request, string $dokumen): StreamedResponse|JsonResponse
    {
        $warga = $this->resolveWarga($request);
        if (! $warga) {
            return response()->json(['message' => 'Token autentikasi diperlukan.'], 401);
        }

        $model = DokumenRt::query()
            ->where('_id', $dokumen)
            ->where('akses', 'semua_warga')
            ->firstOrFail();

        if (! $model->path_berkas || ! Storage::disk('public')->exists($model->path_berkas)) {
            return response()->json(['message' => 'Berkas tidak ditemukan.'], 404);
        }

        UnduhanDokumenWarga::catat((string) $warga->getKey(), (string) $model->getKey());

        $model->jumlah_unduhan = (int) ($model->jumlah_unduhan ?? 0) + 1;
        $model->save();

        return Storage::disk('public')->download(
            $model->path_berkas,
            $model->nama_dokumen.'.'.$model->tipe_berkas
        );
    }

    /**
     * @param  list<string>  $downloadedIds
     * @return array<string, mixed>
     */
    private function formatDokumen(DokumenRt $d, Request $request, array $downloadedIds): array
    {
        $id = (string) $d->getKey();
        $baseUrl = rtrim($request->getSchemeAndHttpHost(), '/');

        return [
            'id' => $id,
            'nama_dokumen' => $d->nama_dokumen,
            'tipe_berkas' => $d->tipe_berkas,
            'kategori' => $d->kategori,
            'ukuran_byte' => (int) ($d->ukuran_byte ?? 0),
            'sudah_diunduh' => in_array($id, $downloadedIds, true),
            'download_url' => $baseUrl.'/api/dokumen/'.$id.'/unduh',
        ];
    }

    private function resolveWarga(Request $request): ?DataWarga
    {
        /** @var DataWarga|null $warga */
        $warga = $request->attributes->get('warga');
        if ($warga) {
            return $warga;
        }

        $token = $request->bearerToken() ?? $request->query('token');
        if ($token) {
            $fromToken = DataWarga::query()
                ->where('api_token', hash('sha256', $token))
                ->where('status_akun', 'Aktif')
                ->first();
            if ($fromToken) {
                return $fromToken;
            }
        }

        if (config('parete.auth_disabled')) {
            return DataWarga::query()->where('status_akun', 'Aktif')->first();
        }

        return null;
    }
}
