<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Intersection;

use PhpRayTracer\RayTracer\Ray\Ray;
use PhpRayTracer\RayTracer\Shape\Shape;
use function array_filter;
use function assert;
use function end;
use function in_array;

final readonly class Intersection
{
    public function __construct(private float $t, private Shape $shape)
    {
    }

    public function getT(): float
    {
        return $this->t;
    }

    public function getShape(): Shape
    {
        return $this->shape;
    }

    public function isHit(): bool
    {
        return $this->t >= 0;
    }

    public function prepareComputations(Ray $ray, Intersections $intersections): Computation
    {
        $n1 = 0.0;
        $n2 = 0.0;

        $containers = [];

        foreach ($intersections->getAll() as $intersection) {
            assert($intersection instanceof Intersection);

            if ($intersection === $this) {
                if (empty($containers)) {
                    $n1 = 1.0;
                } else {
                    $shape = end($containers);
                    assert($shape instanceof Shape);

                    $n1 = $shape->getMaterial()->getRefractiveIndex();
                }
            }

            if (in_array($intersection->getShape(), $containers)) {
                $containers = array_filter($containers, static fn ($container) => $container !== $intersection->getShape());
            } else {
                $containers[] = $intersection->getShape();
            }

            if ($intersection !== $this) {
                continue;
            }

            if (empty($containers)) {
                $n2 = 1.0;
            } else {
                $shape = end($containers);
                assert($shape instanceof Shape);

                $n2 = $shape->getMaterial()->getRefractiveIndex();
            }

            break;
        }

        $normalVector = $this->shape->normalAt($ray->position($this->t));
        $reflectVector = $ray->direction->reflect($normalVector);

        return new Computation(
            $this->t,
            $this->shape,
            $ray->position($this->t),
            $ray->direction->negate(),
            $normalVector,
            $reflectVector,
            $n1,
            $n2
        );
    }
}
