<?php

namespace App\Support;

class Money
{
    public static function rupiah(mixed $value): string
    {
        return 'Rp'.number_format((float) $value, 0, ',', '.');
    }

    public static function number(mixed $value): string
    {
        return number_format((float) $value, 0, ',', '.');
    }
}
