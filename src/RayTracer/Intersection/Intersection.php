<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Intersection;

use PhpRayTracer\RayTracer\Object\Sphere;

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
        return $this->sphere;
        return $this->shape;
    }

    }
}
