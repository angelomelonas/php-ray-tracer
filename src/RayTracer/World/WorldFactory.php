<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\World;

final class WorldFactory
{
    public static function create(): World
    {
        return new World();
    }
}
