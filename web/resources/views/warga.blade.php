<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Data Warga</title>
  <link rel="stylesheet" href="{{ asset('css/parete.css') }}" />
  <script src="https://unpkg.com/@phosphor-icons/web@2.1.1/src/index.js" defer></script>
</head>
<body>

<div id="app">

  <div class="page-header">
    <div>
      <div class="breadcrumb"><i class="ph ph-house-simple"></i><span>/</span><span>Data Warga</span></div>
      <h1>Data Warga</h1>
      <p>Kelola data kepala keluarga dan pengguna warga RT 05</p>
    </div>
    <button class="btn btn-blue" onclick="openModal('addWargaModal')">
      <i class="ph ph-plus"></i> Tambah Warga
    </button>
  </div>

  <!-- Summary -->
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px;">
    <div class="card" style="padding:20px;display:flex;align-items:center;gap:16px;">
      <div style="width:48px;height:48px;border-radius:var(--radius-md);background:var(--blue-light);display:flex;align-items:center;justify-content:center;font-size:22px;color:var(--blue-primary);"><i class="ph ph-users-three"></i></div>
      <div>
        <div style="font-family:var(--font-display);font-size:26px;font-weight:800;color:var(--gray-900);">47</div>
        <div style="font-size:12px;color:var(--gray-400);">Total Kepala Keluarga</div>
      </div>
    </div>
    <div class="card" style="padding:20px;display:flex;align-items:center;gap:16px;">
      <div style="width:48px;height:48px;border-radius:var(--radius-md);background:var(--green-light);display:flex;align-items:center;justify-content:center;font-size:22px;color:var(--green-success);"><i class="ph ph-user-check"></i></div>
      <div>
        <div style="font-family:var(--font-display);font-size:26px;font-weight:800;color:var(--gray-900);">44</div>
        <div style="font-size:12px;color:var(--gray-400);">Akun Aktif</div>
      </div>
    </div>
    <div class="card" style="padding:20px;display:flex;align-items:center;gap:16px;">
      <div style="width:48px;height:48px;border-radius:var(--radius-md);background:var(--orange-light);display:flex;align-items:center;justify-content:center;font-size:22px;color:var(--orange-warn);"><i class="ph ph-user-minus"></i></div>
      <div>
        <div style="font-family:var(--font-display);font-size:26px;font-weight:800;color:var(--gray-900);">3</div>
        <div style="font-size:12px;color:var(--gray-400);">Belum Aktif</div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <span class="card-title">Daftar Warga</span>
    </div>
    <div class="card-body">
      <div class="filter-bar">
        <div class="search-input-bar">
          <i class="ph ph-magnifying-glass icon"></i>
          <input type="search" placeholder="Cari nama, ID, nomor rumah..." oninput="filterWarga(this.value)" />
        </div>
        <select class="filter-select">
          <option>Semua Status</option>
          <option>Aktif</option>
          <option>Nonaktif</option>
        </select>
        <button class="btn btn-outline btn-sm"><i class="ph ph-export"></i> Export</button>
        <button class="btn btn-outline btn-sm"><i class="ph ph-printer"></i> Cetak</button>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>No.</th>
              <th>ID Keluarga</th>
              <th>Nama KK</th>
              <th>No. Rumah</th>
              <th>No. HP</th>
              <th>Pengaduan</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="wargaTable"><!-- JS --></tbody>
        </table>
      </div>

      <div style="display:flex;align-items:center;justify-content:space-between;padding-top:16px;flex-wrap:wrap;gap:12px;">
        <span style="font-size:13px;color:var(--gray-400);">Menampilkan 1–5 dari 47 warga</span>
        <div class="pagination">
          <button class="page-btn"><i class="ph ph-caret-left"></i></button>
          <button class="page-btn active">1</button>
          <button class="page-btn">2</button>
          <button class="page-btn">3</button>
          <button class="page-btn"><i class="ph ph-caret-right"></i></button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tambah Warga -->
<div class="modal-overlay" id="addWargaModal" style="display:none;">
  <div class="modal">
    <div class="modal-header">
      <h3>Tambah Data Warga</h3>
      <button class="modal-close" onclick="closeModal('addWargaModal')">×</button>
    </div>
    <div class="modal-body">
      <div class="alert-banner info" style="margin-bottom:16px;">
        <i class="ph ph-info"></i>
        <span>ID Keluarga akan dibuat otomatis. Warga akan mendapat password awal <strong>warga123</strong>.</span>
      </div>
      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">Nama Kepala Keluarga *</label>
          <input type="text" class="form-control-plain" placeholder="Nama lengkap" style="border-radius:var(--radius-sm);" />
        </div>
        <div class="form-group">
          <label class="form-label">No. Rumah *</label>
          <input type="text" class="form-control-plain" placeholder="cth. 87" style="border-radius:var(--radius-sm);" />
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Alamat Lengkap</label>
        <textarea class="form-control-plain" rows="2" placeholder="Tulis alamat..." style="height:auto;padding:12px;border-radius:var(--radius-sm);resize:none;"></textarea>
      </div>
      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">No. HP / WhatsApp</label>
          <input type="tel" class="form-control-plain" placeholder="+62..." style="border-radius:var(--radius-sm);" />
        </div>
        <div class="form-group">
          <label class="form-label">Username Login</label>
          <input type="text" class="form-control-plain" placeholder="cth. warga087" style="border-radius:var(--radius-sm);" />
        </div>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;">
        <button class="btn btn-outline" onclick="closeModal('addWargaModal')">Batal</button>
        <button class="btn btn-blue" onclick="submitWarga()"><i class="ph ph-user-plus"></i> Simpan</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Edit Warga -->
