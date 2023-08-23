<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Intersection;

use PhpRayTracer\RayTracer\Ray\Ray;
use PhpRayTracer\RayTracer\Shape\Shape;

final readonly class Intersection
{
    public function __construct(private float $t, private Shape $shape)
    {
    }

    public function getT(): float
    {
        return $this->t;
    }

    public function getObject(): Shape
    {
        return $this->shape;
    }

    public function prepareComputations(Ray $ray): Computation
    {
        return new Computation(
            $this->t,
            $this->shape,
            $ray->position($this->t),
            $ray->direction->negate(),
            $this->shape->normalAt($ray->position($this->t))
        );
    }
}
