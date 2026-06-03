/**
 * parete.js — shared JS for Parete Admin Web
 * Renders sidebar + topbar, handles navigation active state,
 * and provides shared utilities.
 */

/* ── Sidebar Navigation Config (paths = routes/web.php) ─────────────────── */
const NAV_ITEMS = [
  { section: 'Menu Utama' },
  { id: 'dashboard',  label: 'Dashboard',       icon: 'ph-squares-four',       href: '/dashboard' },
  { id: 'admin',      label: 'Kelola Admin',    icon: 'ph-shield-check',       href: '/admin/register' },
  { id: 'warga',      label: 'Data Warga',      icon: 'ph-users-three',        href: '/warga' },
  { id: 'pengaduan',  label: 'Pengaduan',       icon: 'ph-clipboard-text',     href: '/pengaduan' },
  { id: 'informasi',  label: 'Informasi',       icon: 'ph-megaphone-simple',   href: '/informasi' },
  { id: 'profil-rt',  label: 'Profil RT',       icon: 'ph-buildings',          href: '/profil-rt' },
  { id: 'dokumen',    label: 'Dokumen',         icon: 'ph-files',              href: '/dokumen' },
];

function getAuthContext() {
  const root = document.getElementById('app');
  const nama = root?.dataset?.adminNama || 'Admin';
  const username = root?.dataset?.adminUsername || 'admin';
  const initials = nama.trim().slice(0, 2).toUpperCase() || 'AD';

  return { nama, username, initials };
}

/* ── Render Sidebar ─────────────────────────────── */
function renderSidebar(activeId, auth, csrfToken) {
  const path = window.location.pathname.replace(/\/+$/, '') || '/';
  const currentPage = activeId || 'dashboard';

  let navHTML = '';
  for (const item of NAV_ITEMS) {
    if (item.section) {
      navHTML += `<div class="nav-section-label">${item.section}</div>`;
      continue;
    }
    const isActive = item.id === currentPage || (item.href && path.startsWith(item.href));
    const badgeHTML = item.badge
      ? `<span class="nav-badge">${item.badge}</span>`
      : '';
    navHTML += `
      <a class="nav-item${isActive ? ' active' : ''}" href="${item.href}">
        <div class="nav-icon"><i class="ph ${item.icon}"></i></div>
        <span>${item.label}</span>
        ${badgeHTML}
      </a>`;
  }

  return `
    <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <img src="/images/logo-blue.png" class="sidebar-logo" alt="Logo">
    </div>
    <nav class="sidebar-nav">${navHTML}</nav>
      <div class="sidebar-footer">
        <form method="post" action="/logout" style="margin:0;">
          <input type="hidden" name="_token" value="${csrfToken}">
          <div class="sidebar-user">
            <div class="user-avatar">${auth.initials}</div>
            <div class="user-info">
              <strong>${auth.nama}</strong>
              <span>${auth.username}</span>
            </div>
            <button type="submit" title="Keluar" style="background:none;border:none;color:var(--gray-400);font-size:15px;margin-left:auto;cursor:pointer;padding:0;">
              <i class="ph ph-sign-out"></i>
            </button>
          </div>
        </form>
      </div>
    </aside>`;
}

/* ── Render Topbar ──────────────────────────────── */
function renderTopbar(auth) {
  return `
    <header class="topbar">

      <button class="topbar-btn mobile-only" onclick="toggleSidebar()" id="menuBtn">
        <i class="ph ph-list"></i>
      </button>

      <div class="topbar-actions">
        <button class="topbar-btn" title="Notifikasi">
          <i class="ph ph-bell"></i>
          <span class="notif-dot"></span>
        </button>
        <button class="topbar-btn" title="Tema">
          <i class="ph ph-moon"></i>
        </button>
        <div class="topbar-avatar" title="${auth.nama}">${auth.initials}</div>
      </div>

    </header>`;
}

