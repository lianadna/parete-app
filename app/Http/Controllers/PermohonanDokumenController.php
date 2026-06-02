<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitasAdmin;
use App\Models\PermohonanDokumen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PermohonanDokumenController extends Controller
{
    public function respond(Request $request, string $permohonan): RedirectResponse
    {
        $model = PermohonanDokumen::query()->findOrFail($permohonan);

        $validated = $request->validate([
            'status_permohonan' => ['required', 'in:Diproses,Selesai,Ditolak'],
            'catatan_rt' => ['nullable', 'string', 'max:2000'],
            'alasan_ditolak' => ['nullable', 'string', 'max:2000'],
            'surat_balasan' => ['nullable', 'file', 'max:15360', 'mimes:pdf,doc,docx'],
            'surat_ttd' => ['nullable', 'file', 'max:15360', 'mimes:pdf,doc,docx'],
        ]);

        if ($validated['status_permohonan'] === 'Ditolak' && empty($validated['alasan_ditolak'])) {
            return back()->withErrors(['alasan_ditolak' => 'Alasan penolakan wajib diisi.'])->withInput();
        }

        if ($validated['status_permohonan'] === 'Selesai') {
            if (! $request->hasFile('surat_balasan') && empty($model->path_surat_balasan)) {
                return back()->withErrors(['surat_balasan' => 'Unggah surat balasan (PDF/DOCX) untuk menyelesaikan permohonan.'])->withInput();
            }
            if ($model->minta_tanda_tangan === 'dengan_ttd'
                && ! $request->hasFile('surat_ttd')
                && empty($model->path_surat_ttd)) {
                return back()->withErrors(['surat_ttd' => 'Warga meminta surat bertanda tangan. Unggah file surat TTD.'])->withInput();
            }
        }

        if ($request->hasFile('surat_balasan')) {
            $this->hapusFile($model->path_surat_balasan);
            $model->path_surat_balasan = $this->simpanFile($request->file('surat_balasan'), 'balasan_rt');
        }

        if ($request->hasFile('surat_ttd')) {
            $this->hapusFile($model->path_surat_ttd);
            $model->path_surat_ttd = $this->simpanFile($request->file('surat_ttd'), 'balasan_rt');
        }

        $model->status_permohonan = $validated['status_permohonan'];
        $model->catatan_rt = $validated['catatan_rt'] ?? null;
        $model->alasan_ditolak = $validated['status_permohonan'] === 'Ditolak'
            ? ($validated['alasan_ditolak'] ?? null)
            : null;

        if ($validated['status_permohonan'] === 'Selesai') {
            $model->tanggal_dijawab = now();
        }

        $model->save();

        LogAktivitasAdmin::catat('menindaklanjuti', 'permohonan dokumen warga');

        return redirect()
            ->route('dokumen.index', ['tab' => 'permohonan'])
            ->with('success', 'Permohonan dokumen berhasil diperbarui.');
    }

    public function file(string $permohonan, string $jenis): StreamedResponse|RedirectResponse
    {
        $model = PermohonanDokumen::query()->findOrFail($permohonan);

        $path = match ($jenis) {
            'berkas_diisi' => $model->path_berkas_diisi,
            'dokumen_pendukung' => $model->path_dokumen_pendukung,
            'surat_balasan' => $model->path_surat_balasan,
            'surat_ttd' => $model->path_surat_ttd,
            default => null,
        };

        if (! $path || ! Storage::disk('public')->exists($path)) {
            return redirect()->route('dokumen.index', ['tab' => 'permohonan'])
                ->with('error', 'Berkas tidak ditemukan.');
        }

        $nama = $model->jenis_dokumen_display.'-'.$jenis;

        return Storage::disk('public')->download($path, $nama);
    }

    private function simpanFile(\Illuminate\Http\UploadedFile $file, string $folder): string
    {
        $path = $file->store('permohonan_dokumen/'.$folder, 'public');
        if ($path === false) {
            throw new \RuntimeException('Gagal menyimpan berkas.');
        }

        return str_replace('\\', '/', $path);
    }

    private function hapusFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
