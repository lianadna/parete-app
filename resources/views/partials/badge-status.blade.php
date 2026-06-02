@php
  $map = [
    'Terkirim' => ['badge-gray', 'Terkirim'],
    'Diterima' => ['badge-yellow', 'Diterima'],
    'Diproses' => ['badge-blue', 'Diproses'],
    'Selesai' => ['badge-green', 'Selesai'],
    'Ditolak' => ['badge-red', 'Ditolak'],
    'Dibatalkan' => ['badge-gray', 'Dibatalkan'],
    'Aktif' => ['badge-green', 'Aktif'],
    'Nonaktif' => ['badge-gray', 'Nonaktif'],
  ];
  [$cls, $label] = $map[$status] ?? ['badge-gray', $status];
@endphp
<span class="badge {{ $cls }}">{{ $label }}</span>
