<?php

namespace App\Support;

class MediaUrl
{
    public static function fromPublicDisk(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        $normalized = str_replace('\\', '/', trim($path));

        if (str_starts_with($normalized, 'http://') || str_starts_with($normalized, 'https://')) {
            return $normalized;
        }

        $normalized = ltrim($normalized, '/');

        $base = self::requestBaseUrl();

        return $base.'/storage/'.$normalized;
    }

    private static function requestBaseUrl(): string
    {
        if (! app()->runningInConsole() && request()->hasHeader('Host')) {
            return request()->getSchemeAndHttpHost();
        }

        return rtrim((string) config('app.url'), '/');
    }
}
