<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Intersection;

use PhpRayTracer\RayTracer\Shape\Shape;

final class IntersectionFactory
{
    public static function create(float $t, Shape $shape): Intersection
    {
        return new Intersection($t, $shape);
    }
}
