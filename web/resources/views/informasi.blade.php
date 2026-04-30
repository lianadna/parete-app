<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Informasi</title>
  <link rel="stylesheet" href="{{ asset('css/parete.css') }}" />
  <script src="https://unpkg.com/@phosphor-icons/web@2.1.1/src/index.js" defer></script>
</head>
<body>

<div id="app">

  <div class="page-header">
    <div>
      <div class="breadcrumb"><i class="ph ph-house-simple"></i><span>/</span><span>Informasi</span></div>
      <h1>Informasi & Pengumuman</h1>
      <p>Kelola pengumuman dan jadwal kegiatan RT</p>
    </div>
    <button class="btn btn-blue" onclick="openModal('addInfoModal')">
      <i class="ph ph-plus"></i> Buat Informasi
    </button>
  </div>

  <!-- Tab -->
  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
    <div class="tab-bar" style="margin-bottom:0">
      <div class="tab-item active" onclick="switchTab('semua',this)">Semua</div>
      <div class="tab-item" onclick="switchTab('pengumuman',this)">Pengumuman</div>
      <div class="tab-item" onclick="switchTab('kegiatan',this)">Kegiatan</div>
    </div>
    <div class="filter-bar" style="margin-bottom:0">
      <div class="search-input-bar">
        <i class="ph ph-magnifying-glass icon"></i>
        <input type="search" placeholder="Cari informasi..." />
      </div>
      <select class="filter-select">
        <option>Semua Bulan</option>
        <option>Maret 2025</option>
        <option>Februari 2025</option>
      </select>
    </div>
  </div>

  <!-- Grid cards -->
  <div id="infoGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:16px;">
    <!-- JS rendered -->
  </div>

</div>

<!-- Modal Tambah Info -->
<div class="modal-overlay" id="addInfoModal" style="display:none;">
  <div class="modal" style="max-width:580px;">
    <div class="modal-header">
      <h3>Buat Informasi Baru</h3>
      <button class="modal-close" onclick="closeModal('addInfoModal')">×</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Tipe *</label>
        <div style="display:flex;gap:8px;">
          <label style="flex:1;display:flex;align-items:center;gap:8px;padding:10px 14px;border:1.5px solid var(--blue-primary);border-radius:var(--radius-sm);cursor:pointer;font-size:13px;font-weight:600;color:var(--blue-primary);background:var(--blue-light);">
            <input type="radio" name="tipe" value="pengumuman" checked style="accent-color:var(--blue-primary);">
            <i class="ph ph-megaphone-simple"></i> Pengumuman
          </label>
          <label style="flex:1;display:flex;align-items:center;gap:8px;padding:10px 14px;border:1.5px solid var(--gray-200);border-radius:var(--radius-sm);cursor:pointer;font-size:13px;font-weight:500;color:var(--gray-600);">
            <input type="radio" name="tipe" value="kegiatan" style="accent-color:var(--blue-primary);">
            <i class="ph ph-calendar-check"></i> Kegiatan
          </label>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Judul *</label>
        <input type="text" class="form-control-plain" placeholder="Judul informasi..." style="border-radius:var(--radius-sm);" />
      </div>
      <div class="form-group">
        <label class="form-label">Isi / Deskripsi *</label>
        <textarea class="form-control-plain" rows="5" placeholder="Tulis isi pengumuman atau kegiatan..." style="height:auto;padding:12px;resize:vertical;border-radius:var(--radius-sm);"></textarea>
      </div>
      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">Tanggal Publikasi</label>
          <input type="date" class="form-control-plain" style="border-radius:var(--radius-sm);" />
        </div>
        <div class="form-group" id="tglKegiatanWrap">
          <label class="form-label">Tanggal Kegiatan</label>
          <input type="date" class="form-control-plain" style="border-radius:var(--radius-sm);" />
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Lampiran (opsional)</label>
        <div style="border:1.5px dashed var(--gray-200);border-radius:var(--radius-sm);padding:20px;text-align:center;cursor:pointer;transition:var(--transition);" onmouseenter="this.style.borderColor='var(--blue-primary)'" onmouseleave="this.style.borderColor='var(--gray-200)'">
          <i class="ph ph-upload-simple" style="font-size:24px;color:var(--gray-400);display:block;margin-bottom:6px;"></i>
          <span style="font-size:13px;color:var(--gray-400);">Klik untuk unggah atau drag & drop</span>
        </div>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;">
        <button class="btn btn-outline" onclick="closeModal('addInfoModal')">Batal</button>
        <button class="btn btn-blue" onclick="submitInfo()"><i class="ph ph-paper-plane-tilt"></i> Publikasikan</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Detail -->
<div class="modal-overlay" id="detailInfoModal" style="display:none;">
  <div class="modal" style="max-width:560px;">
    <div class="modal-header">
      <h3 id="diJudul">Judul Info</h3>
      <button class="modal-close" onclick="closeModal('detailInfoModal')">×</button>
    </div>
    <div class="modal-body">
      <div style="display:flex;gap:10px;margin-bottom:16px;align-items:center;">
        <span id="diBadge"></span>
        <span style="font-size:12px;color:var(--gray-400);" id="diTanggal"></span>
      </div>
      <p style="font-size:14px;color:var(--gray-700);line-height:1.8;" id="diIsi"></p>
      <div style="margin-top:20px;border-top:1px solid var(--gray-100);padding-top:16px;display:flex;gap:10px;justify-content:flex-end;">
        <button class="btn btn-outline" onclick="closeModal('detailInfoModal')"><i class="ph ph-pencil"></i> Edit</button>
        <button class="btn btn-danger-outline btn-sm" onclick="closeModal('detailInfoModal')"><i class="ph ph-trash"></i> Hapus</button>
      </div>
    </div>
  </div>
</div>

