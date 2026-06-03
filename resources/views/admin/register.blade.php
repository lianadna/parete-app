<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <link rel="stylesheet" href="{{ asset('css/parete.css') }}" />
  <script src="https://unpkg.com/@phosphor-icons/web@2.1.1/src/index.js" defer></script>
</head>
<body data-open-add-admin-modal="{{ ($errors->any() && (old('username') || old('nama'))) ? '1' : '0' }}">

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
      <div class="breadcrumb"><i class="ph ph-house-simple"></i><span>/</span><span>Kelola Admin</span></div>
      <h1>Kelola Admin</h1>
      <p>Daftarkan akun admin baru untuk mengakses panel Parete</p>
    </div>
    <button type="button" class="btn btn-blue" onclick="openModal('addAdminModal')">
      <i class="ph ph-user-plus"></i> Daftar Admin
    </button>
  </div>

  <div class="card">
    <div class="card-header">
      <span class="card-title">Daftar Admin Terdaftar</span>
    </div>
    <div class="card-body">
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Nama</th>
              <th>Username</th>
              <th>Password</th>
              <th style="width:80px;text-align:center;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($admins as $admin)
              <tr data-admin-id="{{ $admin->getKey() }}" data-admin-nama="{{ $admin->nama }}">
                <td>
                  <span style="font-weight:500;">{{ $admin->nama }}</span>
                  @if((string) $admin->getKey() === $authAdminId)
                    <span class="badge badge-blue" style="margin-left:8px;font-size:10px;">Anda</span>
                  @endif
                </td>
                <td>
                  <div class="admin-secret-cell">
                    <span class="admin-secret-value masked" data-field="username">****</span>
                    <button type="button" class="btn btn-outline btn-sm btn-icon admin-reveal-btn" data-field="username" title="Tampilkan username">
                      <i class="ph ph-eye"></i>
                    </button>
                  </div>
                </td>
                <td>
                  <div class="admin-secret-cell">
                    <span class="admin-secret-value masked" data-field="password">****</span>
                    <button type="button" class="btn btn-outline btn-sm btn-icon admin-reveal-btn" data-field="password" title="Tampilkan password">
                      <i class="ph ph-eye"></i>
                    </button>
                  </div>
                </td>
                <td style="text-align:center;">
                  @if((string) $admin->getKey() === $authAdminId)
                    <button type="button" class="btn btn-outline btn-sm btn-icon" disabled title="Tidak dapat menghapus akun sendiri">
                      <i class="ph ph-trash"></i>
                    </button>
                  @else
                    <form action="{{ route('admin.destroy', $admin->getKey()) }}" method="post" style="display:inline;" onsubmit="return confirm('Hapus admin {{ $admin->nama }}?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger-outline btn-sm btn-icon" title="Hapus">
                        <i class="ph ph-trash"></i>
                      </button>
                    </form>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4">
                  <div class="empty-state">
                    <div class="empty-icon">🛡️</div>
                    <div class="empty-title">Belum ada admin terdaftar</div>
                    <p style="font-size:13px;color:var(--gray-400);margin-top:8px;">Klik «Daftar Admin» untuk menambahkan akun pertama.</p>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($admins->isNotEmpty())
        <div style="margin-top:12px;font-size:13px;color:var(--gray-400);">Total {{ $admins->count() }} admin</div>
      @endif
    </div>
  </div>
</div>

<div class="modal-overlay" id="revealSecretModal" style="display:none;">
  <div class="modal" style="max-width:400px;">
    <div class="modal-header">
      <h3 id="revealSecretTitle">Verifikasi Kata Sandi</h3>
      <button type="button" class="modal-close" onclick="closeRevealModal()">×</button>
    </div>
    <div class="modal-body">
      <p style="font-size:13px;color:var(--gray-600);line-height:1.6;margin-bottom:16px;" id="revealSecretHint">
        Masukkan kata sandi admin ini untuk melihat data rahasia.
      </p>
      <div class="alert-banner warning" id="revealSecretError" style="display:none;margin-bottom:12px;">
        <i class="ph ph-warning-circle"></i>
        <span id="revealSecretErrorText"></span>
      </div>
      <div class="form-group" style="margin-bottom:0;">
        <label class="form-label" for="revealVerificationPassword">Kata Sandi Admin *</label>
        <input type="password" id="revealVerificationPassword" class="form-control-plain" autocomplete="off" placeholder="Kata sandi admin yang ingin dilihat" style="border-radius:var(--radius-sm);" />
      </div>
      <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px;padding-top:16px;border-top:1px solid var(--gray-100);">
        <button type="button" class="btn btn-outline" onclick="closeRevealModal()">Batal</button>
        <button type="button" class="btn btn-blue" id="revealSecretSubmit"><i class="ph ph-eye"></i> Tampilkan</button>
      </div>
    </div>
  </div>
</div>

