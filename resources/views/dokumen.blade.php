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

@php
  $formatUkur = function (int $b): string {
    if ($b >= 1048576) {
      return round($b / 1048576, 1).' MB';
    }
    if ($b >= 1024) {
      return round($b / 1024).' KB';
    }
    return $b.' B';
  };
@endphp

<div id="app">

  @if(session('success'))
    <div class="alert-banner info" style="margin-bottom:16px;"><i class="ph ph-check-circle"></i><span>{{ session('success') }}</span></div>
  @endif
  @if(session('error'))
    <div class="alert-banner warning" style="margin-bottom:16px;"><i class="ph ph-warning-circle"></i><span>{{ session('error') }}</span></div>
  @endif
  @if($errors->any())
    <div class="alert-banner warning" style="margin-bottom:16px;"><i class="ph ph-warning-circle"></i><span>{{ $errors->first() }}</span></div>
  @endif

  <div class="page-header">
    <div>
      <div class="breadcrumb"><i class="ph ph-house-simple"></i><span>/</span><span>Dokumen</span></div>
      <h1>Manajemen Dokumen</h1>
      <p>Unggah dan kelola dokumen yang dapat diunduh warga</p>
    </div>
    <button type="button" class="btn btn-blue" onclick="openModal('uploadModal')">
      <i class="ph ph-upload-simple"></i> Unggah Dokumen
    </button>
  </div>

  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;">
    <div style="background:white;border-radius:var(--radius-md);padding:16px 20px;border:1px solid var(--gray-100);display:flex;align-items:center;gap:12px;">
      <div style="width:40px;height:40px;border-radius:8px;background:var(--blue-light);display:flex;align-items:center;justify-content:center;font-size:20px;color:var(--blue-primary);"><i class="ph ph-files"></i></div>
      <div><div style="font-size:22px;font-weight:800;font-family:var(--font-display);">{{ $stats['total'] }}</div><div style="font-size:11px;color:var(--gray-400);">Total Dokumen</div></div>
    </div>
    <div style="background:white;border-radius:var(--radius-md);padding:16px 20px;border:1px solid var(--gray-100);display:flex;align-items:center;gap:12px;">
      <div style="width:40px;height:40px;border-radius:8px;background:var(--red-light);display:flex;align-items:center;justify-content:center;font-size:20px;color:#C62828;"><i class="ph ph-file-pdf"></i></div>
      <div><div style="font-size:22px;font-weight:800;font-family:var(--font-display);">{{ $stats['pdf'] }}</div><div style="font-size:11px;color:var(--gray-400);">File PDF</div></div>
    </div>
    <div style="background:white;border-radius:var(--radius-md);padding:16px 20px;border:1px solid var(--gray-100);display:flex;align-items:center;gap:12px;">
      <div style="width:40px;height:40px;border-radius:8px;background:var(--blue-light);display:flex;align-items:center;justify-content:center;font-size:20px;color:#1565C0;"><i class="ph ph-file-doc"></i></div>
      <div><div style="font-size:22px;font-weight:800;font-family:var(--font-display);">{{ $stats['doc'] }}</div><div style="font-size:11px;color:var(--gray-400);">File DOC</div></div>
    </div>
    <div style="background:white;border-radius:var(--radius-md);padding:16px 20px;border:1px solid var(--gray-100);display:flex;align-items:center;gap:12px;">
      <div style="width:40px;height:40px;border-radius:8px;background:var(--green-light);display:flex;align-items:center;justify-content:center;font-size:20px;color:#2E7D32;"><i class="ph ph-microsoft-excel-logo"></i></div>
      <div><div style="font-size:22px;font-weight:800;font-family:var(--font-display);">{{ $stats['xls'] }}</div><div style="font-size:11px;color:var(--gray-400);">File XLS</div></div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><span class="card-title">Daftar Dokumen</span></div>
    <div class="card-body">
      <div class="filter-bar">
        <div class="search-input-bar">
          <i class="ph ph-magnifying-glass icon"></i>
          <input type="search" placeholder="Cari dokumen..." oninput="filterDokumen(this.value)" />
        </div>
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
          <tbody id="dokumenTable">
            @forelse($dokumens as $d)
              <tr
                data-id="{{ $d->getKey() }}"
                data-nama="{{ $d->nama_dokumen }}"
                data-tipe="{{ $d->tipe_berkas }}"
                data-kat="{{ $d->kategori }}"
                data-akses="{{ $d->akses }}"
              >
                <td>
                  <div style="display:flex;align-items:center;gap:12px;">
                    @include('partials.doc-type', ['ext' => $d->tipe_berkas])
                    <div>
                      <div style="font-size:14px;font-weight:500;color:var(--gray-800);">{{ $d->nama_dokumen }}</div>
                    </div>
                  </div>
                </td>
                <td><span style="font-size:12px;text-transform:uppercase;font-weight:700;color:var(--gray-400);">{{ $d->tipe_berkas }}</span></td>
                <td><span style="font-size:12px;background:var(--gray-100);padding:3px 10px;border-radius:99px;color:var(--gray-600);text-transform:capitalize;">{{ $d->kategori }}</span></td>
                <td style="font-size:13px;color:var(--gray-500);">{{ $formatUkur((int) $d->ukuran_byte) }}</td>
                <td style="font-size:13px;color:var(--gray-400);">{{ optional($d->tanggal_dibuat)->format('d M Y') ?? '-' }}</td>
                <td>
                  <span style="display:flex;align-items:center;gap:4px;font-size:13px;font-weight:600;color:var(--blue-primary);">
                    <i class="ph ph-download-simple"></i> {{ (int) $d->jumlah_unduhan }}×
                  </span>
                </td>
                <td>
                  <div style="display:flex;gap:4px;">
                    <a href="{{ route('dokumen.download', $d->getKey()) }}" class="btn btn-blue btn-sm btn-icon" title="Unduh"><i class="ph ph-download-simple"></i></a>
                    <button type="button" class="btn btn-outline btn-sm btn-icon" title="Edit" onclick="openEditDokumen(this.closest('tr'))"><i class="ph ph-pencil"></i></button>
                    <form action="{{ route('dokumen.destroy', $d->getKey()) }}" method="post" style="display:inline;" onsubmit="return confirm('Hapus dokumen ini?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger-outline btn-sm btn-icon" title="Hapus"><i class="ph ph-trash"></i></button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr><td colspan="7"><div class="empty-state"><div class="empty-icon">📄</div><div class="empty-title">Belum ada dokumen</div></div></td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      {!! $dokumens->links('partials.pagination-parete') !!}

      <div style="padding-top:8px;font-size:13px;color:var(--gray-400);">Total {{ $dokumens->total() }} dokumen</div>
    </div>
  </div>