<script src="{{ asset('js/parete.js') }}"></script>
<script>
  initLayout('informasi', 'Informasi & Pengumuman');

  const infoData = [
    {
      id:1, tipe:'pengumuman',
      judul:'Jadwal Rapat Bulanan RT 05 – Maret 2025',
      isi:'Rapat bulanan RT akan dilaksanakan pada Jumat, 21 Maret 2025 pukul 19.30 WIB di kediaman Pak Ketua RT. Harap seluruh warga atau perwakilan KK hadir tepat waktu.',
      tanggal:'28 Feb 2025',
      penulis:'Admin RT'
    },
    {
      id:2, tipe:'pengumuman',
      judul:'Waspada Demam Berdarah',
      isi:'Dinas kesehatan mengimbau warga untuk melakukan 3M Plus mencegah terpaparnya virus dengue. Pastikan tidak ada genangan air, menguras tempat penampungan, dan menutup wadah air.',
      tanggal:'3 Mar 2025',
      penulis:'Admin RT'
    },
    {
      id:3, tipe:'kegiatan',
      judul:'Kerja Bakti Massal RT 05',
      isi:'Warga dihimbau untuk hadir pada tanggal 5 Maret 2026 pukul 07.00 di area RT 05. Bawa alat kebersihan masing-masing. Acara selesai diperkirakan pukul 10.00 WIB.',
      tanggal:'1 Mar 2025',
      penulis:'Admin RT'
    },
    {
      id:4, tipe:'kegiatan',
      judul:'Pengajian Rutin Minggu Kedua',
      isi:'Pengajian rutin dilaksanakan setiap minggu kedua bulan berjalan. Bertempat di masjid Al-Ikhlas jam 19.30 WIB. Terbuka untuk seluruh warga RT 05.',
      tanggal:'10 Feb 2025',
      penulis:'Admin RT'
    },
    {
      id:5, tipe:'pengumuman',
      judul:'Pemilihan Ketua RT Periode 2026–2028',
      isi:'Pemberitahuan bahwa akan diadakan pemilihan ketua RT untuk periode 2026–2028 pada bulan Juni 2026. Bagi warga yang ingin mencalonkan diri dapat menghubungi pengurus RT.',
      tanggal:'15 Jan 2025',
      penulis:'Admin RT'
    },
  ];

  let displayData = [...infoData];

  const tipeColors = {
    pengumuman: { bg:'var(--blue-light)',   color:'var(--blue-dark)',  icon:'ph-megaphone-simple', label:'Pengumuman', border:'#90CAF9' },
    kegiatan:   { bg:'var(--yellow-light)', color:'#E65100',           icon:'ph-calendar-check',   label:'Kegiatan',   border:'#FFE082' },
  };

  function renderGrid(data) {
    const grid = document.getElementById('infoGrid');
    if (!data.length) {
      grid.innerHTML = `<div style="grid-column:1/-1"><div class="empty-state"><div class="empty-icon">📢</div><div class="empty-title">Belum ada informasi</div><div class="empty-desc">Klik "Buat Informasi" untuk menambahkan.</div></div></div>`;
      return;
    }
    grid.innerHTML = data.map(r => {
      const t = tipeColors[r.tipe];
      return `
        <div class="card" style="cursor:pointer;transition:var(--transition);"
          onmouseenter="this.style.transform='translateY(-3px)';this.style.boxShadow='var(--shadow-md)'"
          onmouseleave="this.style.transform='';this.style.boxShadow=''"
          onclick="showDetail(${r.id})">
          <div style="padding:20px 22px 16px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
              <span style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:${t.color};background:${t.bg};padding:4px 10px;border-radius:99px;border:1px solid ${t.border};">
                <i class="ph ${t.icon}"></i> ${t.label}
              </span>
              <span style="font-size:12px;color:var(--gray-400);">${r.tanggal}</span>
            </div>
            <h3 style="font-family:var(--font-display);font-size:15px;font-weight:700;color:var(--gray-900);margin-bottom:8px;line-height:1.4;">${r.judul}</h3>
            <p style="font-size:13px;color:var(--gray-500);line-height:1.6;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">${r.isi}</p>
          </div>
          <div style="border-top:1px solid var(--gray-100);padding:12px 22px;display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:12px;color:var(--gray-400);display:flex;align-items:center;gap:5px;"><i class="ph ph-user"></i> ${r.penulis}</span>
            <div style="display:flex;gap:6px;" onclick="event.stopPropagation()">
              <button class="btn btn-outline btn-sm btn-icon" title="Edit"><i class="ph ph-pencil"></i></button>
              <button class="btn btn-danger-outline btn-sm btn-icon" title="Hapus"><i class="ph ph-trash"></i></button>
            </div>
          </div>
        </div>`;
    }).join('');
  }

  renderGrid(displayData);

  function switchTab(tab, el) {
    document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    displayData = tab === 'semua' ? [...infoData] : infoData.filter(r => r.tipe === tab);
    renderGrid(displayData);
  }

  function showDetail(id) {
    const r = infoData.find(x => x.id === id);
    if (!r) return;
    const t = tipeColors[r.tipe];
    document.getElementById('diJudul').textContent = r.judul;
    document.getElementById('diIsi').textContent = r.isi;
    document.getElementById('diTanggal').textContent = r.tanggal + ' · ' + r.penulis;
    document.getElementById('diBadge').innerHTML = `
      <span style="font-size:11px;font-weight:700;text-transform:uppercase;color:${t.color};background:${t.bg};padding:4px 10px;border-radius:99px;">${t.label}</span>`;
    openModal('detailInfoModal');
  }

  function submitInfo() {
    closeModal('addInfoModal');
    showToast('Informasi berhasil dipublikasikan!', 'success');
  }
</script>
</body>
</html>