<div class="modal-overlay" id="addAdminModal" style="display:none;">
  <div class="modal" style="max-width:440px;">
    <div class="modal-header">
      <h3>Daftar Admin Baru</h3>
      <button type="button" class="modal-close" onclick="closeModal('addAdminModal')">×</button>
    </div>
    <form method="post" action="{{ route('admin.register.store') }}" class="modal-body">
      @csrf
      <div class="form-group">
        <label class="form-label" for="reg_nama">Nama *</label>
        <input type="text" id="reg_nama" name="nama" class="form-control-plain" required maxlength="120" autocomplete="name" value="{{ old('nama') }}" placeholder="contoh: Pak Budi Santoso" style="border-radius:var(--radius-sm);" />
        <div style="font-size:12px;color:var(--gray-400);margin-top:6px;">Ditampilkan di profil sidebar saat admin login.</div>
      </div>
      <div class="form-group">
        <label class="form-label" for="reg_username">Username *</label>
        <input type="text" id="reg_username" name="username" class="form-control-plain" required maxlength="50" pattern="[a-zA-Z0-9._-]+" autocomplete="off" value="{{ old('username') }}" placeholder="contoh: admin_rt" style="border-radius:var(--radius-sm);" />
        <div style="font-size:12px;color:var(--gray-400);margin-top:6px;">Digunakan saat login ke panel admin.</div>
      </div>
      <div class="form-group">
        <label class="form-label" for="reg_password">Kata Sandi *</label>
        <input type="text" id="reg_password" name="password" class="form-control-plain" required minlength="4" maxlength="100" autocomplete="new-password" placeholder="Minimal 4 karakter" style="border-radius:var(--radius-sm);" />
      </div>
      <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px;padding-top:16px;border-top:1px solid var(--gray-100);">
        <button type="button" class="btn btn-outline" onclick="closeModal('addAdminModal')">Batal</button>
        <button type="submit" class="btn btn-blue"><i class="ph ph-user-plus"></i> Daftar</button>
      </div>
    </form>
  </div>
</div>

<script src="{{ asset('js/parete.js') }}"></script>
<style>
  .admin-secret-cell {
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }
  .admin-secret-value {
    font-family: monospace;
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-800);
    min-width: 36px;
  }
  .admin-secret-value.masked {
    color: var(--gray-400);
    letter-spacing: 2px;
  }
  .admin-reveal-btn {
    width: 28px;
    height: 28px;
    padding: 0;
  }
</style>
<script>
  initLayout('admin', 'Kelola Admin');

  const revealState = { adminId: null, adminNama: '', field: null, row: null, valueEl: null, btn: null };

  function closeRevealModal() {
    closeModal('revealSecretModal');
    document.getElementById('revealVerificationPassword').value = '';
    document.getElementById('revealSecretError').style.display = 'none';
  }

  function maskSecret(valueEl, btn) {
    valueEl.textContent = '****';
    valueEl.classList.add('masked');
    valueEl.dataset.revealed = '0';
    btn.dataset.revealed = '0';
    btn.title = valueEl.dataset.field === 'password' ? 'Tampilkan password' : 'Tampilkan username';
    btn.innerHTML = '<i class="ph ph-eye"></i>';
  }

  function showRevealModal(adminId, adminNama, field, row, valueEl, btn) {
    revealState.adminId = adminId;
    revealState.adminNama = adminNama;
    revealState.field = field;
    revealState.row = row;
    revealState.valueEl = valueEl;
    revealState.btn = btn;

    const label = field === 'password' ? 'password' : 'username';
    document.getElementById('revealSecretTitle').textContent = 'Akses Data Rahasia';
    document.getElementById('revealSecretHint').textContent =
      'Masukkan kata sandi admin «' + adminNama + '» untuk melihat ' + label + '.';
    document.getElementById('revealVerificationPassword').value = '';
    document.getElementById('revealSecretError').style.display = 'none';
    openModal('revealSecretModal');
    setTimeout(() => document.getElementById('revealVerificationPassword').focus(), 100);
  }

  document.querySelectorAll('.admin-reveal-btn').forEach((btn) => {
    btn.addEventListener('click', () => {
      const row = btn.closest('tr');
      const field = btn.dataset.field;
      const valueEl = row.querySelector('.admin-secret-value[data-field="' + field + '"]');
      valueEl.dataset.field = field;

      if (btn.dataset.revealed === '1') {
        maskSecret(valueEl, btn);
        return;
      }

      showRevealModal(row.dataset.adminId, row.dataset.adminNama, field, row, valueEl, btn);
    });
  });

  document.getElementById('revealSecretSubmit').addEventListener('click', async () => {
    const password = document.getElementById('revealVerificationPassword').value;
    const errorBox = document.getElementById('revealSecretError');
    const errorText = document.getElementById('revealSecretErrorText');
    const submitBtn = document.getElementById('revealSecretSubmit');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

    if (!password) {
      errorText.textContent = 'Masukkan kata sandi admin terlebih dahulu.';
      errorBox.style.display = 'flex';
      return;
    }

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="ph ph-spinner"></i> Memverifikasi...';
    errorBox.style.display = 'none';

    try {
      const res = await fetch('/admin/' + encodeURIComponent(revealState.adminId) + '/reveal', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf,
        },
        body: JSON.stringify({
          field: revealState.field,
          verification_password: password,
        }),
      });

      const data = await res.json().catch(() => ({}));

      if (!res.ok) {
        errorText.textContent = data.message || 'Gagal memverifikasi kata sandi.';
        errorBox.style.display = 'flex';
        return;
      }

      revealState.valueEl.textContent = data.value;
      revealState.valueEl.classList.remove('masked');
      revealState.valueEl.dataset.revealed = '1';
      revealState.btn.dataset.revealed = '1';
      revealState.btn.title = 'Sembunyikan';
      revealState.btn.innerHTML = '<i class="ph ph-eye-slash"></i>';
      closeRevealModal();
    } catch (e) {
      errorText.textContent = 'Terjadi kesalahan. Coba lagi.';
      errorBox.style.display = 'flex';
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<i class="ph ph-eye"></i> Tampilkan';
    }
  });

  document.getElementById('revealVerificationPassword').addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      document.getElementById('revealSecretSubmit').click();
    }
  });

  if (document.body.dataset.openAddAdminModal === '1') {
    openModal('addAdminModal');
  }
</script>
</body>
</html>
