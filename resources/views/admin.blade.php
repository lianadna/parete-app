<!-- MASIH BELUM BENER -->

@extends('layouts.app')

@section('title', 'Kelola Admin')

@section('content')
@php
    $admins = [
        ['id' => 1, 'nama' => 'Super Admin Utama', 'email' => 'superadmin@parete.id', 'role' => 'Super Admin', 'status' => 'Aktif'],
        ['id' => 2, 'nama' => 'Admin RT 01', 'email' => 'admin01@parete.id', 'role' => 'Admin RT', 'status' => 'Aktif'],
        ['id' => 3, 'nama' => 'Admin RT 02', 'email' => 'admin02@parete.id', 'role' => 'Admin RT', 'status' => 'Non-Aktif'],
    ];
@endphp

<div class="page-header" style="margin-bottom: 32px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <div>
        <h2 style="font-size: 28px; font-weight: 800; color: var(--primary-dark);">Manajemen Admin</h2>
        <p style="color: var(--text-secondary); font-size: 16px;">Kelola hak akses dan data administrator sistem.</p>
    </div>
    <button class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Tambah Admin
    </button>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nama Admin</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($admins as $adm)
                <tr>
                    <td style="font-weight: 700; color: var(--primary-dark);">{{ $adm['nama'] }}</td>
                    <td style="color: var(--text-secondary);">{{ $adm['email'] }}</td>
                    <td>
                        <span class="badge" style="background: {{ $adm['role'] == 'Super Admin' ? 'var(--primary-dark)' : 'var(--primary-regular)' }}; color: white;">
                            {{ $adm['role'] }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $adm['status'] == 'Aktif' ? 'badge-success' : 'badge-danger' }}">
                            {{ $adm['status'] }}
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <button class="btn btn-outline" style="padding: 8px 12px; border-color: var(--gray-300); color: var(--gray-600);">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-delete" style="padding: 8px 12px;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
