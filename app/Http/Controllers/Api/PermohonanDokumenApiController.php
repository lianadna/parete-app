<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataWarga;
use App\Models\PermohonanDokumen;
use App\Support\ApiDate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            ->map(fn (PermohonanDokumen $p) => $this->format($p, $request));

        return response()->json(['data' => $items]);
    }

    public function show(Request $request, string $permohonan): JsonResponse
    {
        /** @var DataWarga $warga */
        $warga = $request->attributes->get('warga');

        $model = PermohonanDokumen::query()
            ->where('_id', $permohonan)
            ->where('referensi_warga_id', (string) $warga->getKey())
            ->firstOrFail();

        return response()->json(['data' => $this->format($model, $request)]);
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
            'minta_tanda_tangan' => ['required', 'in:tanpa_ttd,dengan_ttd'],
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
            'minta_tanda_tangan' => $validated['minta_tanda_tangan'],
            'path_berkas_diisi' => $pathDiisi,
            'path_dokumen_pendukung' => $pathPendukung,
            'status_permohonan' => 'Terkirim',
        ]);

        return response()->json([
            'message' => 'Permohonan dokumen berhasil dikirim.',
            'data' => $this->format($permohonan, $request),
        ], 201);
    }

    public function downloadSurat(Request $request, string $permohonan, string $jenis): StreamedResponse|JsonResponse
    {
        /** @var DataWarga $warga */
        $warga = $request->attributes->get('warga');

        $model = PermohonanDokumen::query()
            ->where('_id', $permohonan)
            ->where('referensi_warga_id', (string) $warga->getKey())
            ->firstOrFail();

        if ($model->status_permohonan !== 'Selesai') {
            return response()->json(['message' => 'Surat belum tersedia.'], 403);
        }

        $path = match ($jenis) {
            'balasan' => $model->path_surat_balasan,
            'ttd' => $model->path_surat_ttd,
            default => null,
        };

        if (! $path || ! Storage::disk('public')->exists($path)) {
            return response()->json(['message' => 'Berkas surat tidak ditemukan.'], 404);
        }

        $ext = pathinfo($path, PATHINFO_EXTENSION) ?: 'pdf';

        return Storage::disk('public')->download(
            $path,
            $model->jenis_dokumen_display.'.'.$ext
        );
    }

    public function update(Request $request, string $permohonan): JsonResponse
    {
        /** @var DataWarga $warga */
        $warga = $request->attributes->get('warga');

        $model = PermohonanDokumen::query()
            ->where('_id', $permohonan)
            ->where('referensi_warga_id', (string) $warga->getKey())
            ->firstOrFail();

        $this->assertStatusTerkirim($model->status_permohonan);

        $validated = $request->validate([
            'jenis_dokumen' => ['required', 'string', 'max:100'],
            'jenis_dokumen_lainnya' => ['nullable', 'string', 'max:200'],
            'keperluan' => ['required', 'string', 'max:5000'],
            'catatan_tambahan' => ['nullable', 'string', 'max:2000'],
            'minta_tanda_tangan' => ['required', 'in:tanpa_ttd,dengan_ttd'],
            'berkas_diisi' => ['nullable', 'file', 'max:15360', 'mimes:pdf,doc,docx'],
            'dokumen_pendukung' => ['nullable', 'file', 'max:15360', 'mimes:pdf,doc,docx'],
            'hapus_berkas_diisi' => ['nullable', 'boolean'],
            'hapus_dokumen_pendukung' => ['nullable', 'boolean'],
        ]);

        $model->jenis_dokumen = $validated['jenis_dokumen'];
        $model->jenis_dokumen_lainnya = $validated['jenis_dokumen_lainnya'] ?? null;
        $model->keperluan = $validated['keperluan'];
        $model->catatan_tambahan = $validated['catatan_tambahan'] ?? null;
        $model->minta_tanda_tangan = $validated['minta_tanda_tangan'];

        if ($request->boolean('hapus_berkas_diisi')) {
            $this->hapusFile($model->path_berkas_diisi);
            $model->path_berkas_diisi = null;
        }
        if ($request->boolean('hapus_dokumen_pendukung')) {
            $this->hapusFile($model->path_dokumen_pendukung);
            $model->path_dokumen_pendukung = null;
        }
        if ($request->hasFile('berkas_diisi')) {
            $this->hapusFile($model->path_berkas_diisi);
            $model->path_berkas_diisi = $request->file('berkas_diisi')->store('permohonan_dokumen', 'public');
        }
        if ($request->hasFile('dokumen_pendukung')) {
            $this->hapusFile($model->path_dokumen_pendukung);
            $model->path_dokumen_pendukung = $request->file('dokumen_pendukung')->store('permohonan_dokumen', 'public');
        }

        $model->save();

        return response()->json([
            'message' => 'Permohonan berhasil diperbarui.',
            'data' => $this->format($model, $request),
        ]);
    }

    public function batalkan(Request $request, string $permohonan): JsonResponse
    {
        /** @var DataWarga $warga */
        $warga = $request->attributes->get('warga');

        $model = PermohonanDokumen::query()
            ->where('_id', $permohonan)
            ->where('referensi_warga_id', (string) $warga->getKey())
            ->firstOrFail();

        $this->assertStatusTerkirim($model->status_permohonan);

        $model->status_permohonan = 'Dibatalkan';
        $model->save();

        return response()->json([
            'message' => 'Permohonan berhasil dibatalkan.',
            'data' => $this->format($model, $request),
        ]);
    }

    private function assertStatusTerkirim(?string $status): void
    {
        if ($status !== 'Terkirim') {
            abort(422, 'Permohonan hanya dapat diubah atau dibatalkan saat status Terkirim.');
        }
    }

    private function hapusFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /** @return array<string, mixed> */
    private function format(PermohonanDokumen $p, Request $request): array
    {
        $base = rtrim($request->getSchemeAndHttpHost(), '/');
        $id = (string) $p->getKey();

        return [
            'id' => $id,
            'jenis_dokumen' => $p->jenis_dokumen,
            'jenis_dokumen_lainnya' => $p->jenis_dokumen_lainnya,
            'jenis_dokumen_display' => $p->jenis_dokumen_display,
            'keperluan' => $p->keperluan,
            'catatan_tambahan' => $p->catatan_tambahan,
            'minta_tanda_tangan' => $p->minta_tanda_tangan ?? 'tanpa_ttd',
            'minta_tanda_tangan_label' => $p->mintaTandaTanganLabel(),
            'status_permohonan' => $p->status_permohonan,
            'catatan_rt' => $p->catatan_rt,
            'alasan_ditolak' => $p->alasan_ditolak,
            'tanggal_dibuat' => ApiDate::format($p->tanggal_dibuat),
            'tanggal_dijawab' => ApiDate::format($p->tanggal_dijawab),
            'surat_balasan_tersedia' => ! empty($p->path_surat_balasan),
            'surat_ttd_tersedia' => ! empty($p->path_surat_ttd),
            'download_surat_balasan_url' => $base.'/api/permohonan-dokumen/'.$id.'/surat/balasan',
            'download_surat_ttd_url' => $base.'/api/permohonan-dokumen/'.$id.'/surat/ttd',
            'can_edit' => $p->status_permohonan === 'Terkirim',
            'can_batalkan' => $p->status_permohonan === 'Terkirim',
        ];
    }
}
