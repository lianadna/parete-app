<?php

namespace App\Http\Controllers;

use App\Models\InformasiRt;
use App\Models\LogAktivitasAdmin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class InformasiController extends Controller
{
    public function index(): View
    {
        $informasis = InformasiRt::query()->orderByDesc('tanggal_publikasi')->paginate(9);

        return view('informasi', ['informasis' => $informasis]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'tanggal_kegiatan' => $request->filled('tanggal_kegiatan') ? $request->input('tanggal_kegiatan') : null,
        ]);

        $validated = $request->validate([
            'jenis_informasi' => ['required', 'in:pengumuman,kegiatan'],
            'judul_informasi' => ['required', 'string', 'max:200'],
            'isi_informasi' => ['required', 'string', 'max:10000'],
            'tanggal_publikasi' => ['required', 'date'],
            'tanggal_kegiatan' => ['nullable', 'date'],
            'penulis' => ['required', 'string', 'max:100'],
            'gambar_informasi' => ['nullable', 'image', 'max:5120', 'mimes:jpg,jpeg,png,webp'],
        ]);
        if (empty($validated['tanggal_kegiatan'])) {
            $validated['tanggal_kegiatan'] = null;
        }

        if ($request->hasFile('gambar_informasi')) {
            $validated['gambar_informasi'] = $this->simpanGambar($request->file('gambar_informasi'));
        } else {
            unset($validated['gambar_informasi']);
        }

        InformasiRt::query()->create($validated);

        LogAktivitasAdmin::catat('menambah', 'informasi RT');

        return redirect()->route('informasi.index')->with('success', 'Informasi berhasil dipublikasikan.');
    }

    public function update(Request $request, string $informasi): RedirectResponse
    {
        $model = InformasiRt::query()->findOrFail($informasi);

        $request->merge([
            'tanggal_kegiatan' => $request->filled('tanggal_kegiatan') ? $request->input('tanggal_kegiatan') : null,
        ]);

        $validated = $request->validate([
            'jenis_informasi' => ['required', 'in:pengumuman,kegiatan'],
            'judul_informasi' => ['required', 'string', 'max:200'],
            'isi_informasi' => ['required', 'string', 'max:10000'],
            'tanggal_publikasi' => ['required', 'date'],
            'tanggal_kegiatan' => ['nullable', 'date'],
            'penulis' => ['required', 'string', 'max:100'],
            'gambar_informasi' => ['nullable', 'image', 'max:5120', 'mimes:jpg,jpeg,png,webp'],
        ]);

        if (empty($validated['tanggal_kegiatan'])) {
            $validated['tanggal_kegiatan'] = null;
        }

        if ($request->hasFile('gambar_informasi')) {
            $this->hapusGambarLama($model->gambar_informasi);
            $validated['gambar_informasi'] = $this->simpanGambar($request->file('gambar_informasi'));
        } else {
            unset($validated['gambar_informasi']);
        }

        $model->fill($validated);
        $model->save();

        LogAktivitasAdmin::catat('memperbarui', 'informasi RT');

        return redirect()->route('informasi.index')->with('success', 'Informasi berhasil diperbarui.');
    }

    public function destroy(string $informasi): RedirectResponse
    {
        try {
            $model = InformasiRt::query()->findOrFail($informasi);
            $this->hapusGambarLama($model->gambar_informasi);
            $model->delete();

            LogAktivitasAdmin::catat('menghapus', 'informasi RT');

            return redirect()->route('informasi.index')->with('success', 'Data informasi RT berhasil dihapus.');
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('informasi.index')->with('error', 'Data informasi RT gagal dihapus.');
        }
    }

    private function simpanGambar(UploadedFile $file): string
    {
        $path = $file->store('informasi_gambar', 'public');
        if ($path === false) {
            throw new \RuntimeException('Gagal menyimpan gambar pengumuman.');
        }

        return str_replace('\\', '/', $path);
    }

    private function hapusGambarLama(?string $path): void
    {
        if (empty($path) || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }

        Storage::disk('public')->delete(str_replace('\\', '/', $path));
    }
}
