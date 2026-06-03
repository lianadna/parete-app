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
      <div class="breadcrumb"><i class="ph ph-house-simple"></i><span>/</span><span>Informasi</span></div>
      <h1>Informasi & Pengumuman</h1>
      <p>Kelola pengumuman dan jadwal kegiatan RT</p>
    </div>
    <button type="button" class="btn btn-blue" onclick="openModal('addInfoModal')">
      <i class="ph ph-plus"></i> Buat Informasi
    </button>
  </div>

  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
    <div class="filter-bar" style="margin-bottom:0;flex:1;">
      <div class="search-input-bar">
        <i class="ph ph-magnifying-glass icon"></i>
        <input type="search" placeholder="Cari informasi..." oninput="filterInfo(this.value)" />
      </div>
    </div>
  </div>

  <div id="infoGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:16px;">
    @forelse($informasis as $info)
      <div
        class="card info-card"
        style="cursor:pointer;transition:var(--transition);"
        data-id="{{ $info->getKey() }}"
        data-jenis="{{ $info->jenis_informasi }}"
        data-judul="{{ $info->judul_informasi }}"
        data-isi="{{ $info->isi_informasi }}"
        data-publikasi="{{ optional($info->tanggal_publikasi)->format('Y-m-d') }}"
        data-kegiatan="{{ optional($info->tanggal_kegiatan)->format('Y-m-d') }}"
        data-penulis="{{ $info->penulis }}"
        data-gambar-url="{{ $info->gambarPublikUrl() ?? '' }}"
        onclick="showDetailInfo(this)"
      >
        @if($info->gambar_informasi)
          <div style="height:140px;overflow:hidden;background:var(--gray-100);">
            <img src="{{ $info->gambarPublikUrl() }}" alt="" style="width:100%;height:100%;object-fit:cover;" />
          </div>
        @endif
        <div style="padding:20px 22px 16px;">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
            @if($info->jenis_informasi === 'pengumuman')
              <span style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--blue-dark);background:var(--blue-light);padding:4px 10px;border-radius:99px;border:1px solid #90CAF9;">
                <i class="ph ph-megaphone-simple"></i> Pengumuman
              </span>
            @else
              <span style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#E65100;background:var(--yellow-light);padding:4px 10px;border-radius:99px;border:1px solid #FFE082;">
                <i class="ph ph-calendar-check"></i> Kegiatan
              </span>
            @endif
            <span style="font-size:12px;color:var(--gray-400);">{{ optional($info->tanggal_publikasi)->format('d M Y') }}</span>
          </div>
          <h3 style="font-family:var(--font-display);font-size:15px;font-weight:700;color:var(--gray-900);margin-bottom:8px;line-height:1.4;">{{ $info->judul_informasi }}</h3>
          <p style="font-size:13px;color:var(--gray-500);line-height:1.6;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">{{ Str::limit($info->isi_informasi, 220) }}</p>
        </div>
        <div style="border-top:1px solid var(--gray-100);padding:12px 22px;display:flex;align-items:center;justify-content:space-between;" onclick="event.stopPropagation()">
          <span style="font-size:12px;color:var(--gray-400);display:flex;align-items:center;gap:5px;"><i class="ph ph-user"></i> {{ $info->penulis }}</span>
          <div style="display:flex;gap:6px;">
            <button type="button" class="btn btn-outline btn-sm btn-icon" title="Edit" onclick="openEditInfo(this.closest('.info-card'))"><i class="ph ph-pencil"></i></button>
            <form action="{{ route('informasi.destroy', $info->getKey()) }}" method="post" style="display:inline;" onsubmit="return confirm('Hapus informasi ini?');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-danger-outline btn-sm btn-icon" title="Hapus"><i class="ph ph-trash"></i></button>
            </form>
          </div>
        </div>
      </div>
    @empty
      <div style="grid-column:1/-1"><div class="empty-state"><div class="empty-icon">📢</div><div class="empty-title">Belum ada informasi</div></div></div>
    @endforelse
    <div id="infoEmptyFiltered" style="display:none;grid-column:1/-1;">
      <div class="empty-state">
        <div class="empty-icon">🔍</div>
        <div class="empty-title">Data tidak ditemukan</div>
      </div>
    </div>
  </div>

  {!! $informasis->links('partials.pagination-parete') !!}

  @if(!$informasis->isEmpty())
    <div style="margin-top:12px;font-size:13px;color:var(--gray-400);">Total {{ $informasis->total() }} informasi</div>
  @endif
</div>

