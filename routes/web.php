<?php

use App\Http\Controllers\AdminRegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\InformasiController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\PermohonanDokumenController;
use App\Http\Controllers\ProfilRtController;
use App\Http\Controllers\WargaController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/profil-rt', [ProfilRtController::class, 'edit'])->name('profil-rt.edit');
    Route::put('/profil-rt', [ProfilRtController::class, 'update'])->name('profil-rt.update');

    Route::resource('warga', WargaController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::post('pengaduan/{pengaduan}/dibuka', [PengaduanController::class, 'markDibuka'])->name('pengaduan.dibuka');
    Route::resource('pengaduan', PengaduanController::class)->only(['index', 'update']);

    Route::resource('informasi', InformasiController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('/dokumen/{dokumen}/unduh', [DokumenController::class, 'download'])->name('dokumen.download');
    Route::post('/permohonan-dokumen/{permohonan}/respond', [PermohonanDokumenController::class, 'respond'])->name('permohonan-dokumen.respond');
    Route::get('/permohonan-dokumen/{permohonan}/file/{jenis}', [PermohonanDokumenController::class, 'file'])->name('permohonan-dokumen.file');
    Route::resource('dokumen', DokumenController::class)
        ->parameters(['dokumen' => 'dokumen'])
        ->only(['index', 'store', 'update', 'destroy']);

    Route::redirect('/admin', '/admin/register');
    Route::get('/admin/register', [AdminRegisterController::class, 'index'])->name('admin.register');
    Route::post('/admin/register', [AdminRegisterController::class, 'store'])->name('admin.register.store');
    Route::post('/admin/{admin}/reveal', [AdminRegisterController::class, 'reveal'])->name('admin.reveal');
    Route::delete('/admin/{admin}', [AdminRegisterController::class, 'destroy'])->name('admin.destroy');
});
