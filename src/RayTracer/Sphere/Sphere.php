<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Sphere;

use PhpRayTracer\RayTracer\Intersection\IntersectionFactory;
use PhpRayTracer\RayTracer\Intersection\Intersections;
use PhpRayTracer\RayTracer\Material\Material;
use PhpRayTracer\RayTracer\Material\MaterialFactory;
use PhpRayTracer\RayTracer\Matrix\Matrix;
use PhpRayTracer\RayTracer\Matrix\MatrixFactory;
use PhpRayTracer\RayTracer\Ray\Ray;
use PhpRayTracer\RayTracer\Tuple\Tuple;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use function asort;
use function pow;
use function sqrt;
use const SORT_NUMERIC;

final class Sphere
{
    public Tuple $origin;
    public Matrix $transformationMatrix;
    private Material $material;

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

    public function intersect(Ray $ray): Intersections
    {
        $ray = $ray->transform($this->getTransform()->inverse());
        $sphereToRay = $ray->origin->subtract($this->origin);

        $a = $ray->direction->dot($ray->direction);
        $b = 2 * $ray->direction->dot($sphereToRay);
        $c = $sphereToRay->dot($sphereToRay) - 1;

        $discriminant = pow($b, 2) - (4 * $a * $c);

        if ($discriminant < 0) {
            return new Intersections([]);
        }

        $t1 = (-$b - sqrt($discriminant)) / (2 * $a);
        $t2 = (-$b + sqrt($discriminant)) / (2 * $a);

        $intersections = [$t1, $t2];
        asort($intersections, SORT_NUMERIC);

        return new Intersections([
            IntersectionFactory::create($intersections[0], $this),
            IntersectionFactory::create($intersections[1], $this),
        ]);
    }

    public function normalAt(Tuple $point): Tuple
    {
        $objectPoint = $this->getTransform()->inverse()->multiplyTuple($point);
        $objectNormal = $objectPoint->subtract(TupleFactory::createPoint(0, 0, 0));
        $worldNormal = $this->getTransform()->inverse()->transpose()->multiplyTuple($objectNormal);
        $worldNormal->w = 0;

        return $worldNormal->normalize();
    }
}
