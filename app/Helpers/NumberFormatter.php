<?php

namespace App\Helpers;

final class NumberFormatter
{
    public static function cantidad($value, int $maxDecimals = 3): string
    {
        if ($value === null || $value === '') {
            return '0';
        }

        $formatted = number_format((float) $value, $maxDecimals, ',', '.');
        $trimmed = rtrim(rtrim($formatted, '0'), ',');

        return $trimmed === '-0' ? '0' : $trimmed;
    }

    public static function cantidadInput($value, int $maxDecimals = 3): string
    {
        if ($value === null || $value === '') {
            return '0';
        }

        $formatted = number_format((float) $value, $maxDecimals, '.', '');
        $trimmed = rtrim(rtrim($formatted, '0'), '.');

        return $trimmed === '-0' ? '0' : $trimmed;
    }
}
