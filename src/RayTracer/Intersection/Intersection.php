<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Intersection;

use PhpRayTracer\RayTracer\Object\Sphere;

final readonly class Intersection
{
    public function __construct(private float $t, private Sphere $sphere)
    {
    }

    public function getT(): float
    {
        return $this->t;
    }

    public function getObject(): Sphere
    {
        return $this->sphere;
    }
}
