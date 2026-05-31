<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\DokumenApiController;
use App\Http\Controllers\Api\InformasiApiController;
use App\Http\Controllers\Api\PengaduanApiController;
use App\Http\Controllers\Api\PermohonanDokumenApiController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Middleware\WargaApiAuth;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthApiController::class, 'login']);

Route::middleware(WargaApiAuth::class)->group(function () {
    Route::post('/auth/logout', [AuthApiController::class, 'logout']);
    Route::post('/auth/change-password', [AuthApiController::class, 'changePassword']);
    Route::get('/auth/me', [AuthApiController::class, 'me']);

    Route::get('/dashboard', [DashboardApiController::class, 'index']);
    Route::get('/profile', [ProfileApiController::class, 'show']);

    Route::get('/informasi', [InformasiApiController::class, 'index']);
    Route::get('/informasi/{informasi}', [InformasiApiController::class, 'show']);

    Route::get('/dokumen', [DokumenApiController::class, 'index']);

    Route::get('/pengaduan', [PengaduanApiController::class, 'index']);
    Route::post('/pengaduan', [PengaduanApiController::class, 'store']);
    Route::get('/pengaduan/{pengaduan}', [PengaduanApiController::class, 'show']);

    Route::get('/permohonan-dokumen', [PermohonanDokumenApiController::class, 'index']);
    Route::post('/permohonan-dokumen', [PermohonanDokumenApiController::class, 'store']);
});

Route::get('/dokumen/{dokumen}/unduh', [DokumenApiController::class, 'download']);
