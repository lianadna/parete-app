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

@include('partials.app-open')

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
      <p>Arsip unduhan warga dan tindak lanjut permohonan surat resmi</p>
    </div>
    @if($activeTab === 'arsip')
    <button type="button" class="btn btn-blue" onclick="openModal('uploadModal')">
      <i class="ph ph-upload-simple"></i> Unggah Dokumen
    </button>
    @endif
  </div>

  @php
    $urlTabArsip = route('dokumen.index', ['tab' => 'arsip']);
    $urlTabPermohonan = route('dokumen.index', ['tab' => 'permohonan']);
  @endphp

  @if($activeTab === 'arsip')
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
      <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
        <div class="filter-bar" style="margin-bottom:0;flex:1;">
          <div class="search-input-bar">
            <i class="ph ph-magnifying-glass icon"></i>
            <input type="search" placeholder="Cari dokumen..." oninput="filterDokumen(this.value)" />
          </div>
          <select class="filter-select" onchange="if(this.value) window.location.href=this.value">
            <option value="{{ $urlTabArsip }}" selected>Daftar Dokumen</option>
            <option value="{{ $urlTabPermohonan }}">Permohonan Dokumen Warga{{ $permohonanStats['terkirim'] > 0 ? ' ('.$permohonanStats['terkirim'].' menunggu)' : '' }}</option>
          </select>
          <select class="filter-select" id="filterKatDokumen" onchange="filterKat(this.value)">
            <option value="semua">Semua Kategori</option>
            <option value="surat">Surat</option>
            <option value="formulir">Formulir</option>
            <option value="peraturan">Peraturan</option>
            <option value="data">Data</option>
          </select>
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
            <tr id="dokumenEmptyRow" style="display:none;">
              <td colspan="7">
                <div class="empty-state">
                  <div class="empty-icon">🔍</div>
                  <div class="empty-title">Data tidak ditemukan</div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      {!! $dokumens->links('partials.pagination-parete') !!}

      <div style="padding-top:8px;font-size:13px;color:var(--gray-400);">Total {{ $dokumens->total() }} dokumen</div>
    </div>
  </div>
  @else
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;">
    <div style="background:white;border-radius:var(--radius-md);padding:16px 20px;border:1px solid var(--gray-100);">
      <div style="font-size:22px;font-weight:800;">{{ $permohonanStats['total'] }}</div>
      <div style="font-size:11px;color:var(--gray-400);">Total Permohonan</div>
    </div>
    <div style="background:white;border-radius:var(--radius-md);padding:16px 20px;border:1px solid var(--gray-100);">
      <div style="font-size:22px;font-weight:800;color:#E65100;">{{ $permohonanStats['terkirim'] }}</div>
      <div style="font-size:11px;color:var(--gray-400);">Menunggu</div>
    </div>
    <div style="background:white;border-radius:var(--radius-md);padding:16px 20px;border:1px solid var(--gray-100);">
      <div style="font-size:22px;font-weight:800;color:var(--blue-primary);">{{ $permohonanStats['diproses'] }}</div>
      <div style="font-size:11px;color:var(--gray-400);">Diproses</div>
    </div>
    <div style="background:white;border-radius:var(--radius-md);padding:16px 20px;border:1px solid var(--gray-100);">
      <div style="font-size:22px;font-weight:800;color:#2E7D32;">{{ $permohonanStats['selesai'] }}</div>
      <div style="font-size:11px;color:var(--gray-400);">Selesai</div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><span class="card-title">Permohonan Dokumen Warga</span></div>
    <div class="card-body">
      <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
        <div class="filter-bar" style="margin-bottom:0;flex:1;">
          <div class="search-input-bar">
            <i class="ph ph-magnifying-glass icon"></i>
            <input type="search" id="permohonanSearch" placeholder="Cari pemohon atau jenis dokumen..." oninput="filterPermohonan(this.value)" />
          </div>
          <select class="filter-select" onchange="if(this.value) window.location.href=this.value">
            <option value="{{ $urlTabArsip }}">Daftar Dokumen</option>
            <option value="{{ $urlTabPermohonan }}" selected>Permohonan Dokumen Warga{{ $permohonanStats['terkirim'] > 0 ? ' ('.$permohonanStats['terkirim'].' menunggu)' : '' }}</option>
          </select>
          <select class="filter-select" id="filterStatusPermohonan" onchange="filterStatusPermohonan(this.value)">
            <option value="semua">Semua Status</option>
            <option value="Terkirim">Terkirim</option>
            <option value="Diproses">Diproses</option>
            <option value="Selesai">Selesai</option>
            <option value="Ditolak">Ditolak</option>
            <option value="Dibatalkan">Dibatalkan</option>
          </select>
        </div>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Pemohon</th>
              <th>Jenis Dokumen</th>
              <th>Keperluan</th>
              <th>TTD</th>
              <th>Status</th>
              <th>Tanggal</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="permohonanTable">
            @forelse($permohonans as $pm)
              <tr
                data-id="{{ $pm->getKey() }}"
                data-nama="{{ $pm->nama_pemohon }}"
                data-jenis="{{ $pm->jenis_dokumen_display }}"
                data-keperluan="{{ $pm->keperluan }}"
                data-catatan="{{ $pm->catatan_tambahan }}"
                data-minta-ttd="{{ $pm->minta_tanda_tangan ?? 'tanpa_ttd' }}"
                data-status="{{ $pm->status_permohonan }}"
                data-catatan-rt="{{ $pm->catatan_rt }}"
                data-alasan="{{ $pm->alasan_ditolak }}"
                data-berkas="{{ $pm->path_berkas_diisi ? '1' : '0' }}"
                data-pendukung="{{ $pm->path_dokumen_pendukung ? '1' : '0' }}"
                data-surat="{{ $pm->path_surat_balasan ? '1' : '0' }}"
                data-surat-ttd="{{ $pm->path_surat_ttd ? '1' : '0' }}"
              >
                <td style="font-weight:600;">{{ $pm->nama_pemohon }}</td>
                <td>{{ $pm->jenis_dokumen_display }}</td>
                <td style="max-width:220px;font-size:13px;color:var(--gray-600);">{{ \Illuminate\Support\Str::limit($pm->keperluan, 80) }}</td>
                <td style="font-size:12px;">{{ $pm->mintaTandaTanganLabel() }}</td>
                <td>@include('partials.badge-status', ['status' => $pm->status_permohonan])</td>
                <td style="font-size:13px;color:var(--gray-400);">{{ optional($pm->tanggal_dibuat)->timezone(config('app.timezone'))->format('d M Y H:i') ?? '-' }}</td>
                <td>
                  <div style="display:flex;gap:4px;flex-wrap:wrap;">
                    <button type="button" class="btn btn-blue btn-sm" onclick="openRespondPermohonan(this.closest('tr'))">
                      <i class="ph ph-chat-circle-text"></i> Tindak lanjut
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr><td colspan="7"><div class="empty-state"><div class="empty-icon">📋</div><div class="empty-title">Belum ada permohonan</div></div></td></tr>
            @endforelse
            <tr id="permohonanEmptyRow" style="display:none;">
              <td colspan="7">
                <div class="empty-state">
                  <div class="empty-icon">🔍</div>
                  <div class="empty-title">Data tidak ditemukan</div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif
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

