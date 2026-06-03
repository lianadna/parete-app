<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Warga</title>
  <style>
    * { box-sizing: border-box; }
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 10px;
      color: #1e293b;
      margin: 0;
      padding: 0;
    }
    .header {
      border-bottom: 2px solid #1565c0;
      padding-bottom: 10px;
      margin-bottom: 14px;
    }
    .header h1 {
      margin: 0 0 4px;
      font-size: 18px;
      color: #0d47a1;
    }
    .header p {
      margin: 2px 0;
      color: #64748b;
      font-size: 9px;
    }
    .meta {
      margin-bottom: 12px;
      width: 100%;
    }
    .meta td {
      padding: 4px 8px;
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      font-size: 9px;
    }
    .meta strong { color: #334155; }
    table.data {
      width: 100%;
      border-collapse: collapse;
    }
    table.data th {
      background: #1565c0;
      color: #fff;
      padding: 7px 6px;
      text-align: left;
      font-size: 9px;
      font-weight: bold;
    }
    table.data td {
      padding: 6px;
      border-bottom: 1px solid #e2e8f0;
      vertical-align: top;
    }
    table.data tr:nth-child(even) td {
      background: #f8fafc;
    }
    .mono { font-family: DejaVu Sans Mono, monospace; font-size: 8px; }
    .center { text-align: center; }
    .badge {
      display: inline-block;
      padding: 2px 6px;
      border-radius: 4px;
      font-size: 8px;
      font-weight: bold;
    }
    .badge-aktif { background: #e8f5e9; color: #2e7d32; }
    .badge-nonaktif { background: #f5f5f5; color: #757575; }
    .footer {
      margin-top: 12px;
      font-size: 8px;
      color: #94a3b8;
      text-align: right;
    }
  </style>
</head>
<body>
  <div class="header">
    <h1>Data Warga — {{ $profil->labelRt() }} {{ $profil->kelurahan }}</h1>
    <p>{{ $profil->ringkasanWilayah() }}</p>
    <p>Dicetak: {{ $exportedAt->format('d/m/Y H:i') }} WIB · Parete Admin {{ $adminNama }}</p>
    @if($search !== '')
      <p>Filter pencarian: «{{ $search }}»</p>
    @endif
  </div>

  <table class="meta" cellpadding="0" cellspacing="0">
    <tr>
      <td><strong>Total KK</strong><br>{{ $stats['total'] }}</td>
      <td><strong>Aktif</strong><br>{{ $stats['aktif'] }}</td>
      <td><strong>Nonaktif</strong><br>{{ $stats['nonaktif'] }}</td>
      <td><strong>Ketua RT</strong><br>{{ $profil->nama_ketua_rt }}</td>
    </tr>
  </table>

  <table class="data">
    <thead>
      <tr>
        <th style="width:14%;">ID Keluarga</th>
        <th style="width:22%;">Nama KK</th>
        <th style="width:7%;" class="center">No. Rumah</th>
        <th style="width:12%;">No. HP</th>
        <th style="width:28%;">Alamat</th>
        <th style="width:7%;" class="center">Pengaduan</th>
        <th style="width:10%;" class="center">Status</th>
      </tr>
    </thead>
    <tbody>
      @forelse($wargas as $w)
        <tr>
          <td class="mono">{{ $w->id_keluarga }}</td>
          <td>{{ $w->nama_kepala_keluarga }}</td>
          <td class="center">{{ $w->nomor_rumah }}</td>
          <td>{{ \App\Support\PhoneNumber::formatDisplay($w->nomor_hp) ?: '—' }}</td>
          <td>{{ $w->alamat_lengkap ?: '—' }}</td>
          <td class="center">{{ $w->jumlah_pengaduan ?? 0 }}</td>
          <td class="center">
            <span class="badge {{ $w->status_akun === 'Aktif' ? 'badge-aktif' : 'badge-nonaktif' }}">
              {{ $w->status_akun }}
            </span>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="center">Tidak ada data warga.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div class="footer">
    Dokumen ini dihasilkan otomatis dari sistem Parete — {{ $wargas->count() }} baris data.
  </div>
</body>
</html>