<div class="modal-overlay" id="addInfoModal" style="display:none;">
  <div class="modal" style="max-width:580px;">
    <div class="modal-header">
      <h3>Buat Informasi Baru</h3>
      <button type="button" class="modal-close" onclick="closeModal('addInfoModal')">×</button>
    </div>
    <form method="post" action="{{ route('informasi.store') }}" class="modal-body" enctype="multipart/form-data">
      @csrf
      <div class="form-group">
        <label class="form-label">Tipe *</label>
        <div class="info-tipe-options">
          <label class="info-tipe-option">
            <input type="radio" name="jenis_informasi" value="pengumuman" checked>
            <i class="ph ph-megaphone-simple"></i> Pengumuman
          </label>
          <label class="info-tipe-option">
            <input type="radio" name="jenis_informasi" value="kegiatan">
            <i class="ph ph-calendar-check"></i> Kegiatan
          </label>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Judul *</label>
        <input type="text" name="judul_informasi" class="form-control-plain" required value="{{ old('judul_informasi') }}" style="border-radius:var(--radius-sm);" />
      </div>
      <div class="form-group">
        <label class="form-label">Isi / Deskripsi *</label>
        <textarea name="isi_informasi" rows="5" class="form-control-plain" required style="height:auto;padding:12px;resize:vertical;border-radius:var(--radius-sm);">{{ old('isi_informasi') }}</textarea>
      </div>
      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">Tanggal Publikasi *</label>
          <input type="date" name="tanggal_publikasi" class="form-control-plain" required value="{{ old('tanggal_publikasi', now()->format('Y-m-d')) }}" style="border-radius:var(--radius-sm);" />
        </div>
        <div class="form-group">
          <label class="form-label">Tanggal Kegiatan</label>
          <input type="date" name="tanggal_kegiatan" class="form-control-plain" value="{{ old('tanggal_kegiatan') }}" style="border-radius:var(--radius-sm);" />
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Penulis *</label>
        <input type="text" name="penulis" class="form-control-plain" required value="{{ old('penulis', 'Admin RT') }}" style="border-radius:var(--radius-sm);" />
      </div>
      <div class="form-group">
        <label class="form-label">Gambar pengumuman</label>
        <input type="file" name="gambar_informasi" accept="image/jpeg,image/png,image/webp" class="form-control-plain" style="border-radius:var(--radius-sm);padding:10px;" />
        <div style="font-size:11px;color:var(--gray-400);margin-top:6px;">JPG, PNG, atau WEBP · maks. 5 MB · tampil di banner aplikasi warga</div>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;">
        <button type="button" class="btn btn-outline" onclick="closeModal('addInfoModal')">Batal</button>
        <button type="submit" class="btn btn-blue"><i class="ph ph-paper-plane-tilt"></i> Publikasikan</button>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="detailInfoModal" style="display:none;">
  <div class="modal" style="max-width:560px;">
    <div class="modal-header">
      <h3 id="diJudul">Judul Info</h3>
      <button type="button" class="modal-close" onclick="closeModal('detailInfoModal')">×</button>
    </div>
    <div class="modal-body">
      <div id="diGambarWrap" style="display:none;margin-bottom:16px;border-radius:12px;overflow:hidden;max-height:220px;">
        <img id="diGambar" src="" alt="Gambar pengumuman" style="width:100%;height:100%;object-fit:cover;display:block;" />
      </div>
      <div style="display:flex;gap:10px;margin-bottom:16px;align-items:center;">
        <span id="diBadge"></span>
        <span style="font-size:12px;color:var(--gray-400);" id="diTanggal"></span>
      </div>
      <p style="font-size:14px;color:var(--gray-700);line-height:1.8;white-space:pre-wrap;" id="diIsi"></p>
      <div style="margin-top:20px;border-top:1px solid var(--gray-100);padding-top:16px;display:flex;gap:10px;justify-content:flex-end;">
        <button type="button" class="btn btn-outline" onclick="closeModal('detailInfoModal'); openEditInfo(window._lastInfoCard);"><i class="ph ph-pencil"></i> Edit</button>
      </div>
    </div>
  </div>
</div>

