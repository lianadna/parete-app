<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Melayani file publik (path disimpan di MongoDB, mis. informasi_gambar/...).
 * Route ini di luar middleware auth agar Image.network / mobile bisa memuat gambar.
 */
class MediaApiController extends Controller
{
    public function show(Request $request, string $path): BinaryFileResponse
    {
        $normalized = str_replace('\\', '/', $path);
        $normalized = ltrim($normalized, '/');

        if ($normalized === '' || str_contains($normalized, '..')) {
            abort(404);
        }

        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, strlen('storage/'));
        }

        if (! Storage::disk('public')->exists($normalized)) {
            abort(404);
        }

        $absolute = Storage::disk('public')->path($normalized);
        $mime = Storage::disk('public')->mimeType($normalized) ?: 'application/octet-stream';

        return response()->file($absolute, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
