<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataWarga;
use App\Models\PengaduanWarga;
use App\Support\ApiDate;
use App\Support\MediaUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaduanApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        /** @var DataWarga $warga */
        $warga = $request->attributes->get('warga');

        $query = PengaduanWarga::query()
            ->where('referensi_warga_id', (string) $warga->getKey())
            ->orderByDesc('tanggal_dibuat');

        if ($request->filled('status')) {
            $status = $request->string('status')->toString();
            if ($status === 'aktif') {
                $query->whereIn('status_pengaduan', ['Terkirim', 'Diterima', 'Diproses']);
            } elseif ($status === 'selesai') {
                $query->whereIn('status_pengaduan', ['Selesai', 'Ditolak', 'Dibatalkan']);
            } else {
                $query->where('status_pengaduan', $status);
            }
        }

        $items = $query->get()->map(fn (PengaduanWarga $p) => $this->formatPengaduan($p));

        return response()->json(['data' => $items]);
    }

    public function store(Request $request): JsonResponse
    {
        /** @var DataWarga $warga */
        $warga = $request->attributes->get('warga');

        $validated = $request->validate([
            'topik' => ['required', 'in:Infrastruktur,Kebersihan,Keamanan,Sosial,Lainnya'],
            'judul_pengaduan' => ['required', 'string', 'max:200'],
            'deskripsi' => ['required', 'string', 'max:5000'],
            'lokasi_kejadian' => ['required', 'string', 'max:300'],
            'lampiran' => ['nullable', 'array', 'max:3'],
            'lampiran.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,heic,heif'],
        ]);

        $paths = [];
        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $file) {
                $paths[] = $file->store('pengaduan_lampiran', 'public');
            }
        }

        $pengaduan = PengaduanWarga::query()->create([
            'nomor_pengaduan' => $this->generateNomorPengaduan(),
            'referensi_warga_id' => (string) $warga->getKey(),
            'nama_pelapor' => $warga->nama_kepala_keluarga,
            'judul_pengaduan' => $validated['judul_pengaduan'],
            'topik' => $validated['topik'],
            'lokasi_kejadian' => $validated['lokasi_kejadian'],
            'deskripsi' => $validated['deskripsi'],
            'status_pengaduan' => 'Terkirim',
            'lampiran_gambar' => $paths,
        ]);

        return response()->json([
            'message' => 'Pengaduan berhasil dikirim.',
            'data' => $this->formatPengaduan($pengaduan),
        ], 201);
    }

    public function show(Request $request, string $pengaduan): JsonResponse
    {
        /** @var DataWarga $warga */
        $warga = $request->attributes->get('warga');

        $model = PengaduanWarga::query()
            ->where('_id', $pengaduan)
            ->where('referensi_warga_id', (string) $warga->getKey())
            ->firstOrFail();

        return response()->json(['data' => $this->formatPengaduan($model)]);
    }

    public function update(Request $request, string $pengaduan): JsonResponse
    {
        /** @var DataWarga $warga */
        $warga = $request->attributes->get('warga');

        $model = PengaduanWarga::query()
            ->where('_id', $pengaduan)
            ->where('referensi_warga_id', (string) $warga->getKey())
            ->firstOrFail();

        $this->assertStatusTerkirim($model->status_pengaduan, 'Pengaduan');

        $validated = $request->validate([
            'topik' => ['required', 'in:Infrastruktur,Kebersihan,Keamanan,Sosial,Lainnya'],
            'judul_pengaduan' => ['required', 'string', 'max:200'],
            'deskripsi' => ['required', 'string', 'max:5000'],
            'lokasi_kejadian' => ['required', 'string', 'max:300'],
            'lampiran' => ['nullable', 'array', 'max:3'],
            'lampiran.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,heic,heif'],
            'ganti_lampiran' => ['nullable', 'boolean'],
        ]);

        $model->judul_pengaduan = $validated['judul_pengaduan'];
        $model->topik = $validated['topik'];
        $model->lokasi_kejadian = $validated['lokasi_kejadian'];
        $model->deskripsi = $validated['deskripsi'];

        if ($request->boolean('ganti_lampiran') && $request->hasFile('lampiran')) {
            $this->hapusLampiran($model->lampiran_gambar ?? []);
            $paths = [];
            foreach ($request->file('lampiran') as $file) {
                $paths[] = $file->store('pengaduan_lampiran', 'public');
            }
            $model->lampiran_gambar = $paths;
        } elseif ($request->hasFile('lampiran')) {
            $existing = $model->lampiran_gambar ?? [];
            $newFiles = $request->file('lampiran');
            if (count($existing) + count($newFiles) > 3) {
                return response()->json(['message' => 'Maksimal 3 foto lampiran.'], 422);
            }
            foreach ($newFiles as $file) {
                $existing[] = $file->store('pengaduan_lampiran', 'public');
            }
            $model->lampiran_gambar = $existing;
        }

        $model->save();

        return response()->json([
            'message' => 'Pengaduan berhasil diperbarui.',
            'data' => $this->formatPengaduan($model),
        ]);
    }

    public function batalkan(Request $request, string $pengaduan): JsonResponse
    {
        /** @var DataWarga $warga */
        $warga = $request->attributes->get('warga');

        $model = PengaduanWarga::query()
            ->where('_id', $pengaduan)
            ->where('referensi_warga_id', (string) $warga->getKey())
            ->firstOrFail();

        $this->assertStatusTerkirim($model->status_pengaduan, 'Pengaduan');

        $model->status_pengaduan = 'Dibatalkan';
        $model->save();

        return response()->json([
            'message' => 'Pengaduan berhasil dibatalkan.',
            'data' => $this->formatPengaduan($model),
        ]);
    }

    private function assertStatusTerkirim(?string $status, string $label): void
    {
        if ($status !== 'Terkirim') {
            abort(422, "{$label} hanya dapat diubah atau dibatalkan saat status Terkirim.");
        }
    }

    /** @param  array<int, string>  $paths */
    private function hapusLampiran(array $paths): void
    {
        foreach ($paths as $path) {
            if (is_string($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    private function generateNomorPengaduan(): string
    {
        $max = PengaduanWarga::query()
            ->get()
            ->map(function (PengaduanWarga $p) {
                if (preg_match('/ADU-(\d+)/', (string) $p->nomor_pengaduan, $m)) {
                    return (int) $m[1];
                }

                return 0;
            })
            ->max() ?? 0;

        return 'ADU-'.str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
    }

    /** @return array<string, mixed> */
    private function formatPengaduan(PengaduanWarga $p): array
    {
        $lampiran = collect($p->lampiran_gambar ?? [])
            ->map(fn ($path) => MediaUrl::fromPublicDisk(is_string($path) ? $path : null))
            ->filter()
            ->values()
            ->all();

        $bukti = MediaUrl::fromPublicDisk($p->bukti_penyelesaian);

        return [
            'id' => (string) $p->getKey(),
            'nomor_pengaduan' => $p->nomor_pengaduan,
            'judul_pengaduan' => $p->judul_pengaduan,
            'topik' => $p->topik,
            'lokasi_kejadian' => $p->lokasi_kejadian,
            'deskripsi' => $p->deskripsi,
            'status_pengaduan' => $p->status_pengaduan,
            'lampiran_gambar' => $lampiran,
            'catatan_selesai' => $p->catatan_selesai,
            'bukti_penyelesaian' => $bukti,
            'alasan_ditolak' => $p->alasan_ditolak,
            'tanggal_dibuat' => ApiDate::format($p->tanggal_dibuat),
            'can_edit' => $p->status_pengaduan === 'Terkirim',
            'can_batalkan' => $p->status_pengaduan === 'Terkirim',
        ];
    }
}
