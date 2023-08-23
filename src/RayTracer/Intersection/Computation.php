<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Intersection;

use PhpRayTracer\RayTracer\Shape\Shape;
use PhpRayTracer\RayTracer\Tuple\Tuple;

final class Computation
{
    private bool $inside;

    public function __construct(
        private readonly float $t,
        private readonly Shape $object,
        private readonly Tuple $point,
        private readonly Tuple $eyeVector,
        private Tuple $normalVector,
    ) {
        if ($this->normalVector->dot($this->eyeVector) < 0) {
            $this->inside = true;
            $this->normalVector = $this->normalVector->negate();
        } else {
            $this->inside = false;
        }
    }

    public function getT(): float
    {
        return $this->t;
    }

    public function getObject(): Shape
    {
        return $this->object;
    }

    public function getPoint(): Tuple
    {
        return $this->point;
    }

    public function getEyeVector(): Tuple
    {
        return $this->eyeVector;
    }

    public function getNormalVector(): Tuple
    {
        return $this->normalVector;
    }

    public function isInside(): bool
    {
        return $this->inside;
    }
}
