<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\InformasiController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\PermohonanDokumenController;
use App\Http\Controllers\WargaController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/login', fn () => view('login'))->name('login');
Route::get('/dashboard', DashboardController::class)->name('dashboard');

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

Route::get('/admin', fn () => view('admin'))->name('admin');
