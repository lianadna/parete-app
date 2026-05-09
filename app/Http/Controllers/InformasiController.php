<?php

namespace App\Http\Controllers;

use App\Models\InformasiRt;
use App\Models\LogAktivitasAdmin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        ]);
        if (empty($validated['tanggal_kegiatan'])) {
            $validated['tanggal_kegiatan'] = null;
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
        ]);

        if (empty($validated['tanggal_kegiatan'])) {
            $validated['tanggal_kegiatan'] = null;
        }

        $model->fill($validated);
        $model->save();

        LogAktivitasAdmin::catat('memperbarui', 'informasi RT');

        return redirect()->route('informasi.index')->with('success', 'Informasi berhasil diperbarui.');
    }

    public function destroy(string $informasi): RedirectResponse
    {
        try {
            InformasiRt::query()->findOrFail($informasi)->delete();

            LogAktivitasAdmin::catat('menghapus', 'informasi RT');

            return redirect()->route('informasi.index')->with('success', 'Data informasi RT berhasil dihapus.');
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('informasi.index')->with('error', 'Data informasi RT gagal dihapus.');
        }
    }
}
