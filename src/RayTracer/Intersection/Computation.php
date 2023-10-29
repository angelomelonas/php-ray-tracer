<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Intersection;

use PhpRayTracer\RayTracer\Shape\Shape;
use PhpRayTracer\RayTracer\Tuple\Tuple;
use PhpRayTracer\RayTracer\Utility\Utility;
use function sqrt;

final class Computation
{
    private bool $inside;
    private Tuple $overPoint;
    private Tuple $underPoint;

    public function __construct(
        private readonly float $t,
        private readonly Shape $shape,
        private readonly Tuple $point,
        private readonly Tuple $eyeVector,
        private Tuple $normalVector,
        private readonly Tuple $reflectVector,
        private readonly float $n1,
        private readonly float $n2,
    ) {
        if ($this->normalVector->dot($this->eyeVector) < 0) {
            $this->inside = true;
            $this->normalVector = $this->normalVector->negate();
        } else {
            $this->inside = false;
        }

        $this->overPoint = $this->point->add($this->normalVector->multiply(Utility::PRECISION_TEST));
        $this->underPoint = $this->point->subtract($this->normalVector->multiply(Utility::PRECISION_TEST));
    }

    public function getT(): float
    {
        return $this->t;
    }

    public function getShape(): Shape
    {
        return $this->shape;
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

    public function getOverPoint(): Tuple
    {
        return $this->overPoint;
    }

    public function getReflectVector(): Tuple
    {
        return $this->reflectVector;
    }

    public function getUnderPoint(): Tuple
    {
        return $this->underPoint;
    }

    public function getN1(): float
    {
        return $this->n1;
    }

    public function getN2(): float
    {
        return $this->n2;
    }

    public function schlick(): float
    {
        $cosine = $this->getEyeVector()->dot($this->getNormalVector());

        if ($this->getN1() > $this->getN2()) {
            $n = $this->getN1() / $this->getN2();
            $sin2T = $n * $n * (1.0 - $cosine * $cosine);

            if ($sin2T > 1.0) {
                return 1.0;
            }

            $cosine = sqrt(1.0 - $sin2T);
        }

        $r0 = (($this->getN1() - $this->getN2()) / ($this->getN1() + $this->getN2())) ** 2;

        return $r0 + (1.0 - $r0) * (1.0 - $cosine) ** 5;
    }
}
