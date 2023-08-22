<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Material;

final class MaterialFactory
{
    public static function create(): Material
    {
        return new Material();
    }
}
