<?php

namespace PhpRayTracer\RayTracer\Utility;

final class Utility
{
    public static function areFloatsEqual(float $a, float $b): bool
    {
        return bccomp(number_format($a, PHP_FLOAT_DIG), number_format($b, PHP_FLOAT_DIG)) === 0;
    }
}