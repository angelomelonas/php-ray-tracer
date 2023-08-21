<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Projectiles;

final class World
{
    public function __construct()
    {
    }

    public function tick(Projectile $projectile, Environment $environment): Projectile
    {
        $newPosition = $projectile->position->add($projectile->velocity);
        $newVelocity = $projectile->velocity->add($environment->gravity->add($environment->wind));

        return new Projectile($newPosition, $newVelocity);
    }
}