<div class="modal-overlay" id="respondPermohonanModal" style="display:none;">
  <div class="modal" style="max-width:560px;">
    <div class="modal-header">
      <h3>Tindak Lanjut Permohonan</h3>
      <button type="button" class="modal-close" onclick="closeModal('respondPermohonanModal')">×</button>
    </div>
    <form id="formRespondPermohonan" method="post" enctype="multipart/form-data" class="modal-body">
      @csrf
      <div id="pmDetailBox" style="background:var(--gray-50);border-radius:var(--radius-sm);padding:12px 14px;margin-bottom:16px;font-size:13px;line-height:1.5;"></div>
      <div id="pmLampiranLinks" style="margin-bottom:16px;display:flex;flex-wrap:wrap;gap:8px;"></div>
      <div class="form-group">
        <label class="form-label">Status *</label>
        <select name="status_permohonan" id="pmStatus" class="filter-select" style="width:100%;height:42px;" required onchange="togglePmReject()">
          <option value="Diproses">Diproses</option>
          <option value="Selesai">Selesai</option>
          <option value="Ditolak">Ditolak</option>
        </select>
      </div>
      <div class="form-group" id="pmAlasanGroup" style="display:none;">
        <label class="form-label">Alasan penolakan *</label>
        <textarea name="alasan_ditolak" id="pmAlasan" class="form-control-plain" rows="3" style="border-radius:var(--radius-sm);"></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Catatan RT (opsional)</label>
        <textarea name="catatan_rt" id="pmCatatanRt" class="form-control-plain" rows="2" style="border-radius:var(--radius-sm);"></textarea>
      </div>
      <div class="form-group" id="pmSuratBalasanGroup">
        <label class="form-label">Surat balasan (PDF/DOCX)</label>
        <input type="file" name="surat_balasan" accept=".pdf,.doc,.docx" class="form-control-plain" style="border-radius:var(--radius-sm);" />
        <div style="font-size:11px;color:var(--gray-400);margin-top:4px;">Wajib diunggah saat status Selesai (surat tanpa TTD atau versi utama).</div>
      </div>
      <div class="form-group" id="pmSuratTtdGroup" style="display:none;">
        <label class="form-label">Surat bertanda tangan RT (PDF/DOCX)</label>
        <input type="file" name="surat_ttd" accept=".pdf,.doc,.docx" class="form-control-plain" style="border-radius:var(--radius-sm);" />
        <div style="font-size:11px;color:var(--gray-400);margin-top:4px;">Wajib jika warga meminta surat dengan tanda tangan.</div>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;">
        <button type="button" class="btn btn-outline" onclick="closeModal('respondPermohonanModal')">Batal</button>
        <button type="submit" class="btn btn-blue"><i class="ph ph-paper-plane-tilt"></i> Simpan</button>
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

  let dokumenSearch = '';
  let dokumenKat = 'semua';

  function applyDokumenFilters() {
    const q = dokumenSearch.trim().toLowerCase();
    applyClientFilter('#dokumenTable tr[data-id]', 'dokumenEmptyRow', tr => {
      const matchSearch = !q || tr.dataset.nama.toLowerCase().includes(q);
      const matchKat = dokumenKat === 'semua' || tr.dataset.kat === dokumenKat;
      return matchSearch && matchKat;
    });
  }

  function filterDokumen(val) {
    dokumenSearch = val || '';
    applyDokumenFilters();
  }

  function filterKat(kat) {
    dokumenKat = kat || 'semua';
    applyDokumenFilters();
  }

  let pmStatusFilter = 'semua';
  let pmSearchQuery = '';

  function applyPermohonanFilters() {
    const q = pmSearchQuery.trim().toLowerCase();
    applyClientFilter('#permohonanTable tr[data-id]', 'permohonanEmptyRow', tr => {
      const matchStatus = pmStatusFilter === 'semua' || tr.dataset.status === pmStatusFilter;
      const hay = (tr.dataset.nama + ' ' + tr.dataset.jenis + ' ' + tr.dataset.keperluan).toLowerCase();
      const matchSearch = !q || hay.includes(q);
      return matchStatus && matchSearch;
    });
  }

  function filterPermohonan(val) {
    pmSearchQuery = val || '';
    applyPermohonanFilters();
  }

  function filterStatusPermohonan(status) {
    pmStatusFilter = status;
    applyPermohonanFilters();
  }

  function togglePmReject() {
    const st = document.getElementById('pmStatus').value;
    document.getElementById('pmAlasanGroup').style.display = st === 'Ditolak' ? 'block' : 'none';
    const selesai = st === 'Selesai';
    document.getElementById('pmSuratBalasanGroup').style.display = selesai ? 'block' : 'block';
  }

  function openRespondPermohonan(tr) {
    const id = tr.dataset.id;
    document.getElementById('formRespondPermohonan').action =
      '{{ url('/permohonan-dokumen') }}/' + encodeURIComponent(id) + '/respond';
    const minta = tr.dataset.mintaTtd;
    const ttdLabel = minta === 'dengan_ttd' ? 'Dengan tanda tangan RT' : 'Tanpa tanda tangan';
    document.getElementById('pmDetailBox').innerHTML =
      '<strong>' + tr.dataset.nama + '</strong><br/>' +
      '<span style="color:var(--gray-500);">Jenis:</span> ' + tr.dataset.jenis + '<br/>' +
      '<span style="color:var(--gray-500);">Keperluan:</span> ' + escapeHtml(tr.dataset.keperluan) + '<br/>' +
      (tr.dataset.catatan ? '<span style="color:var(--gray-500);">Catatan:</span> ' + escapeHtml(tr.dataset.catatan) + '<br/>' : '') +
      '<span style="color:var(--gray-500);">Permintaan:</span> ' + ttdLabel;

    let links = '';
    const base = '{{ url('/permohonan-dokumen') }}/' + encodeURIComponent(id) + '/file/';
    if (tr.dataset.berkas === '1') {
      links += '<a class="btn btn-outline btn-sm" href="' + base + 'berkas_diisi" target="_blank"><i class="ph ph-file"></i> Berkas diisi warga</a>';
    }
    if (tr.dataset.pendukung === '1') {
      links += '<a class="btn btn-outline btn-sm" href="' + base + 'dokumen_pendukung" target="_blank"><i class="ph ph-files"></i> Dokumen pendukung</a>';
    }
    if (tr.dataset.surat === '1') {
      links += '<a class="btn btn-outline btn-sm" href="' + base + 'surat_balasan" target="_blank"><i class="ph ph-file-pdf"></i> Surat balasan</a>';
    }
    if (tr.dataset.suratTtd === '1') {
      links += '<a class="btn btn-outline btn-sm" href="' + base + 'surat_ttd" target="_blank"><i class="ph ph-signature"></i> Surat TTD</a>';
    }
    document.getElementById('pmLampiranLinks').innerHTML = links || '<span style="font-size:12px;color:var(--gray-400);">Tidak ada lampiran dari warga.</span>';

    document.getElementById('pmStatus').value = tr.dataset.status === 'Terkirim' ? 'Diproses' : tr.dataset.status;
    document.getElementById('pmCatatanRt').value = tr.dataset.catatanRt || '';
    document.getElementById('pmAlasan').value = tr.dataset.alasan || '';
    document.getElementById('pmSuratTtdGroup').style.display = minta === 'dengan_ttd' ? 'block' : 'none';
    togglePmReject();
    openModal('respondPermohonanModal');
  }

  function escapeHtml(s) {
    const d = document.createElement('div');
    d.textContent = s || '';
    return d.innerHTML;
  }

  if (document.getElementById('permohonanTable')) {
    applyPermohonanFilters();
  }
</script>
</body>
</html>
