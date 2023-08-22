<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Intersection;

use PhpRayTracer\RayTracer\Object\Sphere;

final class IntersectionFactory
{
    public static function create(float $t, Sphere $sphere): Intersection
    {
        return new Intersection($t, $sphere);
    }
}
