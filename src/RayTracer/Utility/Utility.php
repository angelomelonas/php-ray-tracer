<?php

namespace PhpRayTracer\RayTracer\Utility;

final class Utility
{
    public static function areFloatsEqual(float $a, float $b): bool
    {
        return bccomp(strval($a), strval($b)) === 0;
    }
}