/* ── Inject Layout ──────────────────────────────── */
function initLayout(pageId, pageTitle) {
  const root = document.getElementById('app');
  if (!root) return;

  const auth = getAuthContext();
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
    || document.querySelector('#app input[name="_token"]')?.value
    || document.querySelector('input[name="_token"]')?.value
    || '';
  const pageContent = root.innerHTML;
  root.innerHTML = `
    <div class="admin-layout">
      ${renderSidebar(pageId, auth, csrfToken)}
      <div class="main-content">
        ${renderTopbar(auth)}
        <div class="page-body" id="pageBody">
          ${pageContent}
        </div>
      </div>
    </div>`;

  function checkWidth() {
    const btn = document.getElementById('menuBtn');
    if (btn) btn.style.display = window.innerWidth <= 900 ? 'flex' : 'none';
  }
  checkWidth();
  window.addEventListener('resize', checkWidth);
}

function toggleSidebar() {
  document.getElementById('sidebar')?.classList.toggle('open');
}

/**
 * Client-side filter: hide items, show empty element when none match.
 * @returns {{ total: number, visible: number }}
 */
function applyClientFilter(itemSelector, emptyElId, matchFn) {
  const items = document.querySelectorAll(itemSelector);
  const emptyEl = emptyElId ? document.getElementById(emptyElId) : null;
  let visible = 0;

  items.forEach(el => {
    const show = matchFn(el);
    el.style.display = show ? '' : 'none';
    if (show) visible++;
  });

  if (emptyEl) {
    emptyEl.style.display = items.length > 0 && visible === 0 ? '' : 'none';
  }

  return { total: items.length, visible };
}

/* ── Format Helpers ─────────────────────────────── */
function formatDate(dateStr) {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
}

function statusBadge(status) {
  const map = {
    'Terkirim':  ['badge-gray',   'Terkirim'],
    'Diterima':  ['badge-yellow', 'Diterima'],
    'Diproses':  ['badge-blue',   'Diproses'],
    'Selesai':   ['badge-green',  'Selesai'],
    'Ditolak':   ['badge-red',    'Ditolak'],
    'Aktif':     ['badge-green',  'Aktif'],
    'Nonaktif':  ['badge-gray',   'Nonaktif'],
  };
  const [cls, label] = map[status] || ['badge-gray', status];
  return `<span class="badge ${cls}">${label}</span>`;
}

function docTypeBadge(ext) {
  const map = { pdf: 'pdf', doc: 'doc', docx: 'doc', xls: 'xls', xlsx: 'xls' };
  const t = map[(ext||'').toLowerCase()] || 'doc';
  const labels = { pdf:'PDF', doc:'DOC', xls:'XLS' };
  return `<span class="doc-type ${t}">${labels[t]||t.toUpperCase()}</span>`;
}

/* ── Modal helpers ──────────────────────────────── */
function openModal(modalId) {
  const el = document.getElementById(modalId);
  if (el) el.style.display = 'flex';
}

function closeModal(modalId) {
  const el = document.getElementById(modalId);
  if (el) el.style.display = 'none';
}

document.addEventListener('click', function(e) {
  if (e.target.classList.contains('modal-overlay')) {
    e.target.style.display = 'none';
  }
});

/* ── Toast notification ─────────────────────────── */
function showToast(message, type = 'success') {
  const colors = {
    success: 'var(--green-success)',
    error:   'var(--red-danger)',
    info:    'var(--blue-primary)',
    warning: 'var(--yellow-accent)',
  };
  const icons = { success:'ph-check-circle', error:'ph-x-circle', info:'ph-info', warning:'ph-warning' };

  const toast = document.createElement('div');
  toast.style.cssText = `
    position:fixed; bottom:24px; right:24px; z-index:9999;
    background:white; border-radius:12px; padding:14px 18px;
    display:flex; align-items:center; gap:12px;
    box-shadow:0 8px 24px rgba(0,0,0,0.12); font-size:14px;
    border-left:4px solid ${colors[type]};
    animation:slideUp 0.3s ease;
    font-family:var(--font-body);
    color:var(--gray-800);
    min-width:260px;
    max-width:360px;
  `;
  toast.innerHTML = `
    <i class="ph ${icons[type]}" style="font-size:20px;color:${colors[type]};flex-shrink:0;"></i>
    <span>${message}</span>
  `;
  document.body.appendChild(toast);
  setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity 0.3s'; }, 2700);
  setTimeout(() => toast.remove(), 3000);
}
