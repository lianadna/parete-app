<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pengaduan</title>
  <link rel="stylesheet" href="{{ asset('css/parete.css') }}" />
  <script src="https://unpkg.com/@phosphor-icons/web@2.1.1/src/index.js" defer></script>
</head>
<body>

<div id="app">

  <!-- Page Header -->
  <div class="page-header">
    <div>
      <div class="breadcrumb">
        <i class="ph ph-house-simple"></i><span>/</span><span>Pengaduan</span>
      </div>
      <h1>Pengaduan Warga</h1>
      <p>Kelola dan tindak lanjuti semua laporan dari warga</p>
    </div>
    <button class="btn btn-blue" onclick="openModal('addModal')">
      <i class="ph ph-plus"></i> Tambah Pengaduan
    </button>
  </div>

  <!-- Stat mini row -->
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;">
    <div style="background:white;border-radius:var(--radius-md);padding:14px 18px;border:1px solid var(--gray-100);display:flex;align-items:center;gap:12px;">
      <div style="width:36px;height:36px;border-radius:8px;background:var(--gray-100);display:flex;align-items:center;justify-content:center;font-size:18px;color:var(--gray-600);"><i class="ph ph-list-bullets"></i></div>
      <div><div style="font-size:20px;font-weight:800;font-family:var(--font-display);color:var(--gray-900);">34</div><div style="font-size:11px;color:var(--gray-400);">Total</div></div>
    </div>
    <div style="background:white;border-radius:var(--radius-md);padding:14px 18px;border:1px solid var(--gray-100);display:flex;align-items:center;gap:12px;">
      <div style="width:36px;height:36px;border-radius:8px;background:var(--blue-light);display:flex;align-items:center;justify-content:center;font-size:18px;color:var(--blue-primary);"><i class="ph ph-spinner-gap"></i></div>
      <div><div style="font-size:20px;font-weight:800;font-family:var(--font-display);color:var(--blue-primary);">3</div><div style="font-size:11px;color:var(--gray-400);">Diproses</div></div>
    </div>
    <div style="background:white;border-radius:var(--radius-md);padding:14px 18px;border:1px solid var(--gray-100);display:flex;align-items:center;gap:12px;">
      <div style="width:36px;height:36px;border-radius:8px;background:var(--orange-light);display:flex;align-items:center;justify-content:center;font-size:18px;color:var(--orange-warn);"><i class="ph ph-clock"></i></div>
      <div><div style="font-size:20px;font-weight:800;font-family:var(--font-display);color:var(--orange-warn);">2</div><div style="font-size:11px;color:var(--gray-400);">Diterima</div></div>
    </div>
    <div style="background:white;border-radius:var(--radius-md);padding:14px 18px;border:1px solid var(--gray-100);display:flex;align-items:center;gap:12px;">
      <div style="width:36px;height:36px;border-radius:8px;background:var(--green-light);display:flex;align-items:center;justify-content:center;font-size:18px;color:var(--green-success);"><i class="ph ph-check-circle"></i></div>
      <div><div style="font-size:20px;font-weight:800;font-family:var(--font-display);color:var(--green-success);">28</div><div style="font-size:11px;color:var(--gray-400);">Selesai</div></div>
    </div>
  </div>

  <!-- Card -->
  <div class="card">
    <div class="card-header">
      <span class="card-title">Daftar Pengaduan</span>
    </div>
    <div class="card-body">

      <!-- Tab + filter -->
      <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
        <div class="tab-bar" style="margin-bottom:0">
          <div class="tab-item active" onclick="filterTab('semua',this)">Semua</div>
          <div class="tab-item" onclick="filterTab('aktif',this)">Aktif</div>
          <div class="tab-item" onclick="filterTab('selesai',this)">Selesai</div>
        </div>
        <div class="filter-bar" style="margin-bottom:0">
          <div class="search-input-bar">
            <i class="ph ph-magnifying-glass icon"></i>
            <input type="search" placeholder="Cari pengaduan..." oninput="filterTable(this.value)" />
          </div>
          <select class="filter-select" onchange="filterTopik(this.value)">
            <option value="">Semua Topik</option>
            <option>Infrastruktur</option>
            <option>Kebersihan</option>
            <option>Keamanan</option>
            <option>Sosial</option>
            <option>Lainnya</option>
          </select>
          <select class="filter-select" onchange="filterStatus(this.value)">
            <option value="">Semua Status</option>
            <option>Terkirim</option>
            <option>Diterima</option>
            <option>Diproses</option>
            <option>Selesai</option>
          </select>
        </div>
      </div>

      <!-- Table -->
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>No. ADU</th>
              <th>Warga</th>
              <th>Judul</th>
              <th>Topik</th>
              <th>Lokasi</th>
              <th>Tanggal</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="pengaduanTable"><!-- JS --></tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div style="display:flex;align-items:center;justify-content:space-between;padding-top:16px;flex-wrap:wrap;gap:12px;">
        <span style="font-size:13px;color:var(--gray-400);">Menampilkan 1–4 dari 34 pengaduan</span>
        <div class="pagination">
          <button class="page-btn"><i class="ph ph-caret-left"></i></button>
          <button class="page-btn active">1</button>
          <button class="page-btn">2</button>
          <button class="page-btn">3</button>
          <span style="color:var(--gray-300);padding:0 4px;">…</span>
          <button class="page-btn">9</button>
          <button class="page-btn"><i class="ph ph-caret-right"></i></button>
        </div>
      </div>
    </div>
  </div>