</div>

<div class="modal-overlay" id="uploadModal" style="display:none;">
  <div class="modal">
    <div class="modal-header">
      <h3>Unggah Dokumen</h3>
      <button type="button" class="modal-close" onclick="closeModal('uploadModal')">×</button>
    </div>
    <form method="post" action="{{ route('dokumen.store') }}" enctype="multipart/form-data" class="modal-body">
      @csrf
      <div class="form-group">
        <label class="form-label">Berkas *</label>
        <input type="file" name="berkas" class="form-control-plain" required accept=".pdf,.doc,.docx,.xls,.xlsx" style="border-radius:var(--radius-sm);" />
      </div>
      <div class="form-group">
        <label class="form-label">Nama Dokumen *</label>
        <input type="text" name="nama_dokumen" class="form-control-plain" required value="{{ old('nama_dokumen') }}" style="border-radius:var(--radius-sm);" />
      </div>
      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">Kategori *</label>
          <select name="kategori" class="filter-select" style="width:100%;height:42px;" required>
            <option value="formulir">Formulir</option>
            <option value="surat">Surat</option>
            <option value="peraturan">Peraturan</option>
            <option value="data">Data</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Akses *</label>
          <select name="akses" class="filter-select" style="width:100%;height:42px;" required>
            <option value="semua_warga">Semua Warga</option>
            <option value="admin_rt">Admin RT Saja</option>
          </select>
        </div>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;">
        <button type="button" class="btn btn-outline" onclick="closeModal('uploadModal')">Batal</button>
        <button type="submit" class="btn btn-blue"><i class="ph ph-upload-simple"></i> Unggah</button>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="editDokumenModal" style="display:none;">
  <div class="modal">
    <div class="modal-header">
      <h3>Edit Dokumen</h3>
      <button type="button" class="modal-close" onclick="closeModal('editDokumenModal')">×</button>
    </div>
    <form id="formEditDokumen" method="post" enctype="multipart/form-data" class="modal-body">
      @csrf
      @method('PUT')
      <div class="form-group">
        <label class="form-label">Ganti berkas (opsional)</label>
        <input type="file" name="berkas" class="form-control-plain" accept=".pdf,.doc,.docx,.xls,.xlsx" style="border-radius:var(--radius-sm);" />
      </div>
      <div class="form-group">
        <label class="form-label">Nama Dokumen *</label>
        <input type="text" name="nama_dokumen" id="editDocNama" class="form-control-plain" required style="border-radius:var(--radius-sm);" />
      </div>
      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">Kategori *</label>
          <select name="kategori" id="editDocKat" class="filter-select" style="width:100%;height:42px;" required>
            <option value="formulir">Formulir</option>
            <option value="surat">Surat</option>
            <option value="peraturan">Peraturan</option>
            <option value="data">Data</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Akses *</label>
          <select name="akses" id="editDocAkses" class="filter-select" style="width:100%;height:42px;" required>
            <option value="semua_warga">Semua Warga</option>
            <option value="admin_rt">Admin RT Saja</option>
          </select>
        </div>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;">
        <button type="button" class="btn btn-outline" onclick="closeModal('editDokumenModal')">Batal</button>
        <button type="submit" class="btn btn-blue">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script src="{{ asset('js/parete.js') }}"></script>
<script>
  initLayout('dokumen', 'Dokumen');

  function openEditDokumen(tr) {
    const id = tr.dataset.id;
    document.getElementById('formEditDokumen').action = '{{ url('/dokumen') }}/' + encodeURIComponent(id);
    document.getElementById('editDocNama').value = tr.dataset.nama;
    document.getElementById('editDocKat').value = tr.dataset.kat;
    document.getElementById('editDocAkses').value = tr.dataset.akses;
    openModal('editDokumenModal');
  }

  function filterDokumen(val) {
    const q = (val || '').toLowerCase();
    document.querySelectorAll('#dokumenTable tr[data-id]').forEach(tr => {
      tr.style.display = !q || tr.dataset.nama.toLowerCase().includes(q) ? '' : 'none';
    });
  }

  function filterKat(kat, el) {
    document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    document.querySelectorAll('#dokumenTable tr[data-id]').forEach(tr => {
      tr.style.display = kat === 'semua' || tr.dataset.kat === kat ? '' : 'none';
    });
  }
</script>
</body>
</html>
