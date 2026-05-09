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
  <!-- breadcrumb + header -->
  <div class="page-header">
    <div>
      <div class="breadcrumb">
        <i class="ph ph-house-simple"></i>
        <span>/</span>
        <span>Dashboard</span>
      </div>
      <h1>Dashboard</h1>
      <p>Ringkasan aktivitas RT 05 Malabar Ujung</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
      <span style="font-size:13px;color:var(--gray-400);">
        <i class="ph ph-calendar-blank"></i>
        Rabu, 30 April 2026
      </span>
      <button class="btn btn-blue btn-sm"><i class="ph ph-arrow-clockwise"></i> Refresh</button>
    </div>
  </div>

  <!-- Stat Cards -->
  <div class="stats-grid mb-24">
    <div class="stat-card blue">
      <div class="stat-icon-wrap blue"><i class="ph ph-users-three"></i></div>
      <div class="stat-value">128</div>
      <div class="stat-label">Total Warga Terdaftar</div>
      <div class="stat-change up"><i class="ph ph-trend-up"></i> +3 bulan ini</div>
    </div>
    <div class="stat-card green">
      <div class="stat-icon-wrap green"><i class="ph ph-clipboard-text"></i></div>
      <div class="stat-value">34</div>
      <div class="stat-label">Total Pengaduan</div>
      <div class="stat-change up"><i class="ph ph-trend-up"></i> +5 bulan ini</div>
    </div>
    <div class="stat-card yellow">
      <div class="stat-icon-wrap yellow"><i class="ph ph-clock"></i></div>
      <div class="stat-value">3</div>
      <div class="stat-label">Pengaduan Aktif</div>
      <div class="stat-change down"><i class="ph ph-trend-down"></i> -2 dari kemarin</div>
    </div>
    <div class="stat-card orange">
      <div class="stat-icon-wrap orange"><i class="ph ph-files"></i></div>
      <div class="stat-value">12</div>
      <div class="stat-label">Total Dokumen</div>
      <div class="stat-change up"><i class="ph ph-trend-up"></i> +1 bulan ini</div>
    </div>
  </div>

  <!-- Main Grid -->
  <div class="dashboard-grid">
    <!-- LEFT -->
    <div style="display:flex;flex-direction:column;gap:20px;">

      <!-- Chart Card -->
      <div class="card">
        <div class="card-header">
          <span class="card-title">Pengaduan per Bulan</span>
          <div class="tab-bar" style="margin-bottom:0">
            <div class="tab-item active">2026</div>
            <div class="tab-item">2025</div>
          </div>
        </div>
        <div class="card-body">
          <div class="chart-placeholder" id="chart">
            <!-- chart bars rendered by JS -->
          </div>
        </div>
      </div>

      <!-- Recent Pengaduan -->
      <div class="card">
        <div class="card-header">
          <span class="card-title">Pengaduan Terbaru</span>
          <a href="pengaduan.html" class="btn btn-outline btn-sm">Lihat Semua</a>
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
              <tbody id="recentTable">
                <!-- JS rendered -->
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT -->
    <div style="display:flex;flex-direction:column;gap:20px;">

      <!-- Info RT Card -->
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

      <!-- Aktivitas Terkini -->
      <div class="card">
        <div class="card-header" style="margin-bottom:4px;">
          <span class="card-title">Aktivitas Terkini</span>
        </div>
        <div class="card-body">
          <div id="activityList"><!-- JS rendered --></div>
        </div>
      </div>

      <!-- Pengaduan Status Donut-like -->
      <div class="card">
        <div class="card-header" style="margin-bottom:8px;">
          <span class="card-title">Status Pengaduan</span>
        </div>
        <div class="card-body">
          <div style="display:flex;flex-direction:column;gap:10px;">
            <div>
              <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:5px;">
                <span style="color:var(--gray-600);">Selesai</span>
                <span style="font-weight:700;color:var(--green-success);">28</span>
              </div>
              <div style="height:6px;background:var(--gray-100);border-radius:99px;overflow:hidden;">
                <div style="height:100%;width:82%;background:var(--green-success);border-radius:99px;"></div>
              </div>
            </div>
            <div>
              <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:5px;">
                <span style="color:var(--gray-600);">Diproses</span>
                <span style="font-weight:700;color:var(--blue-primary);">3</span>
              </div>
              <div style="height:6px;background:var(--gray-100);border-radius:99px;overflow:hidden;">
                <div style="height:100%;width:9%;background:var(--blue-primary);border-radius:99px;"></div>
              </div>
            </div>
            <div>
              <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:5px;">
                <span style="color:var(--gray-600);">Diterima</span>
                <span style="font-weight:700;color:var(--orange-warn);">2</span>
              </div>
              <div style="height:6px;background:var(--gray-100);border-radius:99px;overflow:hidden;">
                <div style="height:100%;width:6%;background:var(--orange-warn);border-radius:99px;"></div>
              </div>
            </div>
            <div>
              <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:5px;">
                <span style="color:var(--gray-600);">Terkirim</span>
                <span style="font-weight:700;color:var(--gray-400);">1</span>
              </div>
              <div style="height:6px;background:var(--gray-100);border-radius:99px;overflow:hidden;">
                <div style="height:100%;width:3%;background:var(--gray-400);border-radius:99px;"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="{{ asset('js/parete.js') }}"></script>
