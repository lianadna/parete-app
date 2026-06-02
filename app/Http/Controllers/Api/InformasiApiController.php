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

        $items = $query->get()->map(fn (InformasiRt $i) => $i->toApiArray());

        return response()->json(['data' => $items]);
    }

    public function show(string $informasi): JsonResponse
    {
        $model = InformasiRt::query()->findOrFail($informasi);

        return response()->json(['data' => $model->toApiArray()]);
    }
}
