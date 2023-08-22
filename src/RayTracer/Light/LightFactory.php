<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Light;

use PhpRayTracer\RayTracer\Tuple\Color;
use PhpRayTracer\RayTracer\Tuple\Tuple;

final class LightFactory
{
    public static function create(Tuple $position, Color $intensity): Light
    {
        return new Light($position, $intensity);
    }
}
