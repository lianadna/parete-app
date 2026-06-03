<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Pengaduan</title>
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

  {{-- Root-relative URL: ikut origin browser (mis. http://127.0.0.1:8000), bukan APP_URL seperti http://localhost --}}
  @php
    $urlBerkasPublik = static function (?string $path): ?string {
      if (!$path || !is_string(trim($path))) {
        return null;
      }
      $pathNorm = ltrim(str_replace('\\', '/', trim($path)), '/');
      try {
        if (! \Illuminate\Support\Facades\Storage::disk('public')->exists($pathNorm)) {
          return null;
        }
      } catch (\Throwable) {
        return null;
      }

      return '/storage/'.$pathNorm;
    };

    $rows = $pengaduans->map(function ($p) use ($urlBerkasPublik) {
      $lampiran = collect($p->lampiran_gambar ?? [])->filter()->values()->map(function ($path) use ($urlBerkasPublik) {
        return $urlBerkasPublik(is_string($path) ? $path : null);
      })->filter()->values()->all();

      $buktiPath = is_string($p->bukti_penyelesaian ?? null) ? $p->bukti_penyelesaian : null;

      return [
        'id' => (string) $p->getKey(),
        'nomor' => $p->nomor_pengaduan,
        'warga' => $p->nama_pelapor,
        'judul' => $p->judul_pengaduan,
        'topik' => $p->topik,
        'lokasi' => $p->lokasi_kejadian,
        'tanggal' => optional($p->tanggal_dibuat)->format('d M Y') ?? '-',
        'status' => $p->status_pengaduan,
        'deskripsi' => $p->deskripsi,
        'lampiran' => $lampiran,
        'catatan_selesai' => $p->catatan_selesai,
        'alasan_ditolak' => $p->alasan_ditolak,
        'buktiUrl' => $urlBerkasPublik($buktiPath),
      ];
    })->values();
  @endphp

  <div class="page-header">
    <div>
      <div class="breadcrumb"><i class="ph ph-house-simple"></i><span>/</span><span>Pengaduan</span></div>
      <h1>Pengaduan Warga</h1>
      <p>Kelola dan tindak lanjuti semua laporan dari warga</p>
    </div>
  </div>

  <style>
    .p-stats-summary{display:flex;align-items:center;gap:10px;margin-bottom:14px;font-size:14px;color:var(--gray-600);}
    .p-stats-summary strong{font-variant-numeric:tabular-nums;font-weight:800;font-size:22px;color:var(--gray-900);font-family:var(--font-display);letter-spacing:-0.35px;margin-right:6px;}
    .p-stats-grid{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:12px;margin-bottom:20px;}
    @media (max-width:1200px){.p-stats-grid{grid-template-columns:repeat(3,minmax(0,1fr));}}
    @media (max-width:600px){.p-stats-grid{grid-template-columns:repeat(2,minmax(0,1fr));}}
    .p-stat{background:#fff;border-radius:var(--radius-md);padding:14px 16px;border:1px solid var(--gray-100);display:flex;align-items:flex-start;gap:12px;min-height:92px;}
    .p-stat-icon{flex-shrink:0;width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;}
    .p-stat-val{font-size:20px;font-weight:800;font-family:var(--font-display);letter-spacing:-0.3px;}
    .p-stat-title{font-size:13px;font-weight:700;color:var(--gray-800);margin-top:2px;}
    .p-stat-hint{font-size:10px;line-height:1.35;color:var(--gray-400);margin-top:4px;}
  </style>

  <div class="p-stats-summary">
    <i class="ph ph-stack-simple" style="font-size:20px;color:var(--gray-400);flex-shrink:0;"></i>
    <span style="flex:1;line-height:1.45;"><strong>{{ $stats['total'] }}</strong> laporan pengaduan dalam sistem</span>
  </div>

  <div class="p-stats-grid">
    <div class="p-stat">
      <div class="p-stat-icon" style="background:#ECEFF1;color:#546E7A;"><i class="ph ph-paper-plane-tilt"></i></div>
      <div>
        <div class="p-stat-val" style="color:#455A64;">{{ $stats['terkirim'] }}</div>
        <div class="p-stat-title">Terkirim</div>
        <div class="p-stat-hint">Dari warga, belum dibuka admin</div>
      </div>
    </div>
    <div class="p-stat">
      <div class="p-stat-icon" style="background:var(--orange-light);color:var(--orange-warn);"><i class="ph ph-eye"></i></div>
      <div>
        <div class="p-stat-val" style="color:var(--orange-warn);">{{ $stats['diterima'] }}</div>
        <div class="p-stat-title">Diterima</div>
        <div class="p-stat-hint">Sudah dibuka · siap ditindak</div>
      </div>
    </div>
    <div class="p-stat">
      <div class="p-stat-icon" style="background:var(--blue-light);color:var(--blue-primary);"><i class="ph ph-spinner-gap"></i></div>
      <div>
        <div class="p-stat-val" style="color:var(--blue-primary);">{{ $stats['diproses'] }}</div>
        <div class="p-stat-title">Diproses</div>
        <div class="p-stat-hint">Sedang ditangani</div>
      </div>
    </div>
    <div class="p-stat">
      <div class="p-stat-icon" style="background:var(--green-light);color:var(--green-success);"><i class="ph ph-check-circle"></i></div>
      <div>
        <div class="p-stat-val" style="color:var(--green-success);">{{ $stats['selesai'] }}</div>
        <div class="p-stat-title">Selesai</div>
        <div class="p-stat-hint">Sudah ditutup dengan catatan</div>
      </div>
    </div>
    <div class="p-stat">
      <div class="p-stat-icon" style="background:#FFEBEE;color:#C62828;"><i class="ph ph-x-circle"></i></div>
      <div>
        <div class="p-stat-val" style="color:#C62828;">{{ $stats['ditolak'] }}</div>
        <div class="p-stat-title">Ditolak</div>
        <div class="p-stat-hint">Dihentikan sesuai alasan</div>
      </div>
    </div>
    <div class="p-stat">
      <div class="p-stat-icon" style="background:#ECEFF1;color:#607D8B;"><i class="ph ph-prohibit"></i></div>
      <div>
        <div class="p-stat-val" style="color:#607D8B;">{{ $stats['dibatalkan'] }}</div>
        <div class="p-stat-title">Dibatalkan</div>
        <div class="p-stat-hint">Dibatalkan oleh warga</div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><span class="card-title">Daftar Pengaduan</span></div>
    <div class="card-body">
      <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
        <div class="filter-bar" style="margin-bottom:0;flex:1;">
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
            <option>Ditolak</option>
            <option>Dibatalkan</option>
          </select>
        </div>
      </div>

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
          <tbody id="pengaduanTable">
            @forelse($pengaduans as $p)
              <tr
                data-id="{{ $p->getKey() }}"
                data-nomor="{{ $p->nomor_pengaduan }}"
                data-warga="{{ $p->nama_pelapor }}"
                data-judul="{{ $p->judul_pengaduan }}"
                data-topik="{{ $p->topik }}"
                data-lokasi="{{ $p->lokasi_kejadian }}"
                data-tanggal="{{ optional($p->tanggal_dibuat)->format('d M Y') ?? '-' }}"
                data-status="{{ $p->status_pengaduan }}"
              >
                <td><span style="font-size:12px;font-weight:700;color:var(--gray-400);">#{{ $p->nomor_pengaduan }}</span></td>
                <td>
                  <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:28px;height:28px;border-radius:50%;background:var(--blue-light);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:var(--blue-dark);">{{ mb_substr($p->nama_pelapor, 0, 1) }}</div>
                    <span style="font-size:13px;">{{ $p->nama_pelapor }}</span>
                  </div>
                </td>
                <td style="font-weight:500;max-width:180px;">{{ $p->judul_pengaduan }}</td>
                <td><span style="font-size:12px;color:var(--gray-500);">{{ $p->topik }}</span></td>
                <td style="font-size:12px;color:var(--gray-500);">{{ $p->lokasi_kejadian }}</td>
                <td style="font-size:12px;color:var(--gray-400);">{{ optional($p->tanggal_dibuat)->format('d M Y') ?? '-' }}</td>
                <td class="td-status-pengaduan">@include('partials.badge-status', ['status' => $p->status_pengaduan])</td>
                <td>
                  <button type="button" class="btn btn-outline btn-sm btn-icon" title="Lihat detail" onclick="detailPengaduan(this.closest('tr'));"><i class="ph ph-eye"></i></button>
                </td>
              </tr>
            @empty
              <tr><td colspan="8"><div class="empty-state"><div class="empty-icon">📋</div><div class="empty-title">Tidak ada pengaduan</div></div></td></tr>
            @endforelse
            <tr id="pengaduanEmptyRow" style="display:none;">
              <td colspan="8">
                <div class="empty-state">
                  <div class="empty-icon">🔍</div>
                  <div class="empty-title">Data tidak ditemukan</div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      {!! $pengaduans->links('partials.pagination-parete') !!}

      @if(!$pengaduans->isEmpty())
        <div style="padding-top:4px;font-size:13px;color:var(--gray-400);">Total {{ $pengaduans->total() }} pengaduan</div>
      @endif
    </div>
  </div>
</div>

<div class="modal-overlay" id="detailModal" style="display:none;">
  <div class="modal" style="max-width:700px;">
    <div class="modal-header">
      <h3>Detail Pengaduan</h3>
      <button type="button" class="modal-close" onclick="closeModal('detailModal')">×</button>
    </div>
    <div class="modal-body">
      <div style="background:var(--gray-50);border-radius:var(--radius-md);padding:16px;margin-bottom:20px;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px;">
          <div>
            <span style="font-size:12px;font-weight:700;color:var(--gray-400);" id="dAduId">#</span>
            <div style="font-family:var(--font-display);font-size:18px;font-weight:700;color:var(--gray-900);margin-top:4px;" id="dJudul"></div>
          </div>
          <div id="dStatus"></div>
        </div>
        <div style="display:flex;gap:20px;margin-top:12px;flex-wrap:wrap;">
          <span style="font-size:12px;color:var(--gray-500);display:flex;align-items:center;gap:5px;"><i class="ph ph-tag"></i> <span id="dTopik"></span></span>
          <span style="font-size:12px;color:var(--gray-500);display:flex;align-items:center;gap:5px;"><i class="ph ph-map-pin"></i> <span id="dLokasi"></span></span>
          <span style="font-size:12px;color:var(--gray-500);display:flex;align-items:center;gap:5px;"><i class="ph ph-calendar"></i> <span id="dTanggal"></span></span>
          <span style="font-size:12px;color:var(--gray-500);display:flex;align-items:center;gap:5px;"><i class="ph ph-user"></i> <span id="dWarga"></span></span>
        </div>
      </div>
      <div style="font-size:13px;font-weight:700;color:var(--gray-700);margin-bottom:8px;">Deskripsi</div>
      <p style="font-size:13px;color:var(--gray-600);line-height:1.7;margin-bottom:16px;" id="dDeskripsi"></p>

      <div id="dLampiranWrap" style="display:none;margin-bottom:20px;">
        <div style="font-size:13px;font-weight:700;color:var(--gray-700);margin-bottom:10px;">Lampiran dari warga</div>
        <div id="dLampiranGrid" style="display:flex;flex-wrap:wrap;gap:8px;"></div>
      </div>

      <div id="dRiwayatSelesai" style="display:none;border-top:1px solid var(--gray-100);padding-top:16px;margin-bottom:16px;">
        <div style="font-size:13px;font-weight:700;color:var(--gray-700);margin-bottom:8px;">Penyelesaian</div>
        <p style="font-size:13px;color:var(--gray-600);line-height:1.7;margin-bottom:10px;" id="dCatatanSelesai"></p>
        <div style="font-size:12px;font-weight:600;color:var(--gray-700);margin-bottom:6px;">Bukti</div>
        <div id="dBuktiThumbWrap" style="display:none;">
          <div style="display:flex;flex-direction:column;gap:10px;">
            <a id="dBuktiOpenFull" href="#" target="_blank" rel="noopener noreferrer" class="btn btn-outline btn-sm" style="width:fit-content;">Buka / unduh bukti ukuran penuh</a>
            <a id="dBuktiThumbLink" href="#" target="_blank" rel="noopener noreferrer" style="display:inline-block;line-height:0;border-radius:var(--radius-sm);border:1px solid var(--gray-100);overflow:hidden;cursor:pointer;max-width:200px;background:var(--gray-50);min-height:48px;" title="Buka ukuran penuh di tab baru">
              <img id="dBuktiThumb" alt="Pratinjau bukti" style="display:block;max-height:96px;max-width:200px;width:auto;height:auto;object-fit:contain;" />
            </a>
            <span id="dBuktiImgGagal" style="display:none;font-size:12px;color:var(--orange-warn);">Pratinjau tidak dimuat · gunakan tombol «Buka / unduh bukti ukuran penuh» di atas.</span>
          </div>
          <div style="font-size:11px;color:var(--gray-400);margin-top:8px;">Klik gambar atau tautan untuk melihat ukuran penuh (tab baru)</div>
        </div>
      </div>

      <div id="dRiwayatDitolak" style="display:none;border-top:1px solid var(--gray-100);padding-top:16px;margin-bottom:16px;">
        <div style="font-size:13px;font-weight:700;color:var(--gray-700);margin-bottom:8px;">Alasan penolakan</div>
        <p style="font-size:13px;color:var(--gray-600);line-height:1.7;" id="dAlasanDitolak"></p>
      </div>

      <div id="blokFormProses" style="border-top:1px solid var(--gray-100);padding-top:20px;">
        <div style="font-size:13px;font-weight:700;color:var(--gray-700);margin-bottom:12px;">Perbarui proses</div>
        <form id="formStatusPengaduan" method="post" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          <input type="hidden" name="pemutakhiran_status" value="1" />
          <div style="margin-bottom:12px;">
            <label class="form-label">Langkah berikutnya</label>
            <select name="status_pengaduan" class="filter-select" style="width:100%;height:42px;" id="detailStatusSelect"></select>
            <span style="font-size:11px;color:var(--gray-400);display:block;margin-top:6px;">Alur berurutan dari diterima: Diproses → Selesai, atau Anda dapat menolak kapan saja setelah diterima.</span>
          </div>
          <div id="fieldSelesai" style="display:none;margin-bottom:12px;">
            <div class="form-group">
              <label class="form-label">Catatan penyelesaian *</label>
              <textarea name="catatan_selesai" id="inputCatatanSelesai" rows="3" class="form-control-plain" style="height:auto;padding:12px;border-radius:var(--radius-sm);" placeholder="Jelaskan tindak lanjut atau hasil di lapangan…"></textarea>
            </div>
            <div class="form-group">
              <label class="form-label">Unggah bukti penyelesaian *</label>
              <input type="file" name="bukti_selesai" id="inputBuktiSelesai" accept=".jpg,.jpeg,.png,.heic,image/jpeg,image/png,image/heic,image/heif" class="form-control-plain" style="border-radius:var(--radius-sm);" />
              <span style="font-size:11px;color:var(--gray-400);display:block;margin-top:6px;">Maks. 10&nbsp;MB · JPG, JPEG, PNG, atau HEIC</span>
            </div>
          </div>
          <div id="fieldDitolak" style="display:none;margin-bottom:12px;">
            <label class="form-label">Alasan penolakan *</label>
            <textarea name="alasan_ditolak" id="inputAlasanDitolak" rows="3" class="form-control-plain" style="height:auto;padding:12px;border-radius:var(--radius-sm);" placeholder="Jelaskan dengan singkat alasannya…"></textarea>
          </div>
          <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button type="button" class="btn btn-outline" onclick="closeModal('detailModal')">Tutup</button>
            <button type="submit" class="btn btn-blue" id="btnSimpanProses"><i class="ph ph-floppy-disk"></i> Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="{{ asset('js/parete.js') }}"></script>
<script>
  initLayout('pengaduan', 'Pengaduan Warga');

  const pengaduanRows = @json($rows);
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const urlDibuka = @json(url('/pengaduan'));

  function badgeHtml(status) {
    const map = {
      'Terkirim': ['badge-gray', 'Terkirim'],
      'Diterima': ['badge-yellow', 'Diterima'],
      'Diproses': ['badge-blue', 'Diproses'],
      'Selesai': ['badge-green', 'Selesai'],
      'Ditolak': ['badge-red', 'Ditolak'],
      'Dibatalkan': ['badge-gray', 'Dibatalkan'],
    };
    const [c, l] = map[status] || ['badge-gray', status];
    return `<span class="badge ${c}">${l}</span>`;
  }

  function rowPayload(id) {
    return pengaduanRows.find(r => r.id === id) || {};
  }

  function isiSelectLangkah(status) {
    const sel = document.getElementById('detailStatusSelect');
    sel.innerHTML = '';
    const opt = (v, t) => {
      const o = document.createElement('option');
      o.value = v;
      o.textContent = t;
      sel.appendChild(o);
    };
    if (status === 'Diterima') {
      opt('Diproses', 'Diproses');
      opt('Ditolak', 'Ditolak');
    } else if (status === 'Diproses') {
      opt('Selesai', 'Selesai');
      opt('Ditolak', 'Ditolak');
    }
  }

  function sinkronFieldKondisional() {
    const v = document.getElementById('detailStatusSelect').value;
    const elS = document.getElementById('fieldSelesai');
    const elD = document.getElementById('fieldDitolak');
    elS.style.display = v === 'Selesai' ? 'block' : 'none';
    elD.style.display = v === 'Ditolak' ? 'block' : 'none';
    const cat = document.getElementById('inputCatatanSelesai');
    const bukt = document.getElementById('inputBuktiSelesai');
    const als = document.getElementById('inputAlasanDitolak');
    if (v !== 'Selesai') { cat.removeAttribute('required'); bukt.removeAttribute('required'); }
    else { cat.setAttribute('required', 'required'); bukt.setAttribute('required', 'required'); }
    if (v !== 'Ditolak') { als.removeAttribute('required'); }
    else { als.setAttribute('required', 'required'); }
  }

  document.getElementById('detailStatusSelect')?.addEventListener('change', sinkronFieldKondisional);

  async function detailPengaduan(tr) {
    const id = tr.dataset.id;
    if (!id) return;
    try {
      if (tr.dataset.status === 'Terkirim') {
        const fd = new FormData();
        fd.append('_token', csrf);
        const res = await fetch(urlDibuka + '/' + encodeURIComponent(id) + '/dibuka', {
          method: 'POST',
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          body: fd,
        });
        if (!res.ok) throw new Error('Gagal menandai diterima.');
        const j = await res.json();
        tr.dataset.status = j.status_pengaduan;
        const tdSt = tr.querySelector('.td-status-pengaduan');
        if (tdSt) tdSt.innerHTML = badgeHtml(j.status_pengaduan);
      }
    } catch (e) {
      alert(String(e.message || e));
      return;
    }

    showDetail(tr);
  }

  function showDetail(tr) {
    const id = tr.dataset.id;
    const r = rowPayload(id);
    const st = tr.dataset.status;

    document.getElementById('formStatusPengaduan').action = urlDibuka.replace(/\/$/, '') + '/' + encodeURIComponent(id);
    document.getElementById('dAduId').textContent = '#' + tr.dataset.nomor;
    document.getElementById('dJudul').textContent = tr.dataset.judul;
    document.getElementById('dStatus').innerHTML = badgeHtml(st);
    document.getElementById('dTopik').textContent = tr.dataset.topik;
    document.getElementById('dLokasi').textContent = tr.dataset.lokasi;
    document.getElementById('dTanggal').textContent = tr.dataset.tanggal;
    document.getElementById('dWarga').textContent = tr.dataset.warga;
    document.getElementById('dDeskripsi').textContent = r.deskripsi || '';

    const wrapL = document.getElementById('dLampiranWrap');
    const gridL = document.getElementById('dLampiranGrid');
    gridL.innerHTML = '';
    if (Array.isArray(r.lampiran) && r.lampiran.length) {
      wrapL.style.display = 'block';
      r.lampiran.forEach(u => {
        if (!u) return;
        const a = document.createElement('a');
        a.href = u;
        a.target = '_blank';
        a.rel = 'noopener noreferrer';
        a.style.display = 'block';
        const img = document.createElement('img');
        img.src = u;
        img.alt = 'Lampiran';
        img.style.maxHeight = '96px';
        img.style.borderRadius = 'var(--radius-sm)';
        img.style.border = '1px solid var(--gray-100)';
        a.appendChild(img);
        gridL.appendChild(a);
      });
    } else {
      wrapL.style.display = 'none';
    }

    const rs = document.getElementById('dRiwayatSelesai');
    const rd = document.getElementById('dRiwayatDitolak');
    rs.style.display = 'none';
    rd.style.display = 'none';
    const wrapB = document.getElementById('dBuktiThumbWrap');
    wrapB.style.display = 'none';

    if (st === 'Selesai') {
      rs.style.display = 'block';
      document.getElementById('dCatatanSelesai').textContent = r.catatan_selesai || '—';
      if (r.buktiUrl) {
        const thumb = document.getElementById('dBuktiThumb');
        const a = document.getElementById('dBuktiThumbLink');
        const openFull = document.getElementById('dBuktiOpenFull');
        const gagal = document.getElementById('dBuktiImgGagal');
        openFull.href = r.buktiUrl;
        a.href = r.buktiUrl;
        a.style.display = 'inline-block';
        gagal.style.display = 'none';
        thumb.onload = () => { gagal.style.display = 'none'; };
        thumb.onerror = () => {
          thumb.style.display = 'none';
          a.style.display = 'none';
          gagal.style.display = 'block';
        };
        thumb.removeAttribute('src');
        thumb.style.display = 'block';
        thumb.src = r.buktiUrl;
        wrapB.style.display = 'block';
      }
    } else if (st === 'Ditolak') {
      rd.style.display = 'block';
      document.getElementById('dAlasanDitolak').textContent = r.alasan_ditolak || '—';
    }

    const blokForm = document.getElementById('blokFormProses');
    if (['Selesai', 'Ditolak', 'Dibatalkan'].includes(st)) {
      blokForm.style.display = 'none';
    } else {
      blokForm.style.display = 'block';
      isiSelectLangkah(st);
      document.getElementById('detailStatusSelect').value = document.getElementById('detailStatusSelect').options[0]?.value || '';
      document.getElementById('inputCatatanSelesai').value = '';
      document.getElementById('inputBuktiSelesai').value = '';
      document.getElementById('inputAlasanDitolak').value = '';
      sinkronFieldKondisional();
    }

    openModal('detailModal');
  }

  let pengaduanSearch = '';
  let pengaduanTopik = '';
  let pengaduanStatus = '';

  function applyPengaduanFilters() {
    const q = pengaduanSearch.trim().toLowerCase();
    applyClientFilter('#pengaduanTable tr[data-id]', 'pengaduanEmptyRow', tr => {
      const blob = [tr.dataset.judul, tr.dataset.warga, tr.dataset.nomor, tr.dataset.topik, tr.dataset.lokasi].join(' ').toLowerCase();
      const matchSearch = !q || blob.includes(q);
      const matchTopik = !pengaduanTopik || tr.dataset.topik === pengaduanTopik;
      const matchStatus = !pengaduanStatus || tr.dataset.status === pengaduanStatus;
      return matchSearch && matchTopik && matchStatus;
    });
  }

  function filterTable(val) {
    pengaduanSearch = val || '';
    applyPengaduanFilters();
  }

  function filterTopik(val) {
    pengaduanTopik = val || '';
    applyPengaduanFilters();
  }

  function filterStatus(val) {
    pengaduanStatus = val || '';
    applyPengaduanFilters();
  }
</script>
</body>
</html>
