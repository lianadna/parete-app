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

@include('partials.app-open')

  @if(session('success'))
    <div class="alert-banner info" style="margin-bottom:16px;">
      <i class="ph ph-check-circle"></i>
      <span>{{ session('success') }}</span>
    </div>
  @endif
  @if(session('error'))
    <div class="alert-banner warning" style="margin-bottom:16px;">
      <i class="ph ph-warning-circle"></i>
      <span>{{ session('error') }}</span>
    </div>
  @endif
  @if($errors->any())
    <div class="alert-banner warning" style="margin-bottom:16px;">
      <i class="ph ph-warning-circle"></i>
      <span>{{ $errors->first() }}</span>
    </div>
  @endif

  <div class="page-header">
    <div>
      <div class="breadcrumb"><i class="ph ph-house-simple"></i><span>/</span><span>Data Warga</span></div>
      <h1>Data Warga</h1>
      <p>Kelola data kepala keluarga dan pengguna warga RT 05</p>
    </div>
    <button type="button" class="btn btn-blue" onclick="openModal('addWargaModal')">
      <i class="ph ph-plus"></i> Tambah Warga
    </button>
  </div>

  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px;">
    <div class="card" style="padding:20px;display:flex;align-items:center;gap:16px;">
      <div style="width:48px;height:48px;border-radius:var(--radius-md);background:var(--blue-light);display:flex;align-items:center;justify-content:center;font-size:22px;color:var(--blue-primary);"><i class="ph ph-users-three"></i></div>
      <div>
        <div style="font-family:var(--font-display);font-size:26px;font-weight:800;color:var(--gray-900);">{{ $stats['total'] }}</div>
        <div style="font-size:12px;color:var(--gray-400);">Total Kepala Keluarga</div>
      </div>
    </div>
    <div class="card" style="padding:20px;display:flex;align-items:center;gap:16px;">
      <div style="width:48px;height:48px;border-radius:var(--radius-md);background:var(--green-light);display:flex;align-items:center;justify-content:center;font-size:22px;color:var(--green-success);"><i class="ph ph-user-check"></i></div>
      <div>
        <div style="font-family:var(--font-display);font-size:26px;font-weight:800;color:var(--gray-900);">{{ $stats['aktif'] }}</div>
        <div style="font-size:12px;color:var(--gray-400);">Akun Aktif</div>
      </div>
    </div>
    <div class="card" style="padding:20px;display:flex;align-items:center;gap:16px;">
      <div style="width:48px;height:48px;border-radius:var(--radius-md);background:var(--orange-light);display:flex;align-items:center;justify-content:center;font-size:22px;color:var(--orange-warn);"><i class="ph ph-user-minus"></i></div>
      <div>
        <div style="font-family:var(--font-display);font-size:26px;font-weight:800;color:var(--gray-900);">{{ $stats['nonaktif'] }}</div>
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
        <a href="{{ route('warga.export.pdf', request()->only('q')) }}" class="btn btn-outline btn-sm" title="Export PDF">
          <i class="ph ph-file-pdf"></i> Export PDF
        </a>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>ID Keluarga</th>
              <th>Nama KK</th>
              <th>No. Rumah</th>
              <th>No. HP</th>
              <th>Pengaduan</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
              <tbody id="wargaTable">
            @forelse($wargas as $w)
              <tr
                data-id="{{ $w->getKey() }}"
                data-id-keluarga="{{ $w->id_keluarga }}"
                data-nama="{{ $w->nama_kepala_keluarga }}"
                data-rumah="{{ $w->nomor_rumah }}"
                data-hp="{{ $w->nomor_hp ?? '-' }}"
                data-alamat="{{ $w->alamat_lengkap }}"
                data-user="{{ $w->nama_pengguna }}"
                data-status="{{ $w->status_akun }}"
                data-pengaduan="{{ $w->jumlah_pengaduan }}"
              >
                <td>
                  <span style="font-family:monospace;font-size:12px;background:var(--gray-100);padding:3px 8px;border-radius:4px;color:var(--gray-600);">{{ $w->id_keluarga }}</span>
                </td>
                <td>
                  <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--blue-primary),var(--blue-dark));display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:white;flex-shrink:0;">{{ mb_substr($w->nama_kepala_keluarga, 0, 1) }}</div>
                    <span style="font-weight:500;">{{ $w->nama_kepala_keluarga }}</span>
                  </div>
                </td>
                <td style="text-align:center;font-weight:600;">{{ $w->nomor_rumah }}</td>
                <td style="font-size:13px;color:var(--gray-500);">{{ \App\Support\PhoneNumber::formatDisplay($w->nomor_hp) }}</td>
                <td style="text-align:center;">
                  <span style="font-size:13px;font-weight:700;color:{{ ($w->jumlah_pengaduan ?? 0) > 0 ? 'var(--blue-primary)' : 'var(--gray-300)' }};">{{ $w->jumlah_pengaduan ?? 0 }}</span>
                </td>
                <td>@include('partials.badge-status', ['status' => $w->status_akun])</td>
                <td>
                  <div style="display:flex;gap:4px;">
                    <button type="button" class="btn btn-outline btn-sm btn-icon" title="Edit" onclick="editWarga(this.closest('tr'))"><i class="ph ph-pencil"></i></button>
                    <form action="{{ route('warga.destroy', $w->getKey()) }}" method="post" style="display:inline;" onsubmit="return confirm('Hapus data warga ini? Pengaduan terkait juga akan dihapus.');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger-outline btn-sm btn-icon" title="Hapus"><i class="ph ph-trash"></i></button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr><td colspan="7"><div class="empty-state"><div class="empty-icon">👥</div><div class="empty-title">Tidak ada data warga</div></div></td></tr>
            @endforelse
            <tr id="wargaEmptyRow" style="display:none;">
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

      {!! $wargas->links('partials.pagination-parete') !!}

      <div style="display:flex;align-items:center;justify-content:space-between;padding-top:8px;flex-wrap:wrap;gap:12px;">
        <span style="font-size:13px;color:var(--gray-400);">Total {{ $wargas->total() }} warga</span>
      </div>
    </div>
  </div>
