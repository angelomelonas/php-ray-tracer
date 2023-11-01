<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Shape;

use PhpRayTracer\RayTracer\Intersection\Intersection;
use PhpRayTracer\RayTracer\Ray\Ray;
use PhpRayTracer\RayTracer\Tuple\Tuple;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use PhpRayTracer\RayTracer\Utility\Utility;
use function sqrt;
use const INF;
use const PHP_FLOAT_EPSILON;

final class Cylinder extends Shape
{
    public function __construct(
        public float $minimum = -INF,
        public float $maximum = INF,
        public bool $closed = false,
    ) {
        parent::__construct();
    }

    protected function localIntersect(Ray $ray): array
    {
        $xs = [];
        $a = ($ray->direction->x ** 2) + ($ray->direction->z ** 2);

        if (! Utility::areFloatsEqual($a, 0.0)) {
            $b = (2 * $ray->origin->x * $ray->direction->x) + (2 * $ray->origin->z * $ray->direction->z);

            $c = ($ray->origin->x ** 2) + ($ray->origin->z ** 2) - 1;

            $disc = ($b ** 2) - (4 * $a * $c);

            if ($disc < 0) {
                return [];
            }

            $t0 = (-$b - sqrt($disc)) / (2 * $a);
            $t1 = (-$b + sqrt($disc)) / (2 * $a);

            if ($t0 > $t1) {
                $temp = $t0;
                $t0 = $t1;
                $t1 = $temp;
            }

            $y0 = $ray->origin->y + ($t0 * $ray->direction->y);
            if ($this->minimum < $y0 && $y0 < $this->maximum) {
                $xs[] = new Intersection($t0, $this);
            }

            $y1 = $ray->origin->y + ($t1 * $ray->direction->y);
            if ($this->minimum < $y1 && $y1 < $this->maximum) {
                $xs[] = new Intersection($t1, $this);
            }
        }

        return $this->intersectCaps($ray, $xs);
    }

    /**
     * @param Intersection[] $xs
     *
     * @return Intersection[]
     */
    private function intersectCaps(Ray $ray, array $xs): array
    {
        if (! $this->closed || Utility::areFloatsEqual($ray->direction->y, 0.0)) {
            return $xs;
        }

        $t = ($this->minimum - $ray->origin->y) / $ray->direction->y;
        if ($this->checkCap($ray, $t)) {
            $xs[] = new Intersection($t, $this);
        }

        $t = ($this->maximum - $ray->origin->y) / $ray->direction->y;
        if ($this->checkCap($ray, $t)) {
            $xs[] = new Intersection($t, $this);
        }

        return $xs;
    }

    private function checkCap(Ray $ray, float $t): bool
    {
        $x = $ray->origin->x + ($t * $ray->direction->x);
        $z = $ray->origin->z + ($t * $ray->direction->z);

        return ($x ** 2) + ($z ** 2) <= 1;
    }

    protected function localNormalAt(Tuple $point): Tuple
    {
        $dist = ($point->x ** 2) + ($point->z ** 2);

        if ($dist < 1 && $point->y >= $this->maximum - PHP_FLOAT_EPSILON) {
            return TupleFactory::createVector(0, 1, 0);
        }

        if ($dist < 1 && $point->y <= $this->minimum + PHP_FLOAT_EPSILON) {
            return TupleFactory::createVector(0, -1, 0);
        }

        return TupleFactory::createVector($point->x, 0, $point->z);
    }
}
