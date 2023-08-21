<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Ray;

use PhpRayTracer\RayTracer\Tuple\Tuple;

final class RayFactory
{
    public static function create(Tuple $origin, Tuple $direction): Ray
    {
        return new Ray($origin, $direction);
    }
}
