<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Shape;

use PhpRayTracer\RayTracer\Intersection\Intersection;
use PhpRayTracer\RayTracer\Material\Material;
use PhpRayTracer\RayTracer\Material\MaterialFactory;
use PhpRayTracer\RayTracer\Matrix\Matrix;
use PhpRayTracer\RayTracer\Matrix\MatrixFactory;
use PhpRayTracer\RayTracer\Ray\Ray;
use PhpRayTracer\RayTracer\Tuple\Tuple;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;

abstract class Shape
{
    protected Tuple $origin;
    protected Matrix $transformationMatrix;
    protected Material $material;

    public function __construct()
    {
        $this->origin = TupleFactory::createPoint(0, 0, 0);
        $this->transformationMatrix = MatrixFactory::createIdentity(MatrixFactory::MATRIX_4X4);
        $this->material = MaterialFactory::create();
    }

    public function getTransform(): Matrix
    {
        return $this->transformationMatrix;
    }

    public function setTransform(Matrix $matrix): Matrix
    {
        return $this->transformationMatrix = $matrix;
    }

    public function getMaterial(): Material
    {
        return $this->material;
    }

    public function setMaterial(Material $material): Material
    {
        return $this->material = $material;
    }

    /** @return Intersection[] */
    public function intersect(Ray $ray): array
    {
        $ray = $ray->transform($this->getTransform()->inverse());

        return $this->localIntersect($ray);
    }

    public function normalAt(Tuple $point): Tuple
    {
        $localPoint = $this->getTransform()->inverse()->multiplyTuple($point);
        $localNormal = $this->localNormalAt($localPoint);
        $worldNormal = $this->getTransform()->inverse()->transpose()->multiplyTuple($localNormal);
        $worldNormal->w = 0;

        return $worldNormal->normalize();
    }

    /** @return Intersection[] */
    abstract protected function localIntersect(Ray $ray): array;

    abstract protected function localNormalAt(Tuple $point): Tuple;
}
