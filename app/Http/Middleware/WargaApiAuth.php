<?php

namespace App\Http\Middleware;

use App\Models\DataWarga;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WargaApiAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('parete.auth_disabled')) {
            $warga = DataWarga::query()->where('status_akun', 'Aktif')->first();
            if ($warga) {
                $request->attributes->set('warga', $warga);

                return $next($request);
            }
        }

        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Token autentikasi diperlukan.'], 401);
        }

        $warga = DataWarga::query()
            ->where('api_token', hash('sha256', $token))
            ->where('status_akun', 'Aktif')
            ->first();

        if (! $warga) {
            return response()->json(['message' => 'Sesi tidak valid atau akun nonaktif.'], 401);
        }

        $request->attributes->set('warga', $warga);

        return $next($request);
    }
}
