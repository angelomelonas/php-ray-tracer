<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Shape;

final class ShapeFactory
{
    public static function createSphere(): Sphere
    {
        return new Sphere();
    }
}