<div class="modal-overlay" id="editInfoModal" style="display:none;">
  <div class="modal" style="max-width:580px;">
    <div class="modal-header">
      <h3>Edit Informasi</h3>
      <button type="button" class="modal-close" onclick="closeModal('editInfoModal')">×</button>
    </div>
    <form id="formEditInfo" method="post" class="modal-body" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="form-group">
        <label class="form-label">Tipe *</label>
        <select name="jenis_informasi" id="editInfoJenis" class="filter-select" style="width:100%;height:42px;" required>
          <option value="pengumuman">Pengumuman</option>
          <option value="kegiatan">Kegiatan</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Judul *</label>
        <input type="text" name="judul_informasi" id="editInfoJudul" class="form-control-plain" required style="border-radius:var(--radius-sm);" />
      </div>
      <div class="form-group">
        <label class="form-label">Isi *</label>
        <textarea name="isi_informasi" id="editInfoIsi" rows="5" class="form-control-plain" required style="height:auto;padding:12px;border-radius:var(--radius-sm);"></textarea>
      </div>
      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">Tanggal Publikasi *</label>
          <input type="date" name="tanggal_publikasi" id="editInfoPublikasi" class="form-control-plain" required style="border-radius:var(--radius-sm);" />
        </div>
        <div class="form-group">
          <label class="form-label">Tanggal Kegiatan</label>
          <input type="date" name="tanggal_kegiatan" id="editInfoKegiatan" class="form-control-plain" style="border-radius:var(--radius-sm);" />
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Penulis *</label>
        <input type="text" name="penulis" id="editInfoPenulis" class="form-control-plain" required style="border-radius:var(--radius-sm);" />
      </div>
      <div class="form-group">
        <label class="form-label">Gambar pengumuman</label>
        <div id="editInfoGambarPreview" style="display:none;margin-bottom:10px;border-radius:12px;overflow:hidden;border:1px solid var(--gray-200);max-height:180px;">
          <img id="editInfoGambarImg" src="" alt="Gambar saat ini" style="width:100%;max-height:180px;object-fit:cover;display:block;" />
        </div>
        <input type="file" name="gambar_informasi" accept="image/jpeg,image/png,image/webp" class="form-control-plain" style="border-radius:var(--radius-sm);padding:10px;" />
        <div style="font-size:11px;color:var(--gray-400);margin-top:6px;">Kosongkan jika tidak ingin mengganti gambar</div>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;">
        <button type="button" class="btn btn-outline" onclick="closeModal('editInfoModal')">Batal</button>
        <button type="submit" class="btn btn-blue">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script src="{{ asset('js/parete.js') }}"></script>
<script>
  initLayout('informasi', 'Informasi & Pengumuman');

  function filterInfo(val) {
    const q = (val || '').trim().toLowerCase();
    applyClientFilter('.info-card', 'infoEmptyFiltered', card => {
      const blob = [card.dataset.judul, card.dataset.isi, card.dataset.penulis, card.dataset.jenis].join(' ').toLowerCase();
      return !q || blob.includes(q);
    });
  }

  function showDetailInfo(card) {
    window._lastInfoCard = card;
    document.getElementById('diJudul').textContent = card.dataset.judul;
    document.getElementById('diIsi').textContent = card.dataset.isi;
    const tgl = card.dataset.publikasi || '';
    document.getElementById('diTanggal').textContent = tgl + ' · ' + card.dataset.penulis;
    const gambarWrap = document.getElementById('diGambarWrap');
    const gambarImg = document.getElementById('diGambar');
    const gambarUrl = card.dataset.gambarUrl || '';
    if (gambarUrl) {
      gambarWrap.style.display = 'block';
      gambarImg.src = gambarUrl;
    } else {
      gambarWrap.style.display = 'none';
      gambarImg.removeAttribute('src');
    }
    const jenis = card.dataset.jenis;
    if (jenis === 'pengumuman') {
      document.getElementById('diBadge').innerHTML = '<span style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--blue-dark);background:var(--blue-light);padding:4px 10px;border-radius:99px;">Pengumuman</span>';
    } else {
      document.getElementById('diBadge').innerHTML = '<span style="font-size:11px;font-weight:700;text-transform:uppercase;color:#E65100;background:var(--yellow-light);padding:4px 10px;border-radius:99px;">Kegiatan</span>';
    }
    openModal('detailInfoModal');
  }

  function openEditInfo(card) {
    if (!card) return;
    const id = card.dataset.id;
    document.getElementById('formEditInfo').action = '{{ url('/informasi') }}/' + encodeURIComponent(id);
    document.getElementById('editInfoJenis').value = card.dataset.jenis;
    document.getElementById('editInfoJudul').value = card.dataset.judul;
    document.getElementById('editInfoIsi').value = card.dataset.isi;
    document.getElementById('editInfoPublikasi').value = card.dataset.publikasi || '';
    document.getElementById('editInfoKegiatan').value = card.dataset.kegiatan || '';
    document.getElementById('editInfoPenulis').value = card.dataset.penulis || '';
    const gambarUrl = card.dataset.gambarUrl || '';
    const preview = document.getElementById('editInfoGambarPreview');
    const previewImg = document.getElementById('editInfoGambarImg');
    if (gambarUrl) {
      preview.style.display = 'block';
      previewImg.src = gambarUrl;
    } else {
      preview.style.display = 'none';
      previewImg.removeAttribute('src');
    }
    openModal('editInfoModal');
  }
</script>
</body>
</html>
