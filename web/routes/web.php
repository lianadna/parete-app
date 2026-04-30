<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/pengaduan', function () {
    return view('pengaduan');
});

Route::get('/warga', function () {
    return view('warga');
});

Route::get('/informasi', function () {
    return view('informasi');
});

Route::get('/dokumen', function () {
    return view('dokumen');
});