</div><!-- #app -->

<!-- ── Modal: Detail Pengaduan ─────────────────────── -->
<div class="modal-overlay" id="detailModal" style="display:none;">
  <div class="modal" style="max-width:700px;">
    <div class="modal-header">
      <h3>Detail Pengaduan</h3>
      <button class="modal-close" onclick="closeModal('detailModal')">×</button>
    </div>
    <div class="modal-body">
      <!-- ── Header info ── -->
      <div style="background:var(--gray-50);border-radius:var(--radius-md);padding:16px;margin-bottom:20px;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px;">
          <div>
            <span style="font-size:12px;font-weight:700;color:var(--gray-400);" id="dAduId">#ADU-031</span>
            <div style="font-family:var(--font-display);font-size:18px;font-weight:700;color:var(--gray-900);margin-top:4px;" id="dJudul">Lampu Jalan Gang Mawar Mati</div>
          </div>
          <div id="dStatus"></div>
        </div>
        <div style="display:flex;gap:20px;margin-top:12px;flex-wrap:wrap;">
          <span style="font-size:12px;color:var(--gray-500);display:flex;align-items:center;gap:5px;"><i class="ph ph-tag"></i> <span id="dTopik">Infrastruktur</span></span>
          <span style="font-size:12px;color:var(--gray-500);display:flex;align-items:center;gap:5px;"><i class="ph ph-map-pin"></i> <span id="dLokasi">Gang Mawar RT 05</span></span>
          <span style="font-size:12px;color:var(--gray-500);display:flex;align-items:center;gap:5px;"><i class="ph ph-calendar"></i> <span id="dTanggal">5 Mar 2025</span></span>
          <span style="font-size:12px;color:var(--gray-500);display:flex;align-items:center;gap:5px;"><i class="ph ph-user"></i> <span id="dWarga">Yulian Adiprana</span></span>
        </div>
      </div>

      <!-- Grid: isi + timeline -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
        <!-- Left -->
        <div>
          <div style="font-size:13px;font-weight:700;color:var(--gray-700);margin-bottom:8px;">Deskripsi</div>
          <p style="font-size:13px;color:var(--gray-600);line-height:1.7;" id="dDeskripsi">
            Lampu penerangan di gang mawar RT 05 sudah tidak menyala sejak 3 hari yang lalu dan membuat warga khawatir saat malam hari.
          </p>

          <div style="font-size:13px;font-weight:700;color:var(--gray-700);margin:16px 0 8px;">Foto Lampiran</div>
          <div class="foto-grid">
            <div class="foto-item">🌃</div>
            <div class="foto-item">🔦</div>
          </div>

          <!-- Respon Admin -->
          <div style="font-size:13px;font-weight:700;color:var(--gray-700);margin:16px 0 8px;">Tanggapan Admin</div>
          <div class="respon-box" id="dRespon">
            <div class="respon-header">
              <span class="respon-name">Admin RT</span>
              <span class="respon-date">7 Mar 2025 · 09.30</span>
            </div>
            <p class="respon-text">Tim teknis sedang meninjau lokasi kejadian dan akan segera diperbaiki.</p>
          </div>
        </div>

        <!-- Right: Timeline -->
        <div>
          <div style="font-size:13px;font-weight:700;color:var(--gray-700);margin-bottom:12px;">Riwayat Status</div>
          <div class="status-timeline">
            <div class="timeline-item">
              <div class="timeline-dot done"><i class="ph ph-spinner-gap"></i></div>
              <div class="timeline-content">
                <div class="timeline-label" style="color:var(--blue-primary);">Diproses</div>
                <div class="timeline-time">7 Mar 2025 · 09:30</div>
                <div class="timeline-desc">Tim teknis sedang meninjau lokasi kejadian.</div>
              </div>
            </div>
            <div class="timeline-item">
              <div class="timeline-dot done"><i class="ph ph-clock"></i></div>
              <div class="timeline-content">
                <div class="timeline-label" style="color:var(--orange-warn);">Diterima</div>
                <div class="timeline-time">6 Mar 2025 · 14:15</div>
                <div class="timeline-desc">Pengaduan telah diterima dan dicatat oleh Admin RT.</div>
              </div>
            </div>
            <div class="timeline-item">
              <div class="timeline-dot done"><i class="ph ph-paper-plane-tilt"></i></div>
              <div class="timeline-content">
                <div class="timeline-label" style="color:var(--green-success);">Terkirim</div>
                <div class="timeline-time">5 Mar 2025 · 20:12</div>
                <div class="timeline-desc">Pengaduan berhasil dikirimkan oleh warga.</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Update status -->
      <div style="border-top:1px solid var(--gray-100);margin-top:20px;padding-top:20px;">
        <div style="font-size:13px;font-weight:700;color:var(--gray-700);margin-bottom:12px;">Update Status & Tanggapan</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
          <div>
            <label class="form-label">Status Baru</label>
            <select class="filter-select" style="width:100%;height:42px;">
              <option>Terkirim</option>
              <option>Diterima</option>
              <option selected>Diproses</option>
              <option>Selesai</option>
            </select>
          </div>
          <div>
            <label class="form-label">Assignee</label>
            <select class="filter-select" style="width:100%;height:42px;">
              <option>Tim Teknis</option>
              <option>Admin RT</option>
            </select>
          </div>
        </div>
        <div style="margin-bottom:12px;">
          <label class="form-label">Tanggapan / Catatan</label>
          <textarea class="form-control-plain" rows="3" placeholder="Tulis tanggapan untuk warga..." style="height:auto;padding:12px;resize:vertical;border-radius:var(--radius-sm);"></textarea>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
          <button class="btn btn-outline" onclick="closeModal('detailModal')">Batal</button>
          <button class="btn btn-blue" onclick="saveStatus()"><i class="ph ph-floppy-disk"></i> Simpan Perubahan</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ── Modal: Tambah Pengaduan ──────────────────────── -->