</div>

<div class="modal-overlay" id="addWargaModal" style="display:none;">
  <div class="modal">
    <div class="modal-header">
      <h3>Tambah Data Warga</h3>
      <button type="button" class="modal-close" onclick="closeModal('addWargaModal')">×</button>
    </div>
    <form id="formTambahWarga" method="post" action="{{ route('warga.store') }}" class="modal-body">
      @csrf
      <div class="alert-banner info" style="margin-bottom:16px;">
        <i class="ph ph-info"></i>
        <span>ID Keluarga dibuat otomatis. Set password awal melalui menu pengguna jika diperlukan.</span>
      </div>
      <div class="grid-2">
        <div class="form-group">
          <label class="form-label" for="tambah_nama">Nama Kepala Keluarga *</label>
          <input id="tambah_nama" name="nama_kepala_keluarga" type="text" class="form-control-plain" required value="{{ old('nama_kepala_keluarga') }}" style="border-radius:var(--radius-sm);" />
        </div>
        <div class="form-group">
          <label class="form-label" for="tambah_rumah">No. Rumah *</label>
          <input id="tambah_rumah" name="nomor_rumah" type="text" class="form-control-plain" required value="{{ old('nomor_rumah') }}" style="border-radius:var(--radius-sm);" />
        </div>
      </div>
      <div class="form-group">
        <label class="form-label" for="tambah_alamat">Alamat Lengkap</label>
        <textarea id="tambah_alamat" name="alamat_lengkap" class="form-control-plain" rows="2" style="height:auto;padding:12px;border-radius:var(--radius-sm);resize:none;">{{ old('alamat_lengkap') }}</textarea>
      </div>
      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">No. HP / WhatsApp</label>
          <div class="phone-prefix-row">
            <span class="phone-prefix-badge">🇮🇩 +62</span>
            <input id="tambah_hp_local" type="tel" class="form-control-plain" placeholder="81291497170" value="{{ \App\Support\PhoneNumber::localPart(old('nomor_hp')) }}" style="border-radius:var(--radius-sm);flex:1;" />
          </div>
          <input type="hidden" name="nomor_hp" id="tambah_hp" value="{{ old('nomor_hp') }}" />
        </div>
        <div class="form-group">
          <label class="form-label" for="tambah_user">Username Login</label>
          <input id="tambah_user" name="nama_pengguna" type="text" class="form-control-plain" value="{{ old('nama_pengguna') }}" style="border-radius:var(--radius-sm);" />
        </div>
      </div>
      <div class="form-group">
        <label class="form-label" for="tambah_status">Status Akun</label>
        <select id="tambah_status" name="status_akun" class="filter-select" style="width:100%;height:42px;">
          <option value="Aktif" @selected(old('status_akun', 'Aktif') === 'Aktif')>Aktif</option>
          <option value="Nonaktif" @selected(old('status_akun') === 'Nonaktif')>Nonaktif</option>
        </select>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;">
        <button type="button" class="btn btn-outline" onclick="closeModal('addWargaModal')">Batal</button>
        <button type="submit" class="btn btn-blue"><i class="ph ph-user-plus"></i> Simpan</button>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="editWargaModal" style="display:none;">
  <div class="modal">
    <div class="modal-header">
      <h3>Edit Data Warga</h3>
      <button type="button" class="modal-close" onclick="closeModal('editWargaModal')">×</button>
    </div>
    <form id="formEditWarga" method="post" class="modal-body">
      @csrf
      @method('PUT')
      <div class="grid-2">
        <div class="form-group">
          <label class="form-label" for="editNama">Nama KK</label>
          <input type="text" name="nama_kepala_keluarga" id="editNama" class="form-control-plain" required style="border-radius:var(--radius-sm);" />
        </div>
        <div class="form-group">
          <label class="form-label" for="editNoRumah">No. Rumah</label>
          <input type="text" name="nomor_rumah" id="editNoRumah" class="form-control-plain" required style="border-radius:var(--radius-sm);" />
        </div>
      </div>
      <div class="form-group">
        <label class="form-label" for="editAlamat">Alamat</label>
        <textarea name="alamat_lengkap" id="editAlamat" class="form-control-plain" rows="2" style="height:auto;padding:12px;border-radius:var(--radius-sm);"></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">No. HP</label>
        <div class="phone-prefix-row">
          <span class="phone-prefix-badge">🇮🇩 +62</span>
          <input type="tel" id="editHpLocal" class="form-control-plain" placeholder="81291497170" style="border-radius:var(--radius-sm);flex:1;" />
        </div>
        <input type="hidden" name="nomor_hp" id="editHp" />
      </div>
      <div class="form-group">
        <label class="form-label" for="editUser">Username</label>
        <input type="text" name="nama_pengguna" id="editUser" class="form-control-plain" style="border-radius:var(--radius-sm);" />
      </div>
      <div class="form-group">
        <label class="form-label" for="editStatus">Status Akun</label>
        <select name="status_akun" id="editStatus" class="filter-select" style="width:100%;height:42px;">
          <option value="Aktif">Aktif</option>
          <option value="Nonaktif">Nonaktif</option>
        </select>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;">
        <button type="button" class="btn btn-outline" onclick="closeModal('editWargaModal')">Batal</button>
        <button type="submit" class="btn btn-blue"><i class="ph ph-floppy-disk"></i> Simpan</button>
      </div>
    </form>
  </div>