<div class="modal-overlay" id="editWargaModal" style="display:none;">
  <div class="modal">
    <div class="modal-header">
      <h3>Edit Data Warga</h3>
      <button class="modal-close" onclick="closeModal('editWargaModal')">×</button>
    </div>
    <div class="modal-body">
      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">Nama KK</label>
          <input type="text" class="form-control-plain" id="editNama" style="border-radius:var(--radius-sm);" />
        </div>
        <div class="form-group">
          <label class="form-label">No. Rumah</label>
          <input type="text" class="form-control-plain" id="editNoRumah" style="border-radius:var(--radius-sm);" />
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">No. HP</label>
        <input type="tel" class="form-control-plain" id="editHp" style="border-radius:var(--radius-sm);" />
      </div>
      <div class="form-group">
        <label class="form-label">Status Akun</label>
        <select class="filter-select" style="width:100%;height:42px;">
          <option>Aktif</option>
          <option>Nonaktif</option>
        </select>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;">
        <button class="btn btn-outline" onclick="closeModal('editWargaModal')">Batal</button>
        <button class="btn btn-blue" onclick="submitEdit()"><i class="ph ph-floppy-disk"></i> Simpan</button>
      </div>
    </div>
  </div>
</div>

<script src="{{ asset('js/parete.js') }}"></script>
<script>
  initLayout('warga', 'Data Warga');

  const wargaData = [
    { id:'RT05-2026-001', nama:'Budi Santoso',     noRumah:'1',  hp:'+62 812 0001 0001', pengaduan:2, status:'Aktif'    },
    { id:'RT05-2026-012', nama:'Siti Rahayuni',    noRumah:'12', hp:'+62 812 0012 0012', pengaduan:1, status:'Aktif'    },
    { id:'RT05-2026-045', nama:'Ahmad Fauzan',     noRumah:'45', hp:'+62 812 0045 0045', pengaduan:3, status:'Aktif'    },
    { id:'RT05-2026-087', nama:'Yulian Adiprana',  noRumah:'87', hp:'+62 812 3456 7890', pengaduan:5, status:'Aktif'    },
    { id:'RT05-2026-091', nama:'Dewi Kusumawati',  noRumah:'91', hp:'-',                 pengaduan:0, status:'Nonaktif' },
  ];

  let wargaDisplay = [...wargaData];

  function renderWarga(data) {
    const tbody = document.getElementById('wargaTable');
    if (!data.length) {
      tbody.innerHTML = `<tr><td colspan="8"><div class="empty-state"><div class="empty-icon">👥</div><div class="empty-title">Tidak ada data warga</div></div></td></tr>`;
      return;
    }
    tbody.innerHTML = data.map((r, i) => `
      <tr>
        <td style="color:var(--gray-400);font-size:13px;">${i+1}</td>
        <td>
          <span style="font-family:monospace;font-size:12px;background:var(--gray-100);padding:3px 8px;border-radius:4px;color:var(--gray-600);">${r.id}</span>
        </td>
        <td>
          <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--blue-primary),var(--blue-dark));display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:white;flex-shrink:0;">${r.nama.charAt(0)}</div>
            <span style="font-weight:500;">${r.nama}</span>
          </div>
        </td>
        <td style="text-align:center;font-weight:600;">${r.noRumah}</td>
        <td style="font-size:13px;color:var(--gray-500);">${r.hp}</td>
        <td style="text-align:center;">
          <span style="font-size:13px;font-weight:700;color:${r.pengaduan>0?'var(--blue-primary)':'var(--gray-300)'};">${r.pengaduan}</span>
        </td>
        <td>${statusBadge(r.status)}</td>
        <td>
          <div style="display:flex;gap:4px;">
            <button class="btn btn-outline btn-sm btn-icon" title="Reset Password" onclick="resetPw('${r.id}')"><i class="ph ph-key"></i></button>
            <button class="btn btn-outline btn-sm btn-icon" title="Edit" onclick="editWarga('${r.id}')"><i class="ph ph-pencil"></i></button>
            <button class="btn btn-danger-outline btn-sm btn-icon" title="Hapus"><i class="ph ph-trash"></i></button>
          </div>
        </td>
      </tr>`).join('');
  }

  renderWarga(wargaDisplay);

  function filterWarga(val) {
    wargaDisplay = wargaData.filter(r =>
      r.nama.toLowerCase().includes(val.toLowerCase()) ||
      r.id.toLowerCase().includes(val.toLowerCase()) ||
      r.noRumah.includes(val)
    );
    renderWarga(wargaDisplay);
  }

  function editWarga(id) {
    const r = wargaData.find(x => x.id === id);
    if (!r) return;
    document.getElementById('editNama').value = r.nama;
    document.getElementById('editNoRumah').value = r.noRumah;
    document.getElementById('editHp').value = r.hp;
    openModal('editWargaModal');
  }

  function resetPw(id) {
    if (confirm('Reset password warga ini ke "warga123"?')) {
      showToast('Password berhasil direset ke warga123', 'info');
    }
  }

  function submitWarga() {
    closeModal('addWargaModal');
    showToast('Data warga berhasil ditambahkan!', 'success');
  }

  function submitEdit() {
    closeModal('editWargaModal');
    showToast('Data warga berhasil diperbarui!', 'success');
  }
</script>
</body>
</html>