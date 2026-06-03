<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Profil RT</title>
  <link rel="stylesheet" href="{{ asset('css/parete.css') }}" />
  <script src="https://unpkg.com/@phosphor-icons/web@2.1.1/src/index.js" defer></script>
</head>
<body>

@include('partials.app-open')

  @if(session('success'))
    <div class="alert-banner info" style="margin-bottom:16px;"><i class="ph ph-check-circle"></i><span>{{ session('success') }}</span></div>
  @endif
  @if($errors->any())
    <div class="alert-banner warning" style="margin-bottom:16px;"><i class="ph ph-warning-circle"></i><span>{{ $errors->first() }}</span></div>
  @endif

  <div class="page-header">
    <div>
      <div class="breadcrumb"><i class="ph ph-house-simple"></i><span>/</span><span>Profil RT</span></div>
      <h1>Profil RT</h1>
      <p>Atur identitas dan wilayah administrasi RT</p>
    </div>
  </div>

  <div class="card profil-rt-form-card">
      <div class="card-header">
        <span class="card-title"><i class="ph ph-gear" style="margin-right:6px;"></i> Pengaturan Profil</span>
      </div>
      <div class="card-body">
        <form method="post" action="{{ route('profil-rt.update') }}" id="formProfilRt">
          @csrf
          @method('PUT')

          <div class="profil-rt-form-grid">
            <div class="form-group">
              <label class="form-label" for="nama_ketua_rt"><i class="ph ph-user-gear" style="margin-right:4px;color:var(--blue-primary);"></i> Nama Ketua RT *</label>
              <input type="text" id="nama_ketua_rt" name="nama_ketua_rt" class="form-control-plain" value="{{ old('nama_ketua_rt', $profil->nama_ketua_rt) }}" required maxlength="120" />
            </div>

            <div class="form-group profil-rt-form-row-2">
              <div>
                <label class="form-label" for="nomor_rt"><i class="ph ph-house" style="margin-right:4px;color:var(--blue-primary);"></i> Nomor RT *</label>
                <input type="text" id="nomor_rt" name="nomor_rt" class="form-control-plain" value="{{ old('nomor_rt', $profil->nomor_rt) }}" required maxlength="3" inputmode="numeric" pattern="\d{1,3}" />
              </div>
              <div>
                <label class="form-label" for="nomor_rw"><i class="ph ph-map-pin" style="margin-right:4px;color:var(--blue-primary);"></i> Nomor RW *</label>
                <input type="text" id="nomor_rw" name="nomor_rw" class="form-control-plain" value="{{ old('nomor_rw', $profil->nomor_rw) }}" required maxlength="3" inputmode="numeric" pattern="\d{1,3}" />
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" for="kelurahan"><i class="ph ph-buildings" style="margin-right:4px;color:var(--blue-primary);"></i> Kelurahan *</label>
              <input type="text" id="kelurahan" name="kelurahan" class="form-control-plain" value="{{ old('kelurahan', $profil->kelurahan) }}" required maxlength="120" />
            </div>

            <div class="form-group">
              <label class="form-label" for="kecamatan"><i class="ph ph-map-trifold" style="margin-right:4px;color:var(--blue-primary);"></i> Kecamatan *</label>
              <input type="text" id="kecamatan" name="kecamatan" class="form-control-plain" value="{{ old('kecamatan', $profil->kecamatan) }}" required maxlength="120" />
            </div>

            <div class="form-group profil-rt-form-row-2">
              <div>
                <label class="form-label" for="kota"><i class="ph ph-city" style="margin-right:4px;color:var(--blue-primary);"></i> Kota *</label>
                <input type="text" id="kota" name="kota" class="form-control-plain" value="{{ old('kota', $profil->kota) }}" required maxlength="120" />
              </div>
              <div>
                <label class="form-label" for="provinsi"><i class="ph ph-globe-hemisphere-west" style="margin-right:4px;color:var(--blue-primary);"></i> Provinsi *</label>
                <input type="text" id="provinsi" name="provinsi" class="form-control-plain" value="{{ old('provinsi', $profil->provinsi) }}" required maxlength="120" />
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" for="kode_pos"><i class="ph ph-mailbox" style="margin-right:4px;color:var(--blue-primary);"></i> Kode Pos *</label>
              <input type="text" id="kode_pos" name="kode_pos" class="form-control-plain" value="{{ old('kode_pos', $profil->kode_pos) }}" required maxlength="5" inputmode="numeric" pattern="\d{5}" style="max-width:160px;" />
            </div>
          </div>

          <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:8px;padding-top:20px;border-top:1px solid var(--gray-100);">
            <a href="{{ route('dashboard') }}" class="btn btn-outline">Batal</a>
            <button type="submit" class="btn btn-blue"><i class="ph ph-floppy-disk"></i> Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
</div>

<script src="{{ asset('js/parete.js') }}"></script>
<script>
  initLayout('profil-rt', 'Profil RT');
</script>
</body>
</html>