<div class="modal-overlay" id="addModal" style="display:none;">
  <div class="modal">
    <div class="modal-header">
      <h3>Tambah Pengaduan</h3>
      <button class="modal-close" onclick="closeModal('addModal')">×</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Warga Pengadu *</label>
        <select class="filter-select" style="width:100%;height:42px;">
          <option value="">Pilih warga...</option>
          <option>Yulian Adiprana — No. 87</option>
          <option>Budi Santoso — No. 12</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Topik *</label>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
          <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;padding:6px 12px;border:1.5px solid var(--blue-primary);border-radius:99px;color:var(--blue-primary);font-weight:600;">
            <input type="radio" name="topik" style="display:none;" checked> Infrastruktur
          </label>
          <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;padding:6px 12px;border:1.5px solid var(--gray-200);border-radius:99px;color:var(--gray-600);">
            <input type="radio" name="topik" style="display:none;"> Kebersihan
          </label>
          <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;padding:6px 12px;border:1.5px solid var(--gray-200);border-radius:99px;color:var(--gray-600);">
            <input type="radio" name="topik" style="display:none;"> Keamanan
          </label>
          <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;padding:6px 12px;border:1.5px solid var(--gray-200);border-radius:99px;color:var(--gray-600);">
            <input type="radio" name="topik" style="display:none;"> Sosial
          </label>
          <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;padding:6px 12px;border:1.5px solid var(--gray-200);border-radius:99px;color:var(--gray-600);">
            <input type="radio" name="topik" style="display:none;"> Lainnya
          </label>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Judul Pengaduan *</label>
        <input type="text" class="form-control-plain" placeholder="Masukkan judul..." style="border-radius:var(--radius-sm);" />
      </div>
      <div class="form-group">
        <label class="form-label">Deskripsi *</label>
        <textarea class="form-control-plain" rows="4" placeholder="Jelaskan masalah secara detail..." style="height:auto;padding:12px;resize:vertical;border-radius:var(--radius-sm);"></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Lokasi *</label>
        <input type="text" class="form-control-plain" placeholder="Contoh: Gang Mawar RT 05" style="border-radius:var(--radius-sm);" />
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
        <button class="btn btn-outline" onclick="closeModal('addModal')">Batal</button>
        <button class="btn btn-blue" onclick="submitAdd()"><i class="ph ph-paper-plane-tilt"></i> Kirim</button>
      </div>
    </div>
  </div>
</div>