<script>
  initLayout('dashboard', 'Dashboard');

  /* Render chart bars */
  const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
  const data   = [2,5,8,3,6,4,0,0,0,0,0,0];
  const maxVal = Math.max(...data, 1);
  const chart  = document.getElementById('chart');
  if (chart) {
    chart.innerHTML = months.map((m, i) => {
      const h = data[i] ? Math.max(20, Math.round((data[i]/maxVal)*140)) : 4;
      const color = data[i] ? 'var(--blue-primary)' : 'var(--gray-200)';
      return `
        <div class="chart-bar-wrap">
          <div style="font-size:11px;color:var(--blue-dark);font-weight:700;margin-bottom:4px;">${data[i]||''}</div>
          <div style="height:${h}px;width:100%;background:${color};border-radius:4px 4px 0 0;opacity:${data[i]?0.85:1};transition:opacity 0.2s;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity='${data[i]?0.85:1}'"></div>
          <div class="chart-bar-label">${m}</div>
        </div>`;
    }).join('');
  }

  /* Recent pengaduan */
  const recentData = [
    { id:'ADU-031', judul:'Lampu Jalan Gang Mawar Mati',   topik:'Infrastruktur', tanggal:'5 Mar 2025', status:'Diproses' },
    { id:'ADU-023', judul:'Sampah Menumpuk di Ujung Gang', topik:'Kebersihan',    tanggal:'1 Mar 2025', status:'Diterima' },
    { id:'ADU-019', judul:'Jalan Berlubang Depan No. 12',  topik:'Infrastruktur', tanggal:'14 Feb 2025',status:'Selesai'  },
  ];

  const tbody = document.getElementById('recentTable');
  if (tbody) {
    tbody.innerHTML = recentData.map(r => `
      <tr>
        <td><span style="font-size:12px;font-weight:700;color:var(--gray-400);">#${r.id}</span></td>
        <td style="font-weight:500;max-width:200px;">${r.judul}</td>
        <td><span style="font-size:12px;color:var(--gray-500);">${r.topik}</span></td>
        <td style="font-size:12px;color:var(--gray-400);">${r.tanggal}</td>
        <td>${statusBadge(r.status)}</td>
        <td>
          <a href="pengaduan-detail.html" class="btn btn-outline btn-sm btn-icon" title="Lihat Detail">
            <i class="ph ph-arrow-right"></i>
          </a>
        </td>
      </tr>`).join('');
  }

  /* Activity */
  const activities = [
    { color:'blue',   icon:'ph-clipboard-text', label:'Pengaduan baru diterima', sub:'#ADU-031 · Infrastruktur',    time:'09.30' },
    { color:'green',  icon:'ph-check-circle',   label:'Pengaduan diselesaikan',  sub:'#ADU-019 · Infrastruktur',    time:'08.12' },
    { color:'orange', icon:'ph-bell',           label:'Notifikasi dikirim',       sub:'Jadwal Rapat Bulanan',        time:'Kemarin' },
    { color:'yellow', icon:'ph-file-arrow-up',  label:'Dokumen baru diunggah',   sub:'Peraturan RT 05 (2026)',      time:'2 hr lalu' },
  ];

  const actList = document.getElementById('activityList');
  if (actList) {
    actList.innerHTML = activities.map(a => `
      <div class="activity-item">
        <div class="activity-dot ${a.color}"></div>
        <div class="activity-text">
          <strong>${a.label}</strong>
          <p>${a.sub}</p>
        </div>
        <span class="activity-time">${a.time}</span>
      </div>`).join('');
  }
</script>
</body>
</html>