</div>

<script src="{{ asset('js/parete.js') }}"></script>
<script>
  initLayout('warga', 'Data Warga');

  function phoneLocalFromStored(hp) {
    if (!hp || hp === '-') return '';
    let d = String(hp).replace(/\D/g, '');
    if (d.startsWith('62')) d = d.slice(2);
    else if (d.startsWith('0')) d = d.slice(1);
    return d;
  }

  function phoneToFull(local) {
    let d = String(local || '').replace(/\D/g, '');
    if (d.startsWith('62')) d = d.slice(2);
    if (d.startsWith('0')) d = d.slice(1);
    return d ? '+62' + d : '';
  }

  function bindPhoneForm(formId, localInputId, hiddenInputId) {
    const form = document.getElementById(formId);
    if (!form) return;
    form.addEventListener('submit', function () {
      const local = document.getElementById(localInputId);
      const hidden = document.getElementById(hiddenInputId);
      if (local && hidden) {
        hidden.value = phoneToFull(local.value);
      }
    });
  }

  bindPhoneForm('formTambahWarga', 'tambah_hp_local', 'tambah_hp');
  bindPhoneForm('formEditWarga', 'editHpLocal', 'editHp');

  function filterWarga(val) {
    const q = (val || '').trim().toLowerCase();
    applyClientFilter('#wargaTable tr[data-id]', 'wargaEmptyRow', tr => {
      const blob = [
        tr.dataset.nama,
        tr.dataset.idKeluarga,
        tr.dataset.rumah,
        tr.dataset.hp,
        tr.dataset.user,
      ].join(' ').toLowerCase();
      return !q || blob.includes(q);
    });
  }

  function editWarga(tr) {
    const id = tr.dataset.id;
    document.getElementById('formEditWarga').action = '{{ url('/warga') }}/' + encodeURIComponent(id);
    document.getElementById('editNama').value = tr.dataset.nama;
    document.getElementById('editNoRumah').value = tr.dataset.rumah;
    document.getElementById('editHpLocal').value = phoneLocalFromStored(tr.dataset.hp);
    document.getElementById('editHp').value = phoneToFull(tr.dataset.hp);
    document.getElementById('editAlamat').value = tr.dataset.alamat || '';
    document.getElementById('editUser').value = tr.dataset.user || '';
    document.getElementById('editStatus').value = tr.dataset.status;
    openModal('editWargaModal');
  }
</script>
</body>
</html>
