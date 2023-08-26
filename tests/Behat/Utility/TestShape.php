<?php
declare(strict_types=1);

namespace PhpRayTracer\Tests\Behat\Utility;

use PhpRayTracer\RayTracer\Ray\Ray;
use PhpRayTracer\RayTracer\Shape\Shape;
use PhpRayTracer\RayTracer\Tuple\Tuple;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;

final class TestShape extends Shape
{
    private Ray $savedRay;

    public function getSavedRay(): Ray
    {
        return $this->savedRay;
    }

    protected function localIntersect(Ray $ray): array
    {
        $this->savedRay = $ray;

        return [];
    }

    protected function localNormalAt(Tuple $point): Tuple
    {
        return TupleFactory::createVector($point->x, $point->y, $point->z);
    }
}
