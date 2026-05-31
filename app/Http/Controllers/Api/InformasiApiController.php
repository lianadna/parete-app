<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InformasiRt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InformasiApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = InformasiRt::query()->orderByDesc('tanggal_publikasi');

        if ($request->filled('jenis')) {
            $jenis = strtolower($request->string('jenis')->toString());
            if (in_array($jenis, ['pengumuman', 'kegiatan'], true)) {
                $query->where('jenis_informasi', $jenis);
            }
        }

        $items = $query->get()->map(fn (InformasiRt $i) => $this->formatInformasi($i));

        return response()->json(['data' => $items]);
    }

    public function show(string $informasi): JsonResponse
    {
        $model = InformasiRt::query()->findOrFail($informasi);

        return response()->json(['data' => $this->formatInformasi($model)]);
    }

    /** @return array<string, mixed> */
    private function formatInformasi(InformasiRt $i): array
    {
        return [
            'id' => (string) $i->getKey(),
            'jenis_informasi' => $i->jenis_informasi,
            'judul_informasi' => $i->judul_informasi,
            'isi_informasi' => $i->isi_informasi,
            'tanggal_publikasi' => optional($i->tanggal_publikasi)->toIso8601String(),
            'tanggal_kegiatan' => optional($i->tanggal_kegiatan)->toIso8601String(),
            'penulis' => $i->penulis,
        ];
    }
}
