<?php

namespace App\Http\Controllers;

use App\Models\DataWarga;
use App\Models\LogAktivitasAdmin;
use App\Models\PengaduanWarga;
use App\Models\ProfilRt;
use App\Support\PhoneNumber;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Throwable;

class WargaController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $wargas = $this->wargaQuery($search)->paginate(10)->withQueryString();
        $this->attachPengaduanCounts($wargas);

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

    public function exportPdf(Request $request): Response
    {
        $search = trim((string) $request->query('q', ''));
        $wargas = $this->wargaQuery($search)->get();
        $this->attachPengaduanCounts($wargas);

        $profil = ProfilRt::current();

        $pdf = Pdf::loadView('warga.pdf', [
            'wargas' => $wargas,
            'profil' => $profil,
            'adminNama' => Auth::user()->nama,
            'search' => $search,
            'exportedAt' => now()->timezone(config('app.timezone', 'Asia/Jakarta')),
            'stats' => [
                'total' => $wargas->count(),
                'aktif' => $wargas->where('status_akun', 'Aktif')->count(),
                'nonaktif' => $wargas->where('status_akun', 'Nonaktif')->count(),
            ],
        ])->setPaper('a4', 'landscape');

        LogAktivitasAdmin::catat('mengekspor', 'data warga (PDF)');

        $filename = 'data-warga-'.str_pad((string) $profil->nomor_rt, 2, '0', STR_PAD_LEFT).'-'.now()->format('Y-m-d-His').'.pdf';

        return $pdf->download($filename);
    }

    /** @return Builder<DataWarga> */
    private function wargaQuery(string $search = ''): Builder
    {
        $query = DataWarga::query()->orderBy('nomor_rumah');

        if ($search !== '') {
            $pattern = new \MongoDB\BSON\Regex(preg_quote($search, '/'), 'i');
            $query->where(function ($builder) use ($pattern) {
                $builder->where('nama_kepala_keluarga', 'regex', $pattern)
                    ->orWhere('id_keluarga', 'regex', $pattern)
                    ->orWhere('nomor_rumah', 'regex', $pattern)
                    ->orWhere('nomor_hp', 'regex', $pattern)
                    ->orWhere('nama_pengguna', 'regex', $pattern);
            });
        }

        return $query;
    }

    /** @param iterable<DataWarga> $wargas */
    private function attachPengaduanCounts(iterable $wargas): void
    {
        foreach ($wargas as $w) {
            $w->jumlah_pengaduan = PengaduanWarga::query()
                ->where('referensi_warga_id', (string) $w->getKey())
                ->count();
        }
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
        $validated['password'] = Hash::make('warga123');
        $validated['harus_ganti_password'] = true;
        $validated['nomor_hp'] = PhoneNumber::normalize($validated['nomor_hp'] ?? null);

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

        $validated['nomor_hp'] = PhoneNumber::normalize($validated['nomor_hp'] ?? null);
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
