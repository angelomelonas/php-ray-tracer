<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Shape;

final class ShapeFactory
{
    public static function createSphere(): Sphere
    {
        return new Sphere();
    }

    public static function createPlane(): Plane
    {
        return new Plane();
    }

    public static function createCube(): Cube
    {
        return new Cube();
    }

    public static function createCylinder(): Cylinder
    {
        return new Cylinder();
    }
}
