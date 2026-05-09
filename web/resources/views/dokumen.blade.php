<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dokumen</title>
  <link rel="stylesheet" href="{{ asset('css/parete.css') }}" />
  <script src="https://unpkg.com/@phosphor-icons/web@2.1.1/src/index.js" defer></script>
</head>
<body>

<div id="app">

  <div class="page-header">
    <div>
      <div class="breadcrumb"><i class="ph ph-house-simple"></i><span>/</span><span>Dokumen</span></div>
      <h1>Manajemen Dokumen</h1>
      <p>Unggah dan kelola dokumen yang dapat diunduh warga</p>
    </div>
    <button class="btn btn-blue" onclick="openModal('uploadModal')">
      <i class="ph ph-upload-simple"></i> Unggah Dokumen
    </button>
  </div>

  <!-- Stats mini -->
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;">
    <div style="background:white;border-radius:var(--radius-md);padding:16px 20px;border:1px solid var(--gray-100);display:flex;align-items:center;gap:12px;">
      <div style="width:40px;height:40px;border-radius:8px;background:var(--blue-light);display:flex;align-items:center;justify-content:center;font-size:20px;color:var(--blue-primary);"><i class="ph ph-files"></i></div>
      <div><div style="font-size:22px;font-weight:800;font-family:var(--font-display);">12</div><div style="font-size:11px;color:var(--gray-400);">Total Dokumen</div></div>
    </div>
    <div style="background:white;border-radius:var(--radius-md);padding:16px 20px;border:1px solid var(--gray-100);display:flex;align-items:center;gap:12px;">
      <div style="width:40px;height:40px;border-radius:8px;background:var(--red-light);display:flex;align-items:center;justify-content:center;font-size:20px;color:#C62828;"><i class="ph ph-file-pdf"></i></div>
      <div><div style="font-size:22px;font-weight:800;font-family:var(--font-display);">7</div><div style="font-size:11px;color:var(--gray-400);">File PDF</div></div>
    </div>
    <div style="background:white;border-radius:var(--radius-md);padding:16px 20px;border:1px solid var(--gray-100);display:flex;align-items:center;gap:12px;">
      <div style="width:40px;height:40px;border-radius:8px;background:var(--blue-light);display:flex;align-items:center;justify-content:center;font-size:20px;color:#1565C0;"><i class="ph ph-file-doc"></i></div>
      <div><div style="font-size:22px;font-weight:800;font-family:var(--font-display);">3</div><div style="font-size:11px;color:var(--gray-400);">File DOC</div></div>
    </div>
    <div style="background:white;border-radius:var(--radius-md);padding:16px 20px;border:1px solid var(--gray-100);display:flex;align-items:center;gap:12px;">
      <div style="width:40px;height:40px;border-radius:8px;background:var(--green-light);display:flex;align-items:center;justify-content:center;font-size:20px;color:#2E7D32;"><i class="ph ph-microsoft-excel-logo"></i></div>
      <div><div style="font-size:22px;font-weight:800;font-family:var(--font-display);">2</div><div style="font-size:11px;color:var(--gray-400);">File XLS</div></div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <span class="card-title">Daftar Dokumen</span>
    </div>
    <div class="card-body">
      <div class="filter-bar">
        <div class="search-input-bar">
          <i class="ph ph-magnifying-glass icon"></i>
          <input type="search" placeholder="Cari dokumen..." oninput="filterDokumen(this.value)" />
        </div>

        <!-- Category tabs inline -->
        <div class="tab-bar" style="margin-bottom:0">
          <div class="tab-item active" onclick="filterKat('semua',this)">Semua</div>
          <div class="tab-item" onclick="filterKat('surat',this)">Surat</div>
          <div class="tab-item" onclick="filterKat('formulir',this)">Formulir</div>
          <div class="tab-item" onclick="filterKat('peraturan',this)">Peraturan</div>
          <div class="tab-item" onclick="filterKat('data',this)">Data</div>
        </div>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Dokumen</th>
              <th>Tipe</th>
              <th>Kategori</th>
              <th>Ukuran</th>
              <th>Diunggah</th>
              <th>Unduhan</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="dokumenTable"><!-- JS --></tbody>
        </table>
      </div>

      <div style="display:flex;align-items:center;justify-content:space-between;padding-top:16px;">
        <span style="font-size:13px;color:var(--gray-400);">Menampilkan 1–5 dari 12 dokumen</span>
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

