<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataWarga;
use App\Models\PermohonanDokumen;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermohonanDokumenApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        /** @var DataWarga $warga */
        $warga = $request->attributes->get('warga');

        $items = PermohonanDokumen::query()
            ->where('referensi_warga_id', (string) $warga->getKey())
            ->orderByDesc('tanggal_dibuat')
            ->get()
            ->map(fn (PermohonanDokumen $p) => $this->format($p));

        return response()->json(['data' => $items]);
    }

    public function store(Request $request): JsonResponse
    {
        /** @var DataWarga $warga */
        $warga = $request->attributes->get('warga');

        $validated = $request->validate([
            'jenis_dokumen' => ['required', 'string', 'max:100'],
            'jenis_dokumen_lainnya' => ['nullable', 'string', 'max:200'],
            'keperluan' => ['required', 'string', 'max:5000'],
            'catatan_tambahan' => ['nullable', 'string', 'max:2000'],
            'berkas_diisi' => ['nullable', 'file', 'max:15360', 'mimes:pdf,doc,docx'],
            'dokumen_pendukung' => ['nullable', 'file', 'max:15360', 'mimes:pdf,doc,docx'],
        ]);

        $pathDiisi = $request->hasFile('berkas_diisi')
            ? $request->file('berkas_diisi')->store('permohonan_dokumen', 'public')
            : null;

        $pathPendukung = $request->hasFile('dokumen_pendukung')
            ? $request->file('dokumen_pendukung')->store('permohonan_dokumen', 'public')
            : null;

        $permohonan = PermohonanDokumen::query()->create([
            'referensi_warga_id' => (string) $warga->getKey(),
            'nama_pemohon' => $warga->nama_kepala_keluarga,
            'jenis_dokumen' => $validated['jenis_dokumen'],
            'jenis_dokumen_lainnya' => $validated['jenis_dokumen_lainnya'] ?? null,
            'keperluan' => $validated['keperluan'],
            'catatan_tambahan' => $validated['catatan_tambahan'] ?? null,
            'path_berkas_diisi' => $pathDiisi,
            'path_dokumen_pendukung' => $pathPendukung,
            'status_permohonan' => 'Terkirim',
        ]);

        return response()->json([
            'message' => 'Permohonan dokumen berhasil dikirim.',
            'data' => $this->format($permohonan),
        ], 201);
    }

    /** @return array<string, mixed> */
    private function format(PermohonanDokumen $p): array
    {
        return [
            'id' => (string) $p->getKey(),
            'jenis_dokumen' => $p->jenis_dokumen,
            'jenis_dokumen_lainnya' => $p->jenis_dokumen_lainnya,
            'keperluan' => $p->keperluan,
            'catatan_tambahan' => $p->catatan_tambahan,
            'status_permohonan' => $p->status_permohonan,
            'tanggal_dibuat' => optional($p->tanggal_dibuat)->toIso8601String(),
        ];
    }
}
