<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Admin</title>
  <link rel="stylesheet" href="{{ asset('css/parete.css') }}" />
  <!-- Phosphor Icons (lightweight) -->
  <script src="https://unpkg.com/@phosphor-icons/web@2.1.1/src/index.js" defer></script>
</head>
<body>

<div class="login-page">
  <!-- LEFT — brand panel -->
  <div class="login-left">
    <div class="login-brand">
      <div class="login-logo">
        <img src="{{ asset('images/logo-white.png') }}" alt="Logo" style="height: 60px; margin-right: 12px;" />
      </div>
      <p class="login-tagline">Sistem Pelayanan & Informasi RT Digital</p>
    </div>

    <div class="login-illustration">
      <div class="login-cards">
        <div class="login-card-preview">
          <div class="login-card-icon blue">
            <i class="ph ph-users-three" style="color:white;font-size:20px"></i>
          </div>
          <div class="login-card-text">
            <strong>Kelola Data Warga</strong>
            <span>Manajemen data KK & pengguna RT</span>
          </div>
        </div>
        <div class="login-card-preview">
          <div class="login-card-icon green">
            <i class="ph ph-clipboard-text" style="color:#A5D6A7;font-size:20px"></i>
          </div>
          <div class="login-card-text">
            <strong>Pantau Pengaduan</strong>
            <span>Tindak lanjut laporan warga real-time</span>
          </div>
        </div>
        <div class="login-card-preview">
          <div class="login-card-icon yellow">
            <i class="ph ph-megaphone-simple" style="color:#FFE082;font-size:20px"></i>
          </div>
          <div class="login-card-text">
            <strong>Publikasi Informasi</strong>
            <span>Pengumuman & kegiatan RT</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- RIGHT — form panel -->
  <div class="login-right">
    <div class="login-form-container">
      <h2>Selamat Datang 👋</h2>
      <p>Masuk ke panel admin Parete untuk mengelola layanan RT Anda.</p>

      <!-- Alert: error (hidden by default) -->
      <div class="alert-banner warning" id="loginError" style="display:none">
        <i class="ph ph-warning-circle"></i>
        <span>Username atau kata sandi salah. Silakan coba lagi.</span>
      </div>

      <form id="loginForm" novalidate>
        <div class="form-group">
          <label class="form-label" for="username">Username</label>
          <div class="form-input-wrap">
            <i class="ph ph-user icon"></i>
            <input
              type="text"
              id="username"
              name="username"
              class="form-control"
              placeholder="Masukkan username"
              autocomplete="username"
              required
            />
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Kata Sandi</label>
          <div class="form-input-wrap">
            <i class="ph ph-lock icon"></i>
            <input
              type="password"
              id="password"
              name="password"
              class="form-control"
              placeholder="Masukkan kata sandi"
              autocomplete="current-password"
              required
            />
            <button type="button" class="toggle-pw" onclick="togglePw()" style="
              position:absolute; right:14px; top:50%; transform:translateY(-50%);
              background:none; border:none; color:var(--gray-400); cursor:pointer; font-size:18px;
            ">
              <i class="ph ph-eye" id="eyeIcon"></i>
            </button>
          </div>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
          <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--gray-600);cursor:pointer;">
            <input type="checkbox" name="remember" style="accent-color:var(--blue-primary);width:15px;height:15px;" />
            Ingat saya
          </label>
          <a href="#" style="font-size:13px;color:var(--blue-primary);font-weight:500;">Lupa kata sandi?</a>
        </div>

        <button type="submit" class="btn-primary" id="loginBtn">
          <i class="ph ph-sign-in"></i>
          Masuk
        </button>
      </form>

      <div class="login-footer">
        <p>Parete v1.0.0 &nbsp;·&nbsp; RT 05 Malabar Ujung</p>
        <p style="margin-top:6px;">© 2026 Parete. All rights reserved.</p>
      </div>
    </div>
  </div>
</div>

<script src="{{ asset('js/parete.js') }}"></script>
<script>
  function togglePw() {
    const pw = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    if (pw.type === 'password') {
      pw.type = 'text';
      icon.className = 'ph ph-eye-slash';
    } else {
      pw.type = 'password';
      icon.className = 'ph ph-eye';
    }
  }

  document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('loginBtn');
    btn.innerHTML = '<i class="ph ph-spinner"></i> Memverifikasi...';
    btn.disabled = true;
    setTimeout(() => {
      window.location.href = @json(route('dashboard'));
    }, 1000);
  });
</script>

</body>
</html>