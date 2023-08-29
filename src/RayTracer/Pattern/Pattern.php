<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Pattern;

use PhpRayTracer\RayTracer\Matrix\Matrix;
use PhpRayTracer\RayTracer\Matrix\MatrixFactory;
use PhpRayTracer\RayTracer\Shape\Shape;
use PhpRayTracer\RayTracer\Tuple\Color;
use PhpRayTracer\RayTracer\Tuple\Tuple;

abstract class Pattern
{
    protected Matrix $transformationMatrix;

    public function __construct()
    {
        $this->transformationMatrix = MatrixFactory::createIdentity();
    }

    public function getTransform(): Matrix
    {
        return $this->transformationMatrix;
    }

    public function setTransform(Matrix $matrix): void
    {
        $this->transformationMatrix = $matrix;
    }

    public function patternAtShape(Shape $shape, Tuple $worldPoint): Color
    {
        $objectPoint = $shape->getTransform()->inverse()->multiplyTuple($worldPoint);
        $patternPoint = $this->getTransform()->inverse()->multiplyTuple($objectPoint);

        return $this->patternAt($patternPoint);
    }

    abstract protected function patternAt(Tuple $point): Color;
}
