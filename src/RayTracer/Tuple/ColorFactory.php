<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Tuple;

final class ColorFactory
{
    public static function create(float $red, float $green, float $blue): Color
    {
        return new Color($red, $green, $blue);
    }

    public static function createBlack(): Color
    {
        return new Color(0, 0, 0);
    }

    public static function createWhite(): Color
    {
        return new Color(1, 1, 1);
    }
}
