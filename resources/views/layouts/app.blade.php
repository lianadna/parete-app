<!-- MASIH BELUM BISA DIPAKE, JELEK -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Parete Admin</title>

    <link rel="stylesheet" href="{{ asset('css/parete.css') }}">
    <script src="https://unpkg.com/@phosphor-icons/web@2.1.1/src/index.js" defer></script>
</head>
<body>

@php
    $role = request()->query('role', 'super_admin');
    $roleName = ($role == 'super_admin') ? 'Super Admin' : 'Admin RT';
@endphp

<div class="admin-layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <!-- HEADER (LOGO ONLY) -->
        <div class="sidebar-header">
            <img src="{{ asset('images/logo-blue.png') }}"
                 alt="Parete Logo"
                 style="height:26px; object-fit:contain;">
        </div>

        <!-- NAV -->
        <nav class="sidebar-nav">

            <a href="{{ route('dashboard', ['role' => $role]) }}"
               class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="ph ph-squares-four"></i>
                Dashboard
            </a>

            @if($role == 'super_admin')
            <a href="{{ route('admin', ['role' => $role]) }}"
               class="nav-item {{ request()->routeIs('admin') ? 'active' : '' }}">
                <i class="ph ph-user-gear"></i>
                Kelola Admin
            </a>
            @endif

            <a href="{{ route('warga.index', ['role' => $role]) }}"
               class="nav-item {{ request()->routeIs('warga.*') ? 'active' : '' }}">
                <i class="ph ph-users"></i>
                Data Warga
            </a>

            <a href="{{ route('pengaduan.index', ['role' => $role]) }}"
               class="nav-item {{ request()->routeIs('pengaduan.*') ? 'active' : '' }}">
                <i class="ph ph-warning-circle"></i>
                Pengaduan
            </a>

            <a href="{{ route('informasi.index', ['role' => $role]) }}"
               class="nav-item {{ request()->routeIs('informasi.*') ? 'active' : '' }}">
                <i class="ph ph-info"></i>
                Informasi RT
            </a>

            <a href="{{ route('dokumen.index', ['role' => $role]) }}"
               class="nav-item {{ request()->routeIs('dokumen.*') ? 'active' : '' }}">
                <i class="ph ph-file-text"></i>
                Dokumen
            </a>

            <!-- LOGOUT -->
            <div style="margin-top:24px;padding:0 10px;">
                <hr style="border:0;border-top:1px solid var(--gray-100);margin-bottom:12px;">
                <a href="{{ route('login') }}" class="nav-item" style="color:#e74c3c;">
                    <i class="ph ph-sign-out"></i>
                    Keluar
                </a>
            </div>

        </nav>
    </aside>

    <!-- MAIN -->
    <main class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">

            <div class="topbar-title">
                @yield('title')
            </div>

            <div class="topbar-actions">

                <div class="topbar-avatar">
                    <i class="ph ph-user"></i>
                </div>

            </div>

        </header>

        <!-- CONTENT -->
        <div class="page-body">
            @yield('content')
        </div>

    </main>
</div>

<script src="{{ asset('js/parete.js') }}"></script>

</body>
</html>