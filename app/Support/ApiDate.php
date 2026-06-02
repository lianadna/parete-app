<?php

namespace App\Support;

use Carbon\Carbon;

class ApiDate
{
    /** ISO 8601 dengan zona waktu aplikasi (mis. Asia/Jakarta). */
    public static function format(mixed $date): ?string
    {
        if ($date === null) {
            return null;
        }

        $carbon = $date instanceof Carbon ? $date->copy() : Carbon::parse($date);

        return $carbon->timezone(config('app.timezone'))->format('Y-m-d\TH:i:sP');
    }
}
