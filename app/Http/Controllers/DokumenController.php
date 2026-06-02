<?php

namespace App\Http\Controllers;

use App\Models\DokumenRt;
use App\Models\LogAktivitasAdmin;
use App\Models\PermohonanDokumen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class DokumenController extends Controller
{
    public function index(): View
    {
        $dokumens = DokumenRt::query()->orderByDesc('tanggal_dibuat')->paginate(10);

        $stats = [
            'total' => DokumenRt::query()->count(),
            'pdf' => DokumenRt::query()->where('tipe_berkas', 'pdf')->count(),
            'doc' => DokumenRt::query()->whereIn('tipe_berkas', ['doc', 'docx'])->count(),
            'xls' => DokumenRt::query()->whereIn('tipe_berkas', ['xls', 'xlsx'])->count(),
        ];

        $permohonans = PermohonanDokumen::query()->orderByDesc('tanggal_dibuat')->get();

        $permohonanStats = [
            'total' => PermohonanDokumen::query()->count(),
            'terkirim' => PermohonanDokumen::query()->where('status_permohonan', 'Terkirim')->count(),
            'diproses' => PermohonanDokumen::query()->where('status_permohonan', 'Diproses')->count(),
            'selesai' => PermohonanDokumen::query()->where('status_permohonan', 'Selesai')->count(),
        ];

        return view('dokumen', [
            'dokumens' => $dokumens,
            'stats' => $stats,
            'permohonans' => $permohonans,
            'permohonanStats' => $permohonanStats,
            'activeTab' => request('tab', 'arsip'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_dokumen' => ['required', 'string', 'max:200'],
            'kategori' => ['required', 'in:formulir,surat,peraturan,data'],
            'akses' => ['required', 'in:semua_warga,admin_rt'],
            'berkas' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx'],
        ]);

        $file = $request->file('berkas');
        $ext = strtolower($file->getClientOriginalExtension());
        $path = $file->store('dokumen_rt', 'public');

        DokumenRt::query()->create([
            'nama_dokumen' => $validated['nama_dokumen'],
            'tipe_berkas' => $ext,
            'kategori' => $validated['kategori'],
            'ukuran_byte' => $file->getSize(),
            'jumlah_unduhan' => 0,
            'path_berkas' => $path,
            'akses' => $validated['akses'],
        ]);

        LogAktivitasAdmin::catat('menambah', 'dokumen');

        return redirect()->route('dokumen.index')->with('success', 'Dokumen berhasil diunggah.');
    }

    public function update(Request $request, string $dokumen): RedirectResponse
    {
        $model = DokumenRt::query()->findOrFail($dokumen);

        $validated = $request->validate([
            'nama_dokumen' => ['required', 'string', 'max:200'],
            'kategori' => ['required', 'in:formulir,surat,peraturan,data'],
            'akses' => ['required', 'in:semua_warga,admin_rt'],
            'berkas' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx'],
        ]);

        $model->nama_dokumen = $validated['nama_dokumen'];
        $model->kategori = $validated['kategori'];
        $model->akses = $validated['akses'];

        if ($request->hasFile('berkas')) {
            if ($model->path_berkas && Storage::disk('public')->exists($model->path_berkas)) {
                Storage::disk('public')->delete($model->path_berkas);
            }
            $file = $request->file('berkas');
            $ext = strtolower($file->getClientOriginalExtension());
            $model->path_berkas = $file->store('dokumen_rt', 'public');
            $model->tipe_berkas = $ext;
            $model->ukuran_byte = $file->getSize();
        }

        $model->save();

        LogAktivitasAdmin::catat('memperbarui', 'dokumen');

        return redirect()->route('dokumen.index')->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroy(string $dokumen): RedirectResponse
    {
        try {
            $model = DokumenRt::query()->findOrFail($dokumen);
            if ($model->path_berkas && Storage::disk('public')->exists($model->path_berkas)) {
                Storage::disk('public')->delete($model->path_berkas);
            }
            $model->delete();

            LogAktivitasAdmin::catat('menghapus', 'dokumen');

            return redirect()->route('dokumen.index')->with('success', 'Data dokumen berhasil dihapus.');
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('dokumen.index')->with('error', 'Data dokumen gagal dihapus.');
        }
    }

    public function download(string $dokumen): StreamedResponse|RedirectResponse
    {
        $model = DokumenRt::query()->findOrFail($dokumen);
        if (! $model->path_berkas || ! Storage::disk('public')->exists($model->path_berkas)) {
            return redirect()->route('dokumen.index')->with('error', 'Berkas tidak ditemukan.');
        }

        $model->jumlah_unduhan = (int) ($model->jumlah_unduhan ?? 0) + 1;
        $model->save();

        return Storage::disk('public')->download(
            $model->path_berkas,
            $model->nama_dokumen.'.'.$model->tipe_berkas
        );
    }
}