<script src="{{ asset('js/parete.js') }}"></script>
<script>
  initLayout('pengaduan', 'Pengaduan Warga');

  const pengaduanData = [
    { id:'ADU-031', warga:'Yulian Adiprana', judul:'Lampu Jalan Gang Mawar Mati',   topik:'Infrastruktur', lokasi:'Gang Mawar', tanggal:'5 Mar 2025', status:'Diproses' },
    { id:'ADU-023', warga:'Budi Hartono',    judul:'Sampah Menumpuk di Ujung Gang', topik:'Kebersihan',    lokasi:'Ujung Gang',  tanggal:'1 Mar 2025', status:'Diterima' },
    { id:'ADU-019', warga:'Siti Rahayuni',   judul:'Jalan Berlubang Depan No. 12',  topik:'Infrastruktur', lokasi:'No.12',       tanggal:'14 Feb 2025',status:'Selesai'  },
    { id:'ADU-011', warga:'Ahmad Fauzan',    judul:'Kebisingan di Malam Hari',      topik:'Keamanan',      lokasi:'Gang Melati', tanggal:'1 Feb 2025', status:'Selesai'  },
  ];

  let currentData = [...pengaduanData];

  function renderTable(data) {
    const tbody = document.getElementById('pengaduanTable');
    if (!data.length) {
      tbody.innerHTML = `<tr><td colspan="8"><div class="empty-state"><div class="empty-icon">📋</div><div class="empty-title">Tidak ada pengaduan</div><div class="empty-desc">Belum ada data yang cocok dengan filter.</div></div></td></tr>`;
      return;
    }
    tbody.innerHTML = data.map(r => `
      <tr>
        <td><span style="font-size:12px;font-weight:700;color:var(--gray-400);">#${r.id}</span></td>
        <td>
          <div style="display:flex;align-items:center;gap:8px;">
            <div style="width:28px;height:28px;border-radius:50%;background:var(--blue-light);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:var(--blue-dark);">${r.warga.charAt(0)}</div>
            <span style="font-size:13px;">${r.warga}</span>
          </div>
        </td>
        <td style="font-weight:500;max-width:180px;">${r.judul}</td>
        <td><span style="font-size:12px;color:var(--gray-500);">${r.topik}</span></td>
        <td style="font-size:12px;color:var(--gray-500);">${r.lokasi}</td>
        <td style="font-size:12px;color:var(--gray-400);">${r.tanggal}</td>
        <td>${statusBadge(r.status)}</td>
        <td>
          <div style="display:flex;gap:4px;">
            <button class="btn btn-outline btn-sm btn-icon" title="Detail" onclick="showDetail('${r.id}')"><i class="ph ph-eye"></i></button>
            <button class="btn btn-outline btn-sm btn-icon" title="Edit"><i class="ph ph-pencil"></i></button>
            <button class="btn btn-danger-outline btn-sm btn-icon" title="Hapus"><i class="ph ph-trash"></i></button>
          </div>
        </td>
      </tr>`).join('');
  }

  renderTable(currentData);

  function filterTable(val) {
    currentData = pengaduanData.filter(r =>
      r.judul.toLowerCase().includes(val.toLowerCase()) ||
      r.warga.toLowerCase().includes(val.toLowerCase()) ||
      r.id.toLowerCase().includes(val.toLowerCase())
    );
    renderTable(currentData);
  }

  function filterTopik(val) {
    currentData = val ? pengaduanData.filter(r => r.topik === val) : [...pengaduanData];
    renderTable(currentData);
  }

  function filterStatus(val) {
    currentData = val ? pengaduanData.filter(r => r.status === val) : [...pengaduanData];
    renderTable(currentData);
  }

  function filterTab(tab, el) {
    document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    if (tab === 'aktif') currentData = pengaduanData.filter(r => r.status !== 'Selesai');
    else if (tab === 'selesai') currentData = pengaduanData.filter(r => r.status === 'Selesai');
    else currentData = [...pengaduanData];
    renderTable(currentData);
  }

  function showDetail(id) {
    const r = pengaduanData.find(x => x.id === id);
    if (!r) return;
    document.getElementById('dAduId').textContent = '#' + r.id;
    document.getElementById('dJudul').textContent = r.judul;
    document.getElementById('dStatus').innerHTML = statusBadge(r.status);
    document.getElementById('dTopik').textContent = r.topik;
    document.getElementById('dLokasi').textContent = r.lokasi;
    document.getElementById('dTanggal').textContent = r.tanggal;
    document.getElementById('dWarga').textContent = r.warga;
    openModal('detailModal');
  }

  function saveStatus() {
    closeModal('detailModal');
    showToast('Status pengaduan berhasil diperbarui!', 'success');
  }

  function submitAdd() {
    closeModal('addModal');
    showToast('Pengaduan berhasil ditambahkan!', 'success');
  }
</script>
</body>
</html>