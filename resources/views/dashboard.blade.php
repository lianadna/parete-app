<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard</title>
  <link rel="stylesheet" href="{{ asset('css/parete.css') }}" />
  <script src="https://unpkg.com/@phosphor-icons/web@2.1.1/src/index.js" defer></script>
</head>
<body>

<div id="app">
  <div class="page-header" style="flex-wrap:wrap;align-items:flex-start;">
    <div>
      <div class="breadcrumb">
        <i class="ph ph-house-simple"></i>
        <span>/</span>
        <span>Dashboard</span>
      </div>
      <h1>Dashboard</h1>
      <p>Ringkasan aktivitas RT 05 Malabar Ujung</p>
    </div>
    <div class="dash-clock" aria-live="polite">
      <div class="dash-clock-kicker"><i class="ph ph-clock" style="margin-right:4px;"></i> Waktu sekarang (WIB)</div>
      <div id="dashClockTime" class="dash-clock-time">--:--:--</div>
      <div id="dashClockDate" class="dash-clock-date">—</div>
    </div>
  </div>

  @php
    $pct = static function ($n) use ($totalPengaduan): int {
      if ($totalPengaduan <= 0) return 0;
      return (int) min(100, max(5, round(100 * (int) $n / max(1, $totalPengaduan))));
    };
    $tz = config('app.timezone');
  @endphp

  <div class="stats-grid mb-24">
    <div class="stat-card blue">
      <div class="stat-icon-wrap blue"><i class="ph ph-users-three"></i></div>
      <div class="stat-value">{{ $totalWarga }}</div>
      <div class="stat-label">Total Warga Terdaftar</div>
      @if(($wargaBulanIni ?? 0) > 0)
      <div class="stat-change up"><i class="ph ph-trend-up"></i> +{{ $wargaBulanIni }} bulan ini</div>
      @else
      <div class="stat-change" style="color:var(--gray-400);"><i class="ph ph-dash"></i> Belum ada pendaftar baru</div>
      @endif
    </div>
    <div class="stat-card green">
      <div class="stat-icon-wrap green"><i class="ph ph-clipboard-text"></i></div>
      <div class="stat-value">{{ $totalPengaduan }}</div>
      <div class="stat-label">Total Pengaduan</div>
      @if(($pengaduanBulanIni ?? 0) > 0)
      <div class="stat-change up"><i class="ph ph-trend-up"></i> +{{ $pengaduanBulanIni }} bulan ini</div>
      @else
      <div class="stat-change" style="color:var(--gray-400);"><i class="ph ph-dash"></i> Tidak ada pengaduan bulan ini</div>
      @endif
    </div>
    <div class="stat-card yellow">
      <div class="stat-icon-wrap yellow"><i class="ph ph-clock"></i></div>
      <div class="stat-value">{{ $pengaktif }}</div>
      <div class="stat-label">Pengaduan Aktif</div>
      <div class="stat-change" style="color:var(--gray-600);"><i class="ph ph-info"></i> Terkirim, diterima, atau diproses</div>
    </div>
    <div class="stat-card orange">
      <div class="stat-icon-wrap orange"><i class="ph ph-files"></i></div>
      <div class="stat-value">{{ $totalDokumen }}</div>
      <div class="stat-label">Total Dokumen</div>
      @if(($dokumenBulanIni ?? 0) > 0)
      <div class="stat-change up"><i class="ph ph-trend-up"></i> +{{ $dokumenBulanIni }} bulan ini</div>
      @else
      <div class="stat-change" style="color:var(--gray-400);"><i class="ph ph-dash"></i> Tidak ada unggahan bulan ini</div>
      @endif
    </div>
  </div>

  <div class="dashboard-shell">
    <div class="dashboard-grid">
      <div style="display:flex;flex-direction:column;gap:20px;min-width:0;">

        <div class="card">
          <div class="card-header">
            <span class="card-title">Pengaduan per Bulan ({{ $currentYear }})</span>
          </div>
          <div class="card-body">
            <div class="chart-placeholder" id="chart"></div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <span class="card-title">Pengaduan Terbaru</span>
            <a href="{{ route('pengaduan.index') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
          </div>
          <div class="card-body" style="padding-top:12px;">
            <div class="table-wrap">
              <table>
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Topik</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($pengTerbaru as $p)
                    <tr>
                      <td><span style="font-size:12px;font-weight:700;color:var(--gray-400);">#{{ $p->nomor_pengaduan }}</span></td>
                      <td style="font-weight:500;max-width:200px;">{{ $p->judul_pengaduan }}</td>
                      <td><span style="font-size:12px;color:var(--gray-500);">{{ $p->topik }}</span></td>
                      <td style="font-size:12px;color:var(--gray-400);">{{ optional($p->tanggal_dibuat)->format('d M Y') ?? '-' }}</td>
                      <td>@include('partials.badge-status', ['status' => $p->status_pengaduan])</td>
                      <td>
                        <a href="{{ route('pengaduan.index') }}" class="btn btn-outline btn-sm btn-icon" title="Lihat di halaman Pengaduan">
                          <i class="ph ph-arrow-right"></i>
                        </a>
                      </td>
                    </tr>
                  @empty
                    <tr><td colspan="6"><div class="empty-state"><div class="empty-title">Belum ada pengaduan</div></div></td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div style="display:flex;flex-direction:column;gap:20px;min-width:0;">
        <div class="card">
          <div class="card-header" style="margin-bottom:4px;">
            <span class="card-title">Info RT</span>
            <span class="badge badge-green">Aktif</span>
          </div>
          <div class="card-body">
            <div style="
              background:linear-gradient(135deg,var(--blue-darker),var(--blue-primary));
              border-radius:var(--radius-md);
              padding:16px;
              color:white;
              margin-bottom:16px;
              position:relative;
              overflow:hidden;
            ">
              <div style="font-family:var(--font-display);font-size:22px;font-weight:800;letter-spacing:-0.5px;">RT 05</div>
              <div style="font-size:13px;opacity:0.8;margin-top:2px;">Malabar Ujung</div>
              <div style="position:absolute;right:-20px;top:-20px;width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,0.08);"></div>
            </div>
            <div class="info-rows">
              <div class="info-row">
                <span class="info-row-label"><i class="ph ph-user-gear icon"></i> Ketua RT</span>
                <span class="info-row-value">Pak Budi Santoso</span>
              </div>
              <div class="info-row">
                <span class="info-row-label"><i class="ph ph-house icon"></i> Jumlah KK</span>
                <span class="info-row-value">47 KK</span>
              </div>
              <div class="info-row">
                <span class="info-row-label"><i class="ph ph-users icon"></i> Warga</span>
                <span class="info-row-value">128 Orang</span>
              </div>
              <div class="info-row">
                <span class="info-row-label"><i class="ph ph-map-pin icon"></i> Wilayah</span>
                <span class="info-row-value">RW 03</span>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header" style="margin-bottom:8px;">
            <span class="card-title">Status Pengaduan</span>
          </div>
          <div class="card-body">
            <div style="display:flex;flex-direction:column;gap:10px;">
              @foreach([
                ['Selesai', $statusTotals['selesai'], 'var(--green-success)'],
                ['Diproses', $statusTotals['diproses'], 'var(--blue-primary)'],
                ['Diterima', $statusTotals['diterima'], 'var(--orange-warn)'],
                ['Terkirim', $statusTotals['terkirim'], 'var(--gray-400)'],
                ['Ditolak', $statusTotals['ditolak'], '#C62828'],
                ['Dibatalkan', $statusTotals['dibatalkan'], '#607D8B'],
              ] as [$label, $nilai, $warna])
                <div>
                  <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:5px;">
                    <span style="color:var(--gray-600);">{{ $label }}</span>
                    <span style="font-weight:700;color:{{ $warna }};">{{ $nilai }}</span>
                  </div>
                  <div style="height:6px;background:var(--gray-100);border-radius:99px;overflow:hidden;">
                    <div style="height:100%;width:{{ $pct($nilai) }}%;background:{{ $warna }};border-radius:99px;"></div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header" style="margin-bottom:8px;">
        <span class="card-title"><i class="ph ph-scroll" style="margin-right:6px;"></i> Log aktivitas</span>
      </div>
      <div class="card-body" style="padding-top:0;">
        <div class="table-wrap" style="border-radius:var(--radius-sm);overflow:hidden;border:1px solid var(--gray-100);">
          <table class="dashboard-log-table">
            <thead>
              <tr>
                <th class="log-col-aksi">Aktivitas</th>
                <th class="log-col-time">Waktu (WIB)</th>
              </tr>
            </thead>
            <tbody>
              @forelse($logAktivitas as $log)
                <tr>
                  <td class="log-col-aksi">{{ $log->rangkaianAktivitas() }}</td>
                  <td class="log-col-time">{{ optional($log->waktu)->timezone($tz)->format('d/m/Y H:i:s') ?? '—' }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="2">
                    <div class="empty-state" style="padding:28px;"><div class="empty-title">Belum ada aktivitas yang tercatat</div>
                      <p style="font-size:13px;color:var(--gray-400);margin-top:8px;">Log akan terisi ketika Anda menambah, mengubah, atau menghapus data dari menu aplikasi.</p>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="{{ asset('js/parete.js') }}"></script>
<script>
  initLayout('dashboard', 'Dashboard');

  /* Jam digital + tanggal (zona Asia/Jakarta, WIB) */
  (function dashClock(){
    var elT = document.getElementById('dashClockTime');
    var elD = document.getElementById('dashClockDate');
    if (!elT || !elD) return;
    var TZ = 'Asia/Jakarta';
    function tick(){
      var now = new Date();
      elT.textContent = new Intl.DateTimeFormat('en-GB', {
        timeZone: TZ,
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
      }).format(now);
      elD.textContent = new Intl.DateTimeFormat('id-ID', {
        timeZone: TZ,
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
      }).format(now);
    }
    tick();
    setInterval(tick, 1000);
  })();

  const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
  const data   = @json($chartData);
  const maxVal = Math.max(...data, 1);
  const chart  = document.getElementById('chart');
  if (chart) {
    chart.innerHTML = months.map((m, i) => {
      const h = data[i] ? Math.max(20, Math.round((data[i]/maxVal)*140)) : 4;
      const color = data[i] ? 'var(--blue-primary)' : 'var(--gray-200)';
      return (
        '<div class="chart-bar-wrap">' +
          '<div style="font-size:11px;color:var(--blue-dark);font-weight:700;margin-bottom:4px;">' + (data[i]||'') + '</div>' +
          '<div style="height:' + h + 'px;width:100%;background:' + color + ';border-radius:4px 4px 0 0;opacity:' + (data[i]?0.85:1) + ';transition:opacity 0.2s;"></div>' +
          '<div class="chart-bar-label">' + m + '</div>' +
        '</div>'
      );
    }).join('');
  }
</script>
</body>
</html>
