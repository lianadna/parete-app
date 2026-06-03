@php
  $cardId = $cardId ?? 'infoRtCard';
  $showEditLink = $showEditLink ?? false;
@endphp

<div class="card" id="{{ $cardId }}">
  <div class="card-header" style="margin-bottom:4px;">
    <span class="card-title">Info RT</span>
    <div style="display:flex;align-items:center;gap:8px;">
      <span class="badge badge-green">Aktif</span>
      @if($showEditLink)
        <a href="{{ route('profil-rt.edit') }}" class="btn btn-outline btn-sm" title="Atur profil RT">
          <i class="ph ph-gear"></i>
        </a>
      @endif
    </div>
  </div>
  <div class="card-body">
    <div class="info-rt-hero">
      <div class="info-rt-hero-title" data-preview="labelRt">{{ $profil->labelRt() }}</div>
      <div class="info-rt-hero-sub" data-preview="kelurahan">{{ $profil->kelurahan }}</div>
      <div class="info-rt-hero-glow"></div>
    </div>
    <div class="info-rows">
      <div class="info-row">
        <span class="info-row-label"><i class="ph ph-user-gear icon"></i> Ketua RT</span>
        <span class="info-row-value" data-preview="nama_ketua_rt">{{ $profil->nama_ketua_rt }}</span>
      </div>
      <div class="info-row">
        <span class="info-row-label"><i class="ph ph-map-pin icon"></i> RW</span>
        <span class="info-row-value" data-preview="labelRw">{{ $profil->labelRw() }}</span>
      </div>
      <div class="info-row">
        <span class="info-row-label"><i class="ph ph-map-trifold icon"></i> Kecamatan</span>
        <span class="info-row-value" data-preview="kecamatan">{{ $profil->kecamatan }}</span>
      </div>
      <div class="info-row">
        <span class="info-row-label"><i class="ph ph-city icon"></i> Kota</span>
        <span class="info-row-value" data-preview="kota">{{ $profil->kota }}</span>
      </div>
      <div class="info-row">
        <span class="info-row-label"><i class="ph ph-globe-hemisphere-west icon"></i> Provinsi</span>
        <span class="info-row-value" data-preview="provinsi">{{ $profil->provinsi }}</span>
      </div>
      <div class="info-row">
        <span class="info-row-label"><i class="ph ph-mailbox icon"></i> Kode Pos</span>
        <span class="info-row-value" data-preview="kode_pos">{{ $profil->kode_pos }}</span>
      </div>
    </div>
  </div>
</div>