<!-- Upload Modal -->
<div class="modal-overlay" id="uploadModal" style="display:none;">
  <div class="modal">
    <div class="modal-header">
      <h3>Unggah Dokumen</h3>
      <button class="modal-close" onclick="closeModal('uploadModal')">×</button>
    </div>
    <div class="modal-body">
      <!-- Drop zone -->
      <div id="dropZone" style="
        border:2px dashed var(--gray-200);
        border-radius:var(--radius-md);
        padding:36px;
        text-align:center;
        cursor:pointer;
        transition:var(--transition);
        margin-bottom:20px;
        background:var(--gray-50);
      "
        onmouseenter="this.style.borderColor='var(--blue-primary)';this.style.background='var(--blue-light)'"
        onmouseleave="this.style.borderColor='var(--gray-200)';this.style.background='var(--gray-50)'"
        onclick="document.getElementById('fileInput').click()"
      >
        <i class="ph ph-cloud-arrow-up" style="font-size:40px;color:var(--blue-primary);display:block;margin-bottom:10px;"></i>
        <div style="font-size:15px;font-weight:600;color:var(--gray-700);margin-bottom:6px;">Drag & drop file di sini</div>
        <div style="font-size:13px;color:var(--gray-400);">atau klik untuk memilih file</div>
        <div style="font-size:11px;color:var(--gray-300);margin-top:8px;">PDF, DOC, DOCX, XLS, XLSX · Maks 10 MB</div>
        <input type="file" id="fileInput" style="display:none;" accept=".pdf,.doc,.docx,.xls,.xlsx" onchange="handleFile(this)" />
      </div>

      <div id="filePreview" style="display:none;background:var(--blue-light);border-radius:var(--radius-sm);padding:14px 16px;margin-bottom:20px;display:flex;align-items:center;gap:12px;">
        <i class="ph ph-file" style="font-size:28px;color:var(--blue-primary);"></i>
        <div style="flex:1;">
          <div style="font-size:13px;font-weight:600;" id="fileName">file.pdf</div>
          <div style="font-size:11px;color:var(--gray-500);" id="fileSize">245 KB</div>
        </div>
        <button onclick="clearFile()" style="color:var(--gray-400);font-size:18px;background:none;border:none;cursor:pointer;">×</button>
      </div>

      <div class="form-group">
        <label class="form-label">Nama Dokumen *</label>
        <input type="text" id="docName" class="form-control-plain" placeholder="Nama tampilan dokumen" style="border-radius:var(--radius-sm);" />
      </div>
      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">Kategori</label>
          <select class="filter-select" style="width:100%;height:42px;">
            <option>Formulir</option>
            <option>Surat</option>
            <option>Peraturan</option>
            <option>Data</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Akses</label>
          <select class="filter-select" style="width:100%;height:42px;">
            <option>Semua Warga</option>
            <option>Admin RT Saja</option>
          </select>
        </div>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;">
        <button class="btn btn-outline" onclick="closeModal('uploadModal')">Batal</button>
        <button class="btn btn-blue" onclick="submitUpload()"><i class="ph ph-upload-simple"></i> Unggah</button>
      </div>
    </div>
  </div>
</div>

