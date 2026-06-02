<?php

namespace App\Support;

class PhoneNumber
{
    /** Simpan & kirim ke API: +6281234567890 */
    public static function normalize(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }

        $trimmed = trim($input);
        if ($trimmed === '' || $trimmed === '-') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $trimmed) ?? '';
        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '62')) {
            $digits = substr($digits, 2);
        } elseif (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        if ($digits === '') {
            return null;
        }

        return '+62'.$digits;
    }

    /** Tampilan web / API: +62 812 9149 7170 */
    public static function formatDisplay(?string $stored): string
    {
        $canonical = self::normalize($stored);
        if ($canonical === null) {
            return '-';
        }

        $local = substr($canonical, 3);
        $groups = str_split($local, 4);
        if ($groups === false || $groups === []) {
            return $canonical;
        }

        return '+62 '.implode(' ', $groups);
    }

    /** Digit setelah 62 untuk input (tanpa +62). */
    public static function localPart(?string $stored): string
    {
        $canonical = self::normalize($stored);
        if ($canonical === null) {
            return '';
        }

        return substr($canonical, 3);
    }
}
