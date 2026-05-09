<?php

namespace App\Http\Controllers;

use App\Models\DataWarga;
use App\Models\DokumenRt;
use App\Models\LogAktivitasAdmin;
use App\Models\PengaduanWarga;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $year = (int) now()->year;

        $totalWarga = DataWarga::query()->count();
        $totalPengaduan = PengaduanWarga::query()->count();

        $pengPerBulan = array_fill(0, 12, 0);

        PengaduanWarga::query()
            ->whereNotNull('tanggal_dibuat')
            ->get(['tanggal_dibuat'])
            ->each(function (PengaduanWarga $p) use (&$pengPerBulan, $year): void {
                $d = $p->tanggal_dibuat;
                if ($d instanceof Carbon && (int) $d->year === $year) {
                    $pengPerBulan[(int) $d->format('n') - 1]++;
                }
            });

        $pengTerbaru = PengaduanWarga::query()
            ->whereNotNull('tanggal_dibuat')
            ->orderByDesc('tanggal_dibuat')
            ->limit(5)
            ->get();

        $totalDokumen = DokumenRt::query()->count();

        $bulanIni = now()->copy()->startOfMonth();
        $wargaBulanIni = DataWarga::query()->where('tanggal_dibuat', '>=', $bulanIni)->count();
        $pengaduanBulanIni = PengaduanWarga::query()->where('tanggal_dibuat', '>=', $bulanIni)->count();
        $dokumenBulanIni = DokumenRt::query()->where('tanggal_dibuat', '>=', $bulanIni)->count();

        $pengaktif = PengaduanWarga::query()
            ->whereIn('status_pengaduan', ['Terkirim', 'Diterima', 'Diproses'])
            ->count();

        $selesai = PengaduanWarga::query()->where('status_pengaduan', 'Selesai')->count();
        $diproses = PengaduanWarga::query()->where('status_pengaduan', 'Diproses')->count();
        $diterima = PengaduanWarga::query()->where('status_pengaduan', 'Diterima')->count();
        $terkirim = PengaduanWarga::query()->where('status_pengaduan', 'Terkirim')->count();
        $ditolak = PengaduanWarga::query()->where('status_pengaduan', 'Ditolak')->count();

        $statusTotals = compact('terkirim', 'diterima', 'diproses', 'selesai', 'ditolak');

        $logAktivitas = LogAktivitasAdmin::query()
            ->orderByDesc('waktu')
            ->limit(25)
            ->get();

        return view('dashboard', [
            'currentYear' => $year,
            'chartData' => $pengPerBulan,
            'totalWarga' => $totalWarga,
            'totalPengaduan' => $totalPengaduan,
            'pengaktif' => $pengaktif,
            'totalDokumen' => $totalDokumen,
            'wargaBulanIni' => $wargaBulanIni,
            'pengaduanBulanIni' => $pengaduanBulanIni,
            'dokumenBulanIni' => $dokumenBulanIni,
            'pengTerbaru' => $pengTerbaru,
            'statusTotals' => $statusTotals,
            'logAktivitas' => $logAktivitas,
        ]);
    }
}
