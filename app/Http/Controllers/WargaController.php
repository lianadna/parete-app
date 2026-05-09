<?php

namespace App\Http\Controllers;

use App\Models\DataWarga;
use App\Models\LogAktivitasAdmin;
use App\Models\PengaduanWarga;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class WargaController extends Controller
{
    public function index(): View
    {
        $wargas = DataWarga::query()
            ->orderByDesc('tanggal_dibuat')
            ->paginate(10);

        foreach ($wargas as $w) {
            $w->jumlah_pengaduan = PengaduanWarga::query()
                ->where('referensi_warga_id', (string) $w->getKey())
                ->count();
        }

        $stats = [
            'total' => DataWarga::query()->count(),
            'aktif' => DataWarga::query()->where('status_akun', 'Aktif')->count(),
            'nonaktif' => DataWarga::query()->where('status_akun', 'Nonaktif')->count(),
        ];

        return view('warga', [
            'wargas' => $wargas,
            'stats' => $stats,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kepala_keluarga' => ['required', 'string', 'max:200'],
            'nomor_rumah' => ['required', 'string', 'max:20'],
            'alamat_lengkap' => ['nullable', 'string', 'max:500'],
            'nomor_hp' => ['nullable', 'string', 'max:30'],
            'nama_pengguna' => ['nullable', 'string', 'max:100'],
            'status_akun' => ['required', 'in:Aktif,Nonaktif'],
        ]);

        $seq = str_pad((string) (DataWarga::query()->count() + 1), 3, '0', STR_PAD_LEFT);
        $validated['id_keluarga'] = 'RT05-'.now()->format('Y').'-'.$seq;

        DataWarga::query()->create($validated);

        LogAktivitasAdmin::catat('menambah', 'data warga');

        return redirect()->route('warga.index')->with('success', 'Data warga berhasil ditambahkan.');
    }

    public function update(Request $request, string $warga): RedirectResponse
    {
        $model = DataWarga::query()->findOrFail($warga);

        $validated = $request->validate([
            'nama_kepala_keluarga' => ['required', 'string', 'max:200'],
            'nomor_rumah' => ['required', 'string', 'max:20'],
            'alamat_lengkap' => ['nullable', 'string', 'max:500'],
            'nomor_hp' => ['nullable', 'string', 'max:30'],
            'nama_pengguna' => ['nullable', 'string', 'max:100'],
            'status_akun' => ['required', 'in:Aktif,Nonaktif'],
        ]);

        $model->fill($validated);
        $model->save();

        LogAktivitasAdmin::catat('memperbarui', 'data warga');

        return redirect()->route('warga.index')->with('success', 'Data warga berhasil diperbarui.');
    }

    public function destroy(string $warga): RedirectResponse
    {
        try {
            $model = DataWarga::query()->findOrFail($warga);
            PengaduanWarga::query()->where('referensi_warga_id', (string) $model->getKey())->delete();
            $model->delete();

            LogAktivitasAdmin::catat('menghapus', 'data warga');

            return redirect()->route('warga.index')->with('success', 'Data warga berhasil dihapus.');
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('warga.index')->with('error', 'Data warga gagal dihapus.');
        }
    }
}
