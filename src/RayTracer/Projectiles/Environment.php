<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Projectiles;

use PhpRayTracer\RayTracer\Tuple\Tuple;

final class Environment
{
    public function __construct(public Tuple $gravity, public Tuple $wind)
    {
    }
}
