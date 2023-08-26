<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Shape;

use PhpRayTracer\RayTracer\Intersection\Intersection;
use PhpRayTracer\RayTracer\Intersection\IntersectionFactory;
use PhpRayTracer\RayTracer\Ray\Ray;
use PhpRayTracer\RayTracer\Tuple\Tuple;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use function asort;
use function pow;
use function sqrt;
use const SORT_NUMERIC;

final class Sphere extends Shape
{
    /** @return Intersection[] */
    protected function localIntersect(Ray $ray): array
    {
        $sphereToRay = $ray->origin->subtract($this->origin);

        $a = $ray->direction->dot($ray->direction);
        $b = 2 * $ray->direction->dot($sphereToRay);
        $c = $sphereToRay->dot($sphereToRay) - 1;

        $discriminant = pow($b, 2) - (4 * $a * $c);

        if ($discriminant < 0) {
            return [];
        }

        $t1 = (-$b - sqrt($discriminant)) / (2 * $a);
        $t2 = (-$b + sqrt($discriminant)) / (2 * $a);

        $intersections = [$t1, $t2];
        asort($intersections, SORT_NUMERIC);

        return [
            IntersectionFactory::create($intersections[0], $this),
            IntersectionFactory::create($intersections[1], $this),
        ];
    }

    protected function localNormalAt(Tuple $point): Tuple
    {
        return TupleFactory::createVector($point->x, $point->y, $point->z);
    }
}