<script src="{{ asset('js/parete.js') }}"></script>
<script>
  initLayout('dokumen', 'Dokumen');

  const dokumenData = [
    { id:1, nama:'Formulir Surat Keterangan Domisili', ext:'pdf', kat:'formulir', ukuran:'245 KB', tanggal:'1 Mar 2026',  unduhan:12 },
    { id:2, nama:'Template Pengajuan Surat RT',        ext:'doc', kat:'formulir', ukuran:'89 KB',  tanggal:'15 Feb 2026', unduhan:8  },
    { id:3, nama:'Peraturan RT 05 (2026)',             ext:'pdf', kat:'peraturan',ukuran:'312 KB', tanggal:'1 Jan 2026',  unduhan:31 },
    { id:4, nama:'Pengumuman Rapat Maret 2026',        ext:'doc', kat:'surat',    ukuran:'198 KB', tanggal:'28 Feb 2026', unduhan:5  },
    { id:5, nama:'Data Warga RT 05',                  ext:'xls', kat:'data',     ukuran:'128 KB', tanggal:'10 Jan 2026', unduhan:3  },
  ];

  let displayDok = [...dokumenData];

  function renderDokumen(data) {
    const tbody = document.getElementById('dokumenTable');
    if (!data.length) {
      tbody.innerHTML = `<tr><td colspan="7"><div class="empty-state"><div class="empty-icon">📄</div><div class="empty-title">Belum ada dokumen</div></div></td></tr>`;
      return;
    }
    tbody.innerHTML = data.map(r => `
      <tr>
        <td>
          <div style="display:flex;align-items:center;gap:12px;">
            ${docTypeBadge(r.ext)}
            <div>
              <div style="font-size:14px;font-weight:500;color:var(--gray-800);">${r.nama}</div>
            </div>
          </div>
        </td>
        <td><span style="font-size:12px;text-transform:uppercase;font-weight:700;color:var(--gray-400);">${r.ext}</span></td>
        <td><span style="font-size:12px;background:var(--gray-100);padding:3px 10px;border-radius:99px;color:var(--gray-600);text-transform:capitalize;">${r.kat}</span></td>
        <td style="font-size:13px;color:var(--gray-500);">${r.ukuran}</td>
        <td style="font-size:13px;color:var(--gray-400);">${r.tanggal}</td>
        <td>
          <span style="display:flex;align-items:center;gap:4px;font-size:13px;font-weight:600;color:var(--blue-primary);">
            <i class="ph ph-download-simple"></i> ${r.unduhan}×
          </span>
        </td>
        <td>
          <div style="display:flex;gap:4px;">
            <button class="btn btn-outline btn-sm btn-icon" title="Pratinjau"><i class="ph ph-eye"></i></button>
            <button class="btn btn-blue btn-sm btn-icon" title="Unduh"><i class="ph ph-download-simple"></i></button>
            <button class="btn btn-danger-outline btn-sm btn-icon" title="Hapus"><i class="ph ph-trash"></i></button>
          </div>
        </td>
      </tr>`).join('');
  }

  renderDokumen(displayDok);

  function filterDokumen(val) {
    displayDok = dokumenData.filter(r => r.nama.toLowerCase().includes(val.toLowerCase()));
    renderDokumen(displayDok);
  }

  function filterKat(kat, el) {
    document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    displayDok = kat === 'semua' ? [...dokumenData] : dokumenData.filter(r => r.kat === kat);
    renderDokumen(displayDok);
  }

  function handleFile(input) {
    const file = input.files[0];
    if (!file) return;
    document.getElementById('filePreview').style.display = 'flex';
    document.getElementById('fileName').textContent = file.name;
    document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(0) + ' KB';
    document.getElementById('docName').value = file.name.replace(/\.[^.]+$/, '');
  }

  function clearFile() {
    document.getElementById('fileInput').value = '';
    document.getElementById('filePreview').style.display = 'none';
  }

  function submitUpload() {
    closeModal('uploadModal');
    showToast('Dokumen berhasil diunggah!', 'success');
  }
</script>
</body>
</html>