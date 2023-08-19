<?php

namespace PhpRayTracer\RayTracer\Tuple;

final class ColorFactory
{
    public static function create(float $red, float $green, float $blue): Color
    {
        return new Color($red, $green, $blue);
    }
}