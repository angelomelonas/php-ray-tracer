<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Projectiles;

use PhpRayTracer\RayTracer\Tuple\Tuple;

final class Projectile
{
    public function __construct(public Tuple $position, public Tuple $velocity)
    {
    }
}
