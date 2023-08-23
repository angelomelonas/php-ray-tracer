<?php

use PhpRayTracer\RayTracer\Canvas\Canvas;
use PhpRayTracer\RayTracer\Projectiles\Environment;
use PhpRayTracer\RayTracer\Projectiles\Projectile;
use PhpRayTracer\RayTracer\Tuple\ColorFactory;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use PhpRayTracer\RayTracer\World\World;

require '../../vendor/autoload.php';

$projectile1 = new Projectile(
    TupleFactory::createPoint(0, 1, 0),
    TupleFactory::createVector(1, 1.8, 0)->normalize()->multiply(11)
);
$projectile2 = new Projectile(
    TupleFactory::createPoint(0, 1, 0),
    TupleFactory::createVector(1, 1.8, 0)->normalize()->multiply(11.5)
);
$projectile3 = new Projectile(
    TupleFactory::createPoint(0, 1, 0),
    TupleFactory::createVector(1, 1.8, 0)->normalize()->multiply(12)
);
$projectile4 = new Projectile(
    TupleFactory::createPoint(0, 1, 0),
    TupleFactory::createVector(1, 2, 0)->normalize()->multiply(13)
);

$environment = new Environment(
    TupleFactory::createVector(0, -0.1, 0),
    TupleFactory::createVector(-0.02, 0, 0)
);

$world = new World();

$width = 1200;
$height = 900;
$canvas = new Canvas($width, $height);

while (true) {
    if ($projectile1->position->y >= 0) {
        $canvas->writePixelCluster(
            intval($projectile1->position->x),
            intval($height - $projectile1->position->y),
            ColorFactory::create(1, 0, 0)
        );
    }
    if ($projectile2->position->y >= 0) {
        $canvas->writePixelCluster(
            intval($projectile2->position->x),
            intval($height - $projectile2->position->y),
            ColorFactory::create(0, 1, 0)
        );
    }
    if ($projectile3->position->y >= 0) {
        $canvas->writePixelCluster(
            intval($projectile3->position->x),
            intval($height - $projectile3->position->y),
            ColorFactory::create(0, 0, 1)
        );
    }
    if ($projectile4->position->y >= 0) {
        $canvas->writePixelCluster(
            intval($projectile4->position->x),
            intval($height - $projectile4->position->y),
            ColorFactory::create(1, 0, 1)
        );
    }

    $projectile1 = tick($projectile1, $environment);
    $projectile2 = tick($projectile2, $environment);
    $projectile3 = tick($projectile3, $environment);
    $projectile4 = tick($projectile4, $environment);

    if ($projectile1->position->y < 0 && $projectile2->position->y < 0 && $projectile3->position->y < 0 && $projectile4->position->y < 0) {
        break;
    }
}

$ppm = $canvas->canvasToPPM();
file_put_contents('projectiles.ppm', $ppm);

function tick(Projectile $projectile, Environment $environment): Projectile
{
    $newPosition = $projectile->position->add($projectile->velocity);
    $newVelocity = $projectile->velocity->add($environment->gravity->add($environment->wind));

    return new Projectile($newPosition, $newVelocity);
}