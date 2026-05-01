<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/login', fn() => view('login'))->name('login');
Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
Route::get('/pengaduan', fn() => view('pengaduan'))->name('pengaduan');
Route::get('/warga', fn() => view('warga'))->name('warga');
Route::get('/informasi', fn() => view('informasi'))->name('informasi');
Route::get('/dokumen', fn() => view('dokumen'))->name('dokumen');
Route::get('/admin', fn() => view('admin'))->name('admin');