<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataWarga;
use App\Support\PhoneNumber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthApiController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_keluarga' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'max:100'],
        ]);

        $idKeluarga = strtoupper(trim($validated['id_keluarga']));

        $warga = DataWarga::query()
            ->where('id_keluarga', $idKeluarga)
            ->first();

        if (! $warga) {
            return response()->json([
                'message' => 'ID Keluarga belum terdaftar. Hubungi Admin RT untuk mendapatkan ID.',
            ], 422);
        }

        if (! $warga->password) {
            return response()->json([
                'message' => 'Akun belum diaktivasi oleh Admin RT.',
            ], 422);
        }

        if (! Hash::check($validated['password'], $warga->password)) {
            return response()->json(['message' => 'ID Keluarga atau kata sandi salah.'], 422);
        }

        if ($warga->status_akun !== 'Aktif') {
            return response()->json(['message' => 'Akun Anda nonaktif. Hubungi Admin RT.'], 403);
        }

        $plainToken = Str::random(64);
        $warga->api_token = hash('sha256', $plainToken);
        $warga->save();

        return response()->json([
            'token' => $plainToken,
            'warga' => $this->formatWarga($warga),
            'harus_ganti_password' => (bool) ($warga->harus_ganti_password ?? false),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var DataWarga $warga */
        $warga = $request->attributes->get('warga');
        $warga->api_token = null;
        $warga->save();

        return response()->json(['message' => 'Berhasil keluar.']);
    }

    public function changePassword(Request $request): JsonResponse
    {
        /** @var DataWarga $warga */
        $warga = $request->attributes->get('warga');

        $validated = $request->validate([
            'password_baru' => ['required', 'string', 'min:8', 'max:100'],
            'password_baru_confirmation' => ['required', 'same:password_baru'],
            'nomor_hp' => ['nullable', 'string', 'max:30'],
        ]);

        $warga->password = Hash::make($validated['password_baru']);
        $warga->harus_ganti_password = false;

        if (! empty($validated['nomor_hp'])) {
            $warga->nomor_hp = PhoneNumber::normalize($validated['nomor_hp']);
        }

        $warga->save();

        return response()->json([
            'message' => 'Kata sandi berhasil diperbarui.',
            'warga' => $this->formatWarga($warga),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        /** @var DataWarga $warga */
        $warga = $request->attributes->get('warga');

        return response()->json(['warga' => $this->formatWarga($warga)]);
    }

    /** @return array<string, mixed> */
    private function formatWarga(DataWarga $warga): array
    {
        return [
            'id' => (string) $warga->getKey(),
            'id_keluarga' => $warga->id_keluarga,
            'nama_kepala_keluarga' => $warga->nama_kepala_keluarga,
            'nomor_rumah' => $warga->nomor_rumah,
            'alamat_lengkap' => $warga->alamat_lengkap,
            'nomor_hp' => PhoneNumber::normalize($warga->nomor_hp) ?? '',
            'status_akun' => $warga->status_akun,
            'harus_ganti_password' => (bool) ($warga->harus_ganti_password ?? false),
        ];
    }
}
