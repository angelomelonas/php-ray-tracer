<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Ray;

use PhpRayTracer\RayTracer\Matrix\Matrix;
use PhpRayTracer\RayTracer\Tuple\Tuple;

final class Ray
{
    public function __construct(public Tuple $origin, public Tuple $direction)
    {
    }

    public function position(float $t): Tuple
    {
        return $this->origin->add($this->direction->multiply($t));
    }

    public function transform(Matrix $matrix): Ray
    {
        return new Ray(
            $matrix->multiplyTuple($this->origin),
            $matrix->multiplyTuple($this->direction)
        );
    }
}
