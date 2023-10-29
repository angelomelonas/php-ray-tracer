<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Shape;

use PhpRayTracer\RayTracer\Intersection\Intersection;
use PhpRayTracer\RayTracer\Ray\Ray;
use PhpRayTracer\RayTracer\Tuple\Tuple;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use function abs;
use function max;
use function min;
use const INF;
use const PHP_FLOAT_EPSILON;

final class Cube extends Shape
{
    protected function localIntersect(Ray $ray): array
    {
        [$xtMin, $xtMax] = self::checkAxis($ray->origin->x, $ray->direction->x);
        [$ytMin, $ytMax] = self::checkAxis($ray->origin->y, $ray->direction->y);
        [$ztMin, $ztMax] = self::checkAxis($ray->origin->z, $ray->direction->z);

        $tMin = max($xtMin, $ytMin, $ztMin);
        $tMax = min($xtMax, $ytMax, $ztMax);

        if ($tMin > $tMax) {
            return [];
        }

        return [
            new Intersection($tMin, $this),
            new Intersection($tMax, $this),
        ];
    }

    protected function localNormalAt(Tuple $point): Tuple
    {
        $maxc = max(abs($point->x), abs($point->y), abs($point->z));

        if ($maxc === abs($point->x)) {
            return TupleFactory::createVector($point->x, 0, 0);
        }

        if ($maxc === abs($point->y)) {
            return TupleFactory::createVector(0, $point->y, 0);
        }

        return TupleFactory::createVector(0, 0, $point->z);
    }

    /** @return float[] */
    private static function checkAxis(float $origin, float $direction): array
    {
        $tMinNumerator = -1 - $origin;
        $tMaxNumerator = 1 - $origin;

        if (abs($direction) >= PHP_FLOAT_EPSILON) {
            $tMin = $tMinNumerator / $direction;
            $tMax = $tMaxNumerator / $direction;
        } else {
            $tMin = $tMinNumerator * INF;
            $tMax = $tMaxNumerator * INF;
        }

        if ($tMin > $tMax) {
            $temp = $tMin;
            $tMin = $tMax;
            $tMax = $temp;
        }

        return [$tMin, $tMax];
    }
}
