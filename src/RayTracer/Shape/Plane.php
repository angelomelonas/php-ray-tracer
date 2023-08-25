<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Shape;

use PhpRayTracer\RayTracer\Intersection\IntersectionFactory;
use PhpRayTracer\RayTracer\Ray\Ray;
use PhpRayTracer\RayTracer\Tuple\Tuple;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use function abs;
use const PHP_FLOAT_EPSILON;

final class Plane extends Shape
{
    protected function localIntersect(Ray $ray): array
    {
        if (abs($ray->direction->y) < PHP_FLOAT_EPSILON) {
            return [];
        }

        $t = -$ray->origin->y / $ray->direction->y;

        return [IntersectionFactory::create($t, $this)];
    }

    protected function localNormalAt(Tuple $point): Tuple
    {
        return TupleFactory::createVector(0, 1, 0);
    }
}
