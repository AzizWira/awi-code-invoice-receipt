<?php

namespace App\Support;

class Whatsapp
{
    public static function normalize(?string $number): ?string
    {
        if (! $number) return null;
        $digits = preg_replace('/\D+/', '', $number);
        if ($digits === '') return null;
        if (str_starts_with($digits, '0')) $digits = '62'.substr($digits, 1);
        if (str_starts_with($digits, '8')) $digits = '62'.$digits;
        return $digits;
    }
}
