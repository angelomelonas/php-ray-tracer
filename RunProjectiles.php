<?php

use PhpRayTracer\RayTracer\Projectiles\Environment;
use PhpRayTracer\RayTracer\Projectiles\Projectile;
use PhpRayTracer\RayTracer\Projectiles\World;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;

require 'vendor/autoload.php';


$projectile = new Projectile(
    TupleFactory::createPoint(0, 1, 0),
    TupleFactory::createVector(1, 1, 0)->normalize()
);

$environment = new Environment(
    TupleFactory::createVector(0, -0.1, 0),
    TupleFactory::createVector(-0.01, 0, 0)
);

$world = new World();

for ($i = 0; $i < 100; $i++) {

    $projectile = $world->tick($projectile, $environment);

    if($projectile->position->y < 0) {
        echo sprintf(PHP_EOL ."Projectile hit the ground at position %s after %s seconds." . PHP_EOL, $projectile->position->__toString(), $i);
        break;
    }

    echo sprintf( "Projectile position: %s" . PHP_EOL, $projectile->position->__toString());
}
