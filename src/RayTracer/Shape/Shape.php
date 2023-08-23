<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Shape;

use PhpRayTracer\RayTracer\Intersection\Intersection;
use PhpRayTracer\RayTracer\Material\Material;
use PhpRayTracer\RayTracer\Matrix\Matrix;
use PhpRayTracer\RayTracer\Ray\Ray;
use PhpRayTracer\RayTracer\Tuple\Tuple;

interface Shape
{
    public function getTransform(): Matrix;

    public function getMaterial(): Material;

    /** @return Intersection[] */
    public function intersect(Ray $ray): array;

    public function normalAt(Tuple $point): Tuple;
}
