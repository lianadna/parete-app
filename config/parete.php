<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nonaktifkan autentikasi API warga (development)
    |--------------------------------------------------------------------------
    |
    | Jika true, endpoint API yang dilindungi middleware WargaApiAuth akan
    | otomatis menggunakan warga aktif pertama tanpa token Bearer.
    |
    */
    'auth_disabled' => env('PARETE_AUTH_DISABLED', false),

];
