<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitasAdmin;
use App\Models\PengaduanWarga;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PengaduanController extends Controller
{
    /** @var array<string, list<string>> */
    private const ALLOWED_TRANSITIONS = [
        'Diterima' => ['Diproses', 'Ditolak'],
        'Diproses' => ['Selesai', 'Ditolak'],
    ];

    public function index(): View
    {
        $pengaduans = PengaduanWarga::query()->orderByDesc('tanggal_dibuat')->paginate(10);

        $stats = [
            'total' => PengaduanWarga::query()->count(),
            'terkirim' => PengaduanWarga::query()->where('status_pengaduan', 'Terkirim')->count(),
            'diterima' => PengaduanWarga::query()->where('status_pengaduan', 'Diterima')->count(),
            'diproses' => PengaduanWarga::query()->where('status_pengaduan', 'Diproses')->count(),
            'selesai' => PengaduanWarga::query()->where('status_pengaduan', 'Selesai')->count(),
            'ditolak' => PengaduanWarga::query()->where('status_pengaduan', 'Ditolak')->count(),
            'dibatalkan' => PengaduanWarga::query()->where('status_pengaduan', 'Dibatalkan')->count(),
        ];

        return view('pengaduan', [
            'pengaduans' => $pengaduans,
            'stats' => $stats,
        ]);
    }

    public function markDibuka(string $pengaduan): JsonResponse
    {
        $model = PengaduanWarga::query()->findOrFail($pengaduan);
        if ($model->status_pengaduan === 'Terkirim') {
            $model->status_pengaduan = 'Diterima';
            $model->save();
            LogAktivitasAdmin::catat('menandai diterima', 'pengaduan warga '.$model->nomor_pengaduan);
        }

        return response()->json([
            'status_pengaduan' => $model->status_pengaduan,
            'nomor_pengaduan' => $model->nomor_pengaduan,
        ]);
    }

    public function update(Request $request, string $pengaduan): RedirectResponse
    {
        if (! $request->boolean('pemutakhiran_status')) {
            abort(403);
        }

        $model = PengaduanWarga::query()->findOrFail($pengaduan);

        return $this->sinkronkanStatus($request, $model);
    }

    private function sinkronkanStatus(Request $request, PengaduanWarga $model): RedirectResponse
    {
        $current = $model->status_pengaduan;

        if ($current === 'Terkirim') {
            return redirect()->route('pengaduan.index')
                ->with('error', 'Buka pengaduan (ikon mata) terlebih dahulu untuk menandai sebagai diterima.');
        }

        if (in_array($current, ['Selesai', 'Ditolak', 'Dibatalkan'], true)) {
            return redirect()->route('pengaduan.index')
                ->with('error', 'Pengaduan ini sudah tertutup dan tidak dapat diubah.');
        }

        $nextAllowed = self::ALLOWED_TRANSITIONS[$current] ?? [];

        $validatedBase = $request->validate([
            'status_pengaduan' => ['required', Rule::in($nextAllowed)],
        ]);

        $new = $validatedBase['status_pengaduan'];

        $rules = [];

        if ($new === 'Selesai') {
            $rules['catatan_selesai'] = ['required', 'string', 'max:5000'];
            $rules['bukti_selesai'] = ['required', 'file', 'max:10240', 'mimes:jpg,jpeg,png,heic,heif'];
        }

        if ($new === 'Ditolak') {
            $rules['alasan_ditolak'] = ['required', 'string', 'max:5000'];
        }

        $messages = [
            'bukti_selesai.required' => 'Bukti penyelesaian wajib diunggah.',
            'bukti_selesai.uploaded' => 'Gagal mengunggah bukti. Pastikan ukuran file maks. 10 MB (JPG, PNG, atau HEIC).',
            'bukti_selesai.max' => 'Ukuran bukti penyelesaian maksimal 10 MB.',
            'bukti_selesai.mimes' => 'Format bukti harus JPG, JPEG, PNG, atau HEIC.',
            'catatan_selesai.required' => 'Catatan penyelesaian wajib diisi.',
            'alasan_ditolak.required' => 'Alasan penolakan wajib diisi.',
        ];

        /** @var array<string, mixed> */
        $validatedExtra = $rules !== [] ? $request->validate($rules, $messages) : [];

        if ($new === 'Selesai') {
            if ($model->bukti_penyelesaian && Storage::disk('public')->exists($model->bukti_penyelesaian)) {
                Storage::disk('public')->delete($model->bukti_penyelesaian);
            }
            $model->bukti_penyelesaian = $request->file('bukti_selesai')->store('pengaduan_bukti', 'public');
            $model->catatan_selesai = $validatedExtra['catatan_selesai'];
            $model->alasan_ditolak = null;
        } elseif ($new === 'Ditolak') {
            if ($model->bukti_penyelesaian && Storage::disk('public')->exists($model->bukti_penyelesaian)) {
                Storage::disk('public')->delete($model->bukti_penyelesaian);
            }
            $model->bukti_penyelesaian = null;
            $model->catatan_selesai = null;
            $model->alasan_ditolak = $validatedExtra['alasan_ditolak'];
        }

        $model->status_pengaduan = $new;
        $model->save();

        $nomor = $model->nomor_pengaduan;
        LogAktivitasAdmin::catat(
            'mengubah status pengaduan',
            "{$nomor} menjadi {$new}"
        );

        return redirect()->route('pengaduan.index')->with('success', 'Perkembangan pengaduan berhasil disimpan.');
    